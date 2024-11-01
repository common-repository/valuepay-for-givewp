<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Valuepay_Givewp_Form {

    // Register hooks
    public function __construct() {

        add_action( 'give_donation_form_before_email', array( $this, 'register_form_fields' ) );

        add_filter( 'give_donation_form_required_fields', array( $this, 'validate_form_fields' ) );
        add_action( 'give_insert_payment', array( $this, 'store_form_fields' ) );

        add_action( 'give_view_donation_details_billing_after', array( $this, 'add_donation_details' ) );

    }

    // Add form fields required by ValuePay - before email field
    public function register_form_fields( $form_id ) {

        $enabled_gateways = array_keys( give_get_enabled_payment_gateways( $form_id ) );

        if ( in_array( 'valuepay', $enabled_gateways ) ) {
            ob_start();
            include( VALUEPAY_GIVEWP_PATH . 'includes/views/form-fields.php' );
            echo ob_get_clean();
        }

    }

    // Validate form fields required by ValuePay
    public function validate_form_fields( $required_fields ) {

        $enabled_gateways = array_keys( give_get_enabled_payment_gateways() );

        if ( in_array( 'valuepay', $enabled_gateways ) ) {
            $required_fields[ 'valuepay_telephone' ] = array(
                'error_id'      => 'invalid_valuepay_telephone',
                'error_message' => __( 'Please fill in telephone.', 'valuepay-givewp' ),
            );
        }

        return $required_fields;

    }

    // Store form fields required by ValuePay
    public function store_form_fields( $payment_id ) {

        if ( isset( $_POST['valuepay_telephone'] ) ) {
            $telephone = give_clean( $_POST['valuepay_telephone'] );
            give_update_payment_meta( $payment_id, 'valuepay_telephone', $telephone );
        }

    }

    // Show extra data in payment details
    public function add_donation_details( $payment_id ) {

        if ( give_get_payment_gateway( $payment_id ) === 'valuepay' ) {
            ob_start();
            include( VALUEPAY_GIVEWP_PATH . 'includes/views/donation-details.php' );
            echo ob_get_clean();
        }

    }

}
new Valuepay_Givewp_Form();
