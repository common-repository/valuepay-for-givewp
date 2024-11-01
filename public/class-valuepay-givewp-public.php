<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Valuepay_Givewp_Public {

    // Register hooks
    public function __construct() {

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

    }

    public function enqueue_scripts() {

        wp_enqueue_script( 'valuepay-givewp', VALUEPAY_GIVEWP_URL . 'assets/js/main.js', array( 'jquery', 'give' ), VALUEPAY_GIVEWP_VERSION, true );

    }

}
new Valuepay_Givewp_Public();
