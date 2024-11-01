<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Valuepay_Givewp_API extends Valuepay_Givewp_Client {

    // Initialize API
    public function __construct( $username = null, $app_key = null, $app_secret = null ) {

        $this->username   = $username ?: give_get_option( 'valuepay_givewp_username' );
        $this->app_key    = $app_key ?: give_get_option( 'valuepay_givewp_app_key' );
        $this->app_secret = $app_secret ?: give_get_option( 'valuepay_givewp_app_secret' );

    }

    // Query bank list
    public function get_banks( array $params ) {
        return $this->post( 'querybanklist', $params );
    }

    // Create a bill
    public function create_bill( array $params ) {
        return $this->post( 'createbill', $params );
    }

    // Set enrolment data
    public function set_enrol_data( array $params ) {
        return $this->post( 'setenroldata', $params );
    }

}
