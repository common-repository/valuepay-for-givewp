<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Valuepay_Givewp {

    // Load dependencies
    public function __construct() {

        // Functions
        require_once( VALUEPAY_GIVEWP_PATH . 'includes/functions.php' );

        // API
        require_once( VALUEPAY_GIVEWP_PATH . 'includes/abstracts/abstract-valuepay-givewp-client.php' );
        require_once( VALUEPAY_GIVEWP_PATH . 'includes/class-valuepay-givewp-api.php' );

        // Admin
        require_once( VALUEPAY_GIVEWP_PATH . 'admin/class-valuepay-givewp-admin.php' );
        require_once( VALUEPAY_GIVEWP_PATH . 'admin/class-valuepay-givewp-settings.php' );
        require_once( VALUEPAY_GIVEWP_PATH . 'admin/class-valuepay-givewp-donation-settings.php' );

        // Form fields
        require_once( VALUEPAY_GIVEWP_PATH . 'includes/class-valuepay-givewp-form.php' );

        // Initialize payment gateway
        require_once( VALUEPAY_GIVEWP_PATH . 'includes/class-valuepay-givewp-gateway.php' );

        // Frontend
        require_once( VALUEPAY_GIVEWP_PATH . 'public/class-valuepay-givewp-public.php' );

    }

}
new Valuepay_Givewp();
