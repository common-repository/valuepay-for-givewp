<?php
/**
 * Plugin Name:       ValuePay for GiveWP
 * Description:       Accept payment on GiveWP using ValuePay.
 * Version:           1.0.6
 * Requires at least: 4.6
 * Requires PHP:      7.0
 * Author:            Valuefy Solutions Sdn Bhd
 * Author URI:        https://valuepay.my/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( class_exists( 'Valuepay_Givewp' ) ) return;

define( 'VALUEPAY_GIVEWP_FILE', __FILE__ );
define( 'VALUEPAY_GIVEWP_URL', plugin_dir_url( VALUEPAY_GIVEWP_FILE ) );
define( 'VALUEPAY_GIVEWP_PATH', plugin_dir_path( VALUEPAY_GIVEWP_FILE ) );
define( 'VALUEPAY_GIVEWP_BASENAME', plugin_basename( VALUEPAY_GIVEWP_FILE ) );
define( 'VALUEPAY_GIVEWP_VERSION', '1.0.6' );

// Plugin core class
require( VALUEPAY_GIVEWP_PATH . 'includes/class-valuepay-givewp.php' );
