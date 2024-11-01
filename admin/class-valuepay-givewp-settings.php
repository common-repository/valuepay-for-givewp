<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Valuepay_Givewp_Settings {

    // Register hooks
    public function __construct() {

        add_filter( 'give_get_sections_gateways', array( $this, 'register_gateway_sections' ) );
        add_filter( 'give_get_settings_gateways', array( $this, 'register_gateway_settings' ) );

    }

    // Register section
    public function register_gateway_sections( $sections ) {
        $sections['valuepay'] = __( 'ValuePay', 'valuepay-givewp' );
        return $sections;
    }

    // Register settings field
    public function register_gateway_settings( $settings ) {

        if ( give_get_current_setting_section() !== 'valuepay' ) {
            return $settings;
        }

        return array(
            array(
                'id'   => 'give_title_valuepay',
                'type' => 'title',
            ),
            array(
                'name' => __( 'Merchant Username', 'valuepay-givewp' ),
                'desc' => __( 'Merchant username can be obtained from ValuePay merchant dashboard in Business Profile page.', 'valuepay-givewp' ),
                'id'   => 'valuepay_givewp_username',
                'type' => 'text',
            ),
            array(
                'name' => __( 'Application Key', 'valuepay-givewp' ),
                'desc' => __( 'Application key can be obtained from ValuePay merchant dashboard in Business Profile page.', 'valuepay-givewp' ),
                'id'   => 'valuepay_givewp_app_key',
                'type' => 'text',
            ),
            array(
                'name' => __( 'Application Secret', 'valuepay-givewp' ),
                'desc' => __( 'Application secret can be obtained from ValuePay merchant dashboard in Business Profile page.', 'valuepay-givewp' ),
                'id'   => 'valuepay_givewp_app_secret',
                'type' => 'text',
            ),
            array(
                'name' => __( 'Debug Log', 'valuepay-givewp' ),
                'desc' => __( 'Log ValuePay events, eg: IPN requests. Logs can be viewed on Donations > Tools > Logs.', 'valuepay-givewp' ),
                'id'   => 'valuepay_givewp_debug',
                'type' => 'checkbox',
            ),
            array(
                'id'   => 'give_title_valuepay',
                'type' => 'sectionend',
            ),
        );

    }

}
new Valuepay_Givewp_Settings();
