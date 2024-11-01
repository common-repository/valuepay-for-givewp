<?php
if ( !defined( 'ABSPATH' ) ) exit;

// Display notice
function valuepay_givewp_notice( $message, $type = 'success' ) {

    $plugin = esc_html__( 'ValuePay for GiveWP', 'valuepay-givewp' );

    printf( '<div class="notice notice-%1$s"><p><strong>%2$s:</strong> %3$s</p></div>', esc_attr( $type ), $plugin, $message );

}

// Log a message in GiveWP logs
// Log type = sale, gateway_error, api_request, update, spam
function valuepay_givewp_logger( $message, $type = 'api_request' ) {

    if ( !function_exists( 'give_record_log' ) ) {
        return false;
    }

    if ( !function_exists( 'give_is_setting_enabled' ) ) {
        return false;
    }

    if ( !function_exists( 'give_get_option' ) ) {
        return false;
    }

    $is_debug_enabled = give_is_setting_enabled( give_get_option( 'valuepay_givewp_debug' ) );

    // For api request log message, check if debug is enabled
    if ( $type === 'api_request' && !$is_debug_enabled ) {
        return false;
    }

    return give_record_log( __( 'ValuePay', 'valuepay-givewp' ), $message, 0, $type );

}

// List of identity types accepted by ValuePay
function valuepay_givewp_get_identity_types() {

    return array(
        1 => __( 'New IC No.', 'valuepay-givewp' ),
        2 => __( 'Old IC No.', 'valuepay-givewp' ),
        3 => __( 'Passport No.', 'valuepay-givewp' ),
        4 => __( 'Business Reg. No.', 'valuepay-givewp' ),
        5 => __( 'Others', 'valuepay-givewp' ),
    );

}

// Get readable identity type
function valuepay_givewp_get_identity_type( $key ) {
    $types = valuepay_givewp_get_identity_types();
    return isset( $types[ $key ] ) ? $types[ $key ] : false;
}

// Format telephone number
function valuepay_givewp_format_telephone( $telephone ) {

    // Get numbers only
    $telephone = preg_replace( '/[^0-9]/', '', $telephone );

    // Add country code in the front of phone number if the phone number starts with zero (0)
    if ( strpos( $telephone, '0' ) === 0 ) {
        $telephone = '+6' . $telephone;
    }

    // Add + symbol in the front of phone number if the phone number has no + symbol
    if ( strpos( $telephone, '+' ) !== 0 ) {
        $telephone = '+' . $telephone;
    }

    return $telephone;

}
