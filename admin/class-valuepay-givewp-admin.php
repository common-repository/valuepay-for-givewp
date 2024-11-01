<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Valuepay_Givewp_Admin {

    // Register hooks
    public function __construct() {

        add_action( 'plugin_action_links_' . VALUEPAY_GIVEWP_BASENAME, array( $this, 'register_settings_link' ) );
        add_action( 'admin_notices', array( $this, 'givewp_notice' ) );
        add_action( 'admin_notices', array( $this, 'currency_not_supported_notice' ) );

    }

    // Register plugin settings link
    public function register_settings_link( $links ) {

        $url = admin_url( 'edit.php?post_type=give_forms&page=give-settings&tab=gateways&section=valuepay' );
        $label = esc_html__( 'Settings', 'valuepay' );

        $settings_link = '<a href="' . esc_url( $url ) . '">' . $label . '</a>';
        array_unshift( $links, $settings_link );

        return $links;

    }

    // Check if GiveWP is installed and activated
    private function is_givewp_activated() {
        return in_array( 'give/give.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
    }

    // Show notice if GiveWP not installed
    public function givewp_notice() {

        if ( !$this->is_givewp_activated() ) {
            valuepay_givewp_notice( __( 'GiveWP needs to be installed and activated.', 'valuepay-givewp' ), 'error' );
        }

    }

    // Show notice if currency selected is not supported by ValuePay
    public function currency_not_supported_notice() {

        if ( !function_exists( 'give_get_currency' ) ) {
            return false;
        }

        if ( give_get_currency() !== 'MYR' ) {
            valuepay_givewp_notice( sprintf( __( 'Currency not supported by ValuePay. <a href="%s">Change currency</a>', 'valuepay-givewp' ), admin_url( 'edit.php?post_type=give_forms&page=give-settings&tab=general&section=currency-settings' ) ), 'error' );
        }

    }

}
new Valuepay_Givewp_Admin();
