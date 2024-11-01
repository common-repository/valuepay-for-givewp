<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Valuepay_Givewp_Gateway {

    private $id = 'valuepay';

    // Register hooks
    public function __construct() {

        add_filter( 'give_payment_gateways', array( $this, 'register_gateway' ) );
        add_action( 'give_' . $this->id . '_cc_form', array( $this, 'add_gateway_description' ) );

        add_action( 'give_gateway_' . $this->id, array( $this, 'process_donation' ) );
        add_action( 'init', array( $this, 'handle_ipn' ) );

    }

    // Register payment method
    public function register_gateway( $gateways ) {

        $gateways[ $this->id ] = array(
            'admin_label'    => __( 'ValuePay', 'valuepay-givewp' ),
            'checkout_label' => __( 'ValuePay', 'valuepay-givewp' ),
        );

        return $gateways;

    }

    // Replace credit card fields with gateway description in donation form
    public function add_gateway_description( $form_id ) {

        printf(
            '
            <fieldset class="no-fields">
                <img src="' . VALUEPAY_GIVEWP_URL . 'assets/images/pay-with-valuepay.png" alt="Pay with ValuePay" style="margin: auto; display: block;">
                <p style="text-align: center;"><b>%1$s</b></p>
                <p style="text-align: center;">
                    <b>%2$s</b> %3$s
                </p>
            </fieldset>
        ',
            __( 'Make your donation quickly and securely with ValuePay', 'valuepay-givewp' ),
            __( 'How it works:', 'valuepay-givewp' ),
            __( 'You will be redirected to ValuePay! to make payment. You will then be brought back to this page to view your receipt.', 'valuepay-givewp' )
        );

        $this->display_bank_input( $form_id );
        $this->display_payment_type_input( $form_id );

    }

    // Add bank field after email field in donation form
    public function display_bank_input( $form_id ) {

        // Show bank input in donation form only if recurring payment option is enabled
        if ( !give_get_meta( $form_id, '_valuepay_givewp_mandate_id', true ) ) {
            return false;
        }

        $banks = $this->get_banks();

        ob_start();
        include( VALUEPAY_GIVEWP_PATH . 'includes/views/bank-input.php' );
        echo ob_get_clean();

    }

    // Get list of banks from ValuePay
    private function get_banks() {

        $banks = get_transient( 'valuepay_givewp_banks' );

        if ( !$banks || !is_array( $banks ) ) {
            $banks = array();

            try {
                $valuepay = new Valuepay_Givewp_API();

                $banks_query = $valuepay->get_banks( array(
                    'username' => $valuepay->username,
                    'reqhash'  => md5( $valuepay->app_key . $valuepay->username ),
                ) );

                if ( isset( $banks_query[1]['bank_list'] ) && !empty( $banks_query[1]['bank_list'] ) ) {
                    $banks = $banks_query[1]['bank_list'];

                    // Set transient, so that we can retrieve using transient
                    // instead of retrieve through API request to ValuePay.
                    set_transient( 'valuepay_givewp_banks', $banks, DAY_IN_SECONDS );
                }
            } catch ( Exception $e ) {}
        }

        return $banks;

    }

    // Add payment type field after email field in donation form
    public function display_payment_type_input( $form_id ) {

        ob_start();
        include( VALUEPAY_GIVEWP_PATH . 'includes/views/payment-type-input.php' );
        echo ob_get_clean();

    }

    // Process donation payment
    public function process_donation( $posted_data ) {

        // Make sure we don't have any left over errors present
        give_clear_errors();

        $errors = give_get_errors();

        // Only process the submission if no errors
        if ( !$errors ) {

            $form_id         = intval( $posted_data['post_data']['give-form-id'] );
            $price_id        = !empty( $posted_data['post_data']['give-price-id'] ) ? $posted_data['post_data']['give-price-id'] : 0;
            $donation_amount = !empty( $posted_data['price'] ) ? $posted_data['price'] : 0;

            $donation_data = array(
                'price'           => $donation_amount,
                'give_form_title' => $posted_data['post_data']['give-form-title'],
                'give_form_id'    => $form_id,
                'give_price_id'   => $price_id,
                'date'            => $posted_data['date'],
                'user_email'      => $posted_data['user_email'],
                'purchase_key'    => $posted_data['purchase_key'],
                'currency'        => give_get_currency( $form_id ),
                'user_info'       => $posted_data['user_info'],
                'status'          => 'pending',
                'gateway'         => $this->id,
            );

            $payment_id = give_insert_payment( $donation_data );

            if ( !$payment_id ) {
                // Record Gateway Error as Pending Donation in Give is not created
                give_record_gateway_error(
                    __( 'ValuePay Error', 'valuepay-givewp' ),
                    sprintf(
                        __( 'Unable to create a pending donation with Give.', 'valuepay-givewp' )
                    )
                );

                // Send user back to checkout
                give_send_back_to_checkout( '?payment-mode=' . $this->id );
                return;
            }

            try {
                $redirect_url = $this->get_redirect_url( $payment_id, $posted_data );
                
                if ( $redirect_url ) {
                    wp_redirect( $redirect_url );
                    exit;
                }
            } catch ( Exception $e ) {
                $error_message = $e->getMessage();

                if ( $error_message == 'Missing mandatory field for API' ) {
                    $error_message = __( 'Identity information is required for recurring payment.', 'valuepay-givewp' );
                }

                give_set_error( 'valuepay_payment_error', $error_message );

                // Send user back to checkout
                give_send_back_to_checkout( '?payment-mode=' . $this->id );
                return;
            }

        } else {
            // Send user back to checkout
            give_send_back_to_checkout( '?payment-mode=' . $this->id );
        }

    }

    // Get the URL to redirect the user for payment
    private function get_redirect_url( $payment_id, $posted_data ) {

        $payment_type = 'single';

        if ( isset( $posted_data['post_data']['valuepay_payment_type'] ) && !empty( $posted_data['post_data']['valuepay_payment_type'] ) ) {
            $payment_type = $posted_data['post_data']['valuepay_payment_type'];
        }

        if ( $payment_type === 'recurring' ) {
            return $this->get_enrolment_url( $payment_id, $posted_data );
        } else {
            return $this->get_bill_url( $payment_id, $posted_data );
        }

    }

    // Create an enrolment in ValuePay (for recurring payment)
    private function get_enrolment_url( $payment_id, $posted_data ) {

        // Total amount
        $donation_amount = !empty( $posted_data['price'] ) ? number_format( $posted_data['price'], 2 ) : 0;

        // Full name
        $first_name      = !empty( $posted_data['user_info']['first_name'] ) ? $posted_data['user_info']['first_name'] : null;
        $last_name       = !empty( $posted_data['user_info']['last_name'] ) ? $posted_data['user_info']['last_name'] : null;
        $full_name       = implode( ' ', array( $first_name, $last_name ) );

        // Other user info
        $email           = !empty( $posted_data['user_email'] ) ? $posted_data['user_email'] : null;
        $telephone       = isset( $posted_data['post_data']['valuepay_telephone'] ) ? valuepay_givewp_format_telephone( $posted_data['post_data']['valuepay_telephone'] ) : null;

        // Identity information and bank code
        $identity_type   = isset( $posted_data['post_data']['valuepay_identity_type'] ) ? $posted_data['post_data']['valuepay_identity_type'] : null;
        $identity_value  = isset( $posted_data['post_data']['valuepay_identity_value'] ) ? $posted_data['post_data']['valuepay_identity_value'] : null;
        $bank            = isset( $posted_data['post_data']['valuepay_bank'] ) ? $posted_data['post_data']['valuepay_bank'] : null;

        if ( !$full_name ) {
            throw new Exception( __( 'Name is required', 'valuepay-givewp' ) );
        }

        if ( !$email ) {
            throw new Exception( __( 'Email is required', 'valuepay-givewp' ) );
        }

        if ( !$telephone ) {
            throw new Exception( __( 'Telephone is required', 'valuepay-givewp' ) );
        }

        if ( !$identity_type || !$identity_value ) {
            throw new Exception( __( 'Identity information is required for recurring payment', 'valuepay-givewp' ) );
        }

        if ( !$bank ) {
            throw new Exception( __( 'Bank is required for recurring payment', 'valuepay-givewp' ) );
        }

        // Donation settings
        $form_id    = give_get_payment_form_id( $payment_id );
        $mandate_id = give_get_meta( $form_id, '_valuepay_givewp_mandate_id', true );

        $valuepay = new Valuepay_Givewp_API();

        $params = array(
            'username'        => $valuepay->username,
            'sub_fullname'    => $full_name,
            'sub_ident_type'  => $identity_type,
            'sub_ident_value' => $identity_value,
            'sub_telephone'   => $telephone,
            'sub_email'       => $email,
            'sub_mandate_id'  => $mandate_id,
            'sub_bank_id'     => $bank,
            'sub_amount'      => $donation_amount,
        );

        $hash_data = array(
            $valuepay->app_key,
            $valuepay->username,
            $params['sub_fullname'],
            $params['sub_ident_type'],
            $params['sub_telephone'],
            $params['sub_email'],
            $params['sub_mandate_id'],
            $params['sub_bank_id'],
            $params['sub_amount'],
        );

        $params['reqhash'] = md5( implode( '', array_values( $hash_data ) ) );

        list( $code, $response ) = $valuepay->set_enrol_data( $params );

        if ( isset( $response['method'] ) && isset( $response['method'] ) == 'GET' && isset( $response['action'] ) ) {

            give_update_payment_status( $payment_id, 'preapproval' );

            return $response['action'];
        }

        return false;

    }

    // Create a bill in ValuePay (for one time payment)
    private function get_bill_url( $payment_id, $posted_data ) {

        // Total amount
        $donation_amount = !empty( $posted_data['price'] ) ? $posted_data['price'] : 0;

        // Full name
        $first_name      = !empty( $posted_data['user_info']['first_name'] ) ? $posted_data['user_info']['first_name'] : null;
        $last_name       = !empty( $posted_data['user_info']['last_name'] ) ? $posted_data['user_info']['last_name'] : null;
        $full_name       = implode( ' ', array( $first_name, $last_name ) );

        // Other user info
        $email           = !empty( $posted_data['user_email'] ) ? $posted_data['user_email'] : null;
        $telephone       = isset( $posted_data['post_data']['valuepay_telephone'] ) ? valuepay_givewp_format_telephone( $posted_data['post_data']['valuepay_telephone'] ) : null;

        if ( !$full_name ) {
            throw new Exception( __( 'Name is required', 'valuepay-givewp' ) );
        }

        if ( !$email ) {
            throw new Exception( __( 'Email is required', 'valuepay-givewp' ) );
        }

        if ( !$telephone ) {
            throw new Exception( __( 'Telephone is required', 'valuepay-givewp' ) );
        }

        /////////////////////////////////////////////////////////////////////////

        $buyer_data = array(
            'buyer_name'    => $full_name,
            'mobile_number' => $telephone,
            'email'         => $email,
        );

        // Callback and redirect URL
        $listener_url = add_query_arg( 'give-listener', $this->id, home_url( 'index.php' ) );
        $return_url   = give_get_success_page_uri();

        // Donation settings
        $form_id       = give_get_payment_form_id( $payment_id );
        $collection_id = give_get_meta( $form_id, '_valuepay_givewp_collection_id', true );

        $valuepay = new Valuepay_Givewp_API();

        $params = array(
            'username'          => $valuepay->username,
            'orderno'           => $payment_id,
            'bill_amount'       => $donation_amount,
            'collection_id'     => $collection_id,
            'buyer_data'        => $buyer_data,
            'bill_frontend_url' => $return_url,
            'bill_backend_url'  => $listener_url,
        );

        $hash_data = array(
            $valuepay->app_key,
            $valuepay->username,
            $params['bill_amount'],
            $params['collection_id'],
            $params['orderno'],
        );

        $params['reqhash'] = md5( implode( '', array_values( $hash_data ) ) );

        list( $code, $response ) = $valuepay->create_bill( $params );

        if ( isset( $response['bill_url'] ) ) {
            return $response['bill_url'];
        }

        return false;

    }

    // Listens for a ValuePay IPN requests and then sends to the processing function
    public function handle_ipn() {

        if ( !( isset( $_GET['give-listener'] ) && $_GET['give-listener'] === $this->id ) ) {
            return false;
        }

        $valuepay = new Valuepay_Givewp_API();
        $response = $valuepay->get_ipn_response();

        if ( !$response ) {
            valuepay_givewp_logger(
                sprintf(
                    __( 'Invalid IPN response: %s', 'valuepay-givewp' ),
                    wp_json_encode( $response )
                ),
                'gateway_error'
            );

            return;
        }

        valuepay_givewp_logger( sprintf(
            __( 'IPN response: %s', 'valuepay-givewp' ),
            wp_json_encode( $response )
        ) );

        ////////////////////////////////////////////////////////

        $payment_id = (int) $response['orderno'];
        $donation = give_get_payment_by( 'id', $payment_id );

        if ( !$donation ) {
            valuepay_givewp_logger(
                sprintf(
                    __( 'Donation not found. Payment ID: %s', 'valuepay-givewp' ),
                    $payment_id
                ),
                'gateway_error'
            );

            return false;
        }

        if ( !function_exists( 'give_is_donation_completed' ) ) {
            return false;
        }

        // Check if the payment already marked as paid
        if ( give_is_donation_completed( $donation->ID ) ) {
            return false;
        }

        ////////////////////////////////////////////////////////

        try {
            valuepay_givewp_logger( sprintf(
                __( 'Verifying hash for donation #%d', 'valuepay-givewp' ),
                $donation->ID
            ) );

            $valuepay->validate_ipn_response( $response );

        } catch ( Exception $e ) {
            valuepay_givewp_logger( $e->getMessage() );
            return;
        } finally {
            valuepay_givewp_logger( sprintf(
                __( 'Verified hash for donation #%d', 'valuepay-givewp' ),
                $donation->ID
            ) );
        }

        // Check if the payment status is paid
        if ( $response['bill_status'] === 'paid' ) {
            $this->handle_success_payment( $payment_id, $response );
        } else {
            $this->handle_failed_payment( $payment_id, $response );
        }

    }

    // Handle success payment
    private function handle_success_payment( $payment_id, $response ) {

        if ( give_get_payment_status( $payment_id ) === 'publish' ) {
            return;
        }

        give_update_payment_status( $payment_id, 'publish' );
        give_set_payment_transaction_id( $payment_id, $response['bill_id'] );

        give_insert_payment_note( $payment_id, sprintf( __( 'Payment success! Payment ID: %s', 'valuepay-givewp' ), $response['bill_id'] ) );

        valuepay_givewp_logger( sprintf(
            __( 'Donation payment #%d has been marked as Paid', 'valuepay-givewp' ),
            $payment_id
        ) );

    }

    // Handle failed payment
    private function handle_failed_payment( $payment_id, $response ) {

        give_insert_payment_note( $payment_id, sprintf( __( 'Payment failed! Payment ID: %1$s<br>Payment Type: %2$s', 'valuepay-givewp' ), $response['bill_id'] ) );

        valuepay_givewp_logger( sprintf(
            __( 'Donation payment #%d is failed', 'valuepay-givewp' ),
            $payment_id
        ) );

    }

}
new Valuepay_Givewp_Gateway();
