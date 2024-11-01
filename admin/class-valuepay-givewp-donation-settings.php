<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Valuepay_Givewp_Donation_Settings {

    private $meta_key = '_valuepay_givewp_';

    // Register hooks
    public function __construct() {

        add_filter( 'give_metabox_form_data_settings', array( $this, 'register_settings' ), 10, 2 );

    }

    // Register settings field
    public function register_settings( $settings, $post_id ) {

        $settings['valuepay'] = array(
            'id'        => 'valuepay',
            'title'     => __( 'ValuePay', 'valuepay-givewp' ),
            'icon-html' => '<img src="' . VALUEPAY_GIVEWP_URL . 'assets/images/valuepay-icon.png" alt="ValuePay">',
            'fields'    => array(
                array(
                    'name'        => __( 'Collection ID', 'valuepay-givewp' ),
                    'description' => __( 'Collection ID can be obtained from ValuePay merchant dashboard under FPX Payment menu, in My Collection List page. Leave blank to disable one time payment.', 'valuepay-givewp' ),
                    'id'          => $this->meta_key . 'collection_id',
                    'type'        => 'text_medium',
                    'attributes'  => array(
                        'placeholder' => __('eg: ABCD1234', 'give'),
                    ),
                ),
                array(
                    'name'        => __( 'Mandate ID', 'valuepay-givewp' ),
                    'description' => __( 'Mandate ID can be obtained from ValuePay merchant dashboard under E-Mandate Collection menu, in My Mandate List page. Leave blank to disable recurring payment.', 'valuepay-givewp' ),
                    'id'          => $this->meta_key . 'mandate_id',
                    'type'        => 'text_medium',
                    'attributes'  => array(
                        'placeholder' => __('eg: ABCD1234', 'give'),
                    ),
                ),
                array(
                    'name'        => __( 'Frequency Type', 'valuepay-givewp' ),
                    'description' => __( 'Select frequency type for the mandate above (if enabled).', 'valuepay-givewp' ),
                    'id'          => $this->meta_key . 'frequency_type',
                    'type'        => 'select',
                    'options'     => array(
                        'weekly'  => __( 'Weekly', 'valuepay-givewp' ),
                        'monthly' => __( 'Monthly', 'valuepay-givewp' ),
                    ),
                    'default'     => 'monthly',
                ),
            ),
        );

        return $settings;

    }

}
new Valuepay_Givewp_Donation_Settings();
