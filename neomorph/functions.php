<?php
/**
 * Neomorph Theme — bootstrap.
 *
 * تمام بخش‌های قالب به صورت ماژولار در پوشه inc/ بارگذاری می‌شوند.
 *
 * @package Neomorph
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'NEOMORPH_VERSION', '1.0.0' );
define( 'NEOMORPH_DIR', get_template_directory() );
define( 'NEOMORPH_URI', get_template_directory_uri() );
define( 'NEOMORPH_FILE', __FILE__ );

/**
 * PSR-4-like autoloader for theme classes: Neomorph\Foo\Bar → inc/foo/class-bar.php
 */
spl_autoload_register(
	function ( $class ) {
		if ( 0 !== strpos( $class, 'Neomorph\\' ) ) {
			return;
		}
		$parts = explode( '\\', substr( $class, strlen( 'Neomorph\\' ) ) );
		$file  = 'class-' . str_replace( '_', '-', strtolower( array_pop( $parts ) ) ) . '.php';
		$path  = NEOMORPH_DIR . '/inc';
		foreach ( $parts as $part ) {
			$path .= '/' . strtolower( str_replace( '_', '-', $part ) );
		}
		$path .= '/' . $file;
		if ( is_readable( $path ) ) {
			require_once $path;
		}
	}
);

// Core modules.
require_once NEOMORPH_DIR . '/inc/class-setup.php';
require_once NEOMORPH_DIR . '/inc/class-enqueue.php';
require_once NEOMORPH_DIR . '/inc/class-dynamic-css.php';
require_once NEOMORPH_DIR . '/inc/class-template-tags.php';
require_once NEOMORPH_DIR . '/inc/class-breadcrumbs.php';
require_once NEOMORPH_DIR . '/inc/class-custom-fonts.php';
require_once NEOMORPH_DIR . '/inc/class-dependencies.php';
require_once NEOMORPH_DIR . '/inc/class-admin-extras.php';
require_once NEOMORPH_DIR . '/inc/options/class-options.php';
require_once NEOMORPH_DIR . '/inc/woocommerce/class-woocommerce.php';
require_once NEOMORPH_DIR . '/inc/gravity-forms/class-gravity-forms.php';
require_once NEOMORPH_DIR . '/inc/elementor/class-elementor.php';
require_once NEOMORPH_DIR . '/inc/demo/class-demo-importer.php';

/**
 * Read a theme option with fallback to default.
 *
 * @param string $key     Option key.
 * @param mixed  $default Default value.
 * @return mixed
 */
function neomorph_option( $key, $default = null ) {
	$options = get_option( 'neomorph_options', array() );
	if ( is_array( $options ) && array_key_exists( $key, $options ) && '' !== $options[ $key ] && null !== $options[ $key ] ) {
		return $options[ $key ];
	}
	$defaults = class_exists( '\Neomorph\Options\Options' ) ? \Neomorph\Options\Options::defaults() : array();
	return array_key_exists( $key, $defaults ) ? $defaults[ $key ] : $default;
}

/**
 * Whether the companion plugin (Neomorph Core) is active.
 */
function neomorph_core_active() {
	return defined( 'NEOMORPH_CORE_VERSION' );
}

/**
 * Wrapper: print a neo (neumorphic) surface open tag.
 */
function neomorph_surface( $class = '', $tag = 'div' ) {
	printf( '<%1$s class="neo-surface %2$s">', esc_attr( $tag ), esc_attr( $class ) );
}

/**
 * Wrapper close tag for neomorph_surface().
 */
function neomorph_surface_end( $tag = 'div' ) {
	printf( '</%s>', esc_attr( $tag ) );
}
