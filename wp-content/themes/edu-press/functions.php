<?php
/**
 * Theme functions and definitions.
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 */

define( 'THIM_DIR', trailingslashit( get_template_directory() ) );
define( 'THIM_URI', trailingslashit( get_template_directory_uri() ) );
 define( 'THIM_THEME_VERSION', '1.0.2' );

require THIM_DIR . 'inc/libs/down_image_size.php';

/**
 * Custom template tags for this theme.
 */
require_once( THIM_DIR . 'inc/template-tags.php' );

/**
 * Custom functions that act independently of the theme templates.
 */
require_once( THIM_DIR . 'inc/extras.php' );

if ( class_exists( 'WooCommerce' ) ) {
	require THIM_DIR . 'woocommerce/woocommerce.php';
}
/**
 * Load Jetpack compatibility file.
 */
//require_once( THIM_DIR . 'inc/jetpack.php' );
//logo
require_once THIM_DIR . 'templates/header/logo.php';
/**
 * Custom wrapper layout for theme
 */
require_once( THIM_DIR . 'inc/wrapper-layout.php' );
/**
 * Custom functions
 */
require_once( THIM_DIR . 'inc/custom-functions.php' );

// register widget elementor kit
require THIM_DIR . 'inc/register-widget-ekit.php';

include_once THIM_DIR . '/inc/variables-css.php';
require_once( THIM_DIR . 'inc/init.php' );
require_once( THIM_DIR . 'inc/metabox.php' );

if ( ! function_exists( 'thim_enable_upload_svg' ) ) {
	function thim_enable_upload_svg( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';

		return $mimes;
	}

	add_filter( 'upload_mimes', 'thim_enable_upload_svg' );
}
