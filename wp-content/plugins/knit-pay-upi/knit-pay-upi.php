<?php
/**
 * Plugin Name: Knit Pay - UPI
 * Plugin URI: https://www.knitpay.org
 * Description: Add support of UPI Business apps in Knit Pay
 *
 * Version: 1.4.0.1
 * Requires at least: 6.5
 * Requires PHP: 8.0
 * Requires Plugins: knit-pay
 *
 * Author: KnitPay
 * Author URI: https://profiles.wordpress.org/knitpay/#content-plugins
 *
 * Text Domain: knit-pay-upi
 * Domain Path: /languages/
 *
 * License: GPL-3.0-or-later
 *
 * @author    KnitPay
 * @license   GPL-3.0-or-later
 * @package   KnitPay
 * @copyright 2020-2024 Knit Pay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! defined( 'KNIT_PAY_UPI' ) ) {
	define( 'KNIT_PAY_UPI', true );
}
define( 'KNIT_PAY_UPI_RAPIDAPI_BASE_URL', 'https://knit-pay-upi.p.rapidapi.com/' );
define( 'KNIT_PAY_UPI_RAPIDAPI_HOST', 'knit-pay-upi.p.rapidapi.com' );
define( 'KNIT_PAY_UPI_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Autoload.
 */
require_once __DIR__ . '/vendor/autoload_packages.php';

add_action( 'plugins_loaded', 'knit_pay_upi_dependency_check', -10 );
function knit_pay_upi_dependency_check() {
	if ( ! defined( 'KNITPAY_VERSION' ) || version_compare( KNITPAY_VERSION, '8.85.15.1', '<' ) ) {
		return;
	}
	
	new KnitPayUPI_Setup();
}

spl_autoload_register( 'knit_pay_upi_dependency_autoload' );
function knit_pay_upi_dependency_autoload( $class ) {
	if ( preg_match( '/^KnitPayUPI\\\\(.+)?([^\\\\]+)$/U', ltrim( $class, '\\' ), $match ) ) {
		$extension_dir = KNIT_PAY_UPI_DIR . strtolower( str_replace( '\\', DIRECTORY_SEPARATOR, preg_replace( '/([a-z])([A-Z])/', '$1-$2', $match[1] ) ) );
		if ( ! is_dir( $extension_dir ) ) {
			$extension_dir = KNIT_PAY_UPI_DIR . strtolower( str_replace( '\\', DIRECTORY_SEPARATOR, preg_replace( '/([a-z])([A-Z])/', '$1$2', $match[1] ) ) );
		}
		
		$file = $extension_dir
		. 'src' . DIRECTORY_SEPARATOR
		. $match[2]
		. '.php';
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}
}

class KnitPayUPI_Setup {
	public function __construct() {
		add_filter( 'pronamic_pay_gateways', [ $this, 'update_gateways' ] );
	}
	
	public function update_gateways( $gateways ) {
		// BharatPe QR.
		$gateways[] = new \KnitPayUPI\Gateways\UpiQR\BharatPe\Integration();

		// Paytm for Business QR.
		$gateways[] = new \KnitPayUPI\Gateways\UpiQR\Paytm\Integration();

		// PhonePe Business QR.
		$gateways[] = new \KnitPayUPI\Gateways\UpiQR\PhonePe\Integration();

		// HDFC SmartHub Vyapar.
		if ( version_compare( KNITPAY_VERSION, '8.87.8.0', '>' ) ) {
			$gateways[] = new \KnitPayUPI\Gateways\UpiQR\HdfcSmartHubVyapar\Integration();
		}

		// Return gateways.
		return $gateways;
	}
}
