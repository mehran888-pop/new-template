<?php
/**
 * Dependencies: required/recommended plugins (Elementor, WooCommerce, Gravity Forms, Neomorph Core).
 * Lightweight replacement for TGM Plugin Activation.
 *
 * @package Neomorph
 */

namespace Neomorph;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Dependencies
 */
final class Dependencies {

	/**
	 * Plugin map: file => [label, required?].
	 */
	public static function requirements() {
		return array(
			'neomorph-core/neomorph-core.php' => array(
				'label'    => esc_html__( 'Neomorph Core (افزونه همراه — پنل مشتری، OTP، CRM، فاکتور، استخدام)', 'neomorph' ),
				'required' => true,
				'local'    => NEOMORPH_DIR . '/../neomorph-core',
			),
			'elementor/elementor.php'         => array(
				'label'    => 'Elementor Page Builder',
				'required' => true,
				'repo'     => 'elementor',
			),
			'woocommerce/woocommerce.php'     => array(
				'label'    => 'WooCommerce',
				'required' => true,
				'repo'     => 'woocommerce',
			),
			'gravityforms/gravityforms.php'   => array(
				'label'    => 'Gravity Forms',
				'required' => false,
				'repo'     => 'gravityforms',
			),
		);
	}

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'admin_notices', array( __CLASS__, 'notices' ) );
		add_action( 'tgmpa_register', '__return_false' );
		add_filter( 'plugin_action_links', array( __CLASS__, 'core_link' ), 10, 2 );
	}

	/**
	 * Admin warning with install/activate links.
	 */
	public static function notices() {
		if ( ! current_user_can( 'install_plugins' ) && ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$missing = array();
		foreach ( self::requirements() as $file => $info ) {
			if ( ! self::is_active( $file ) ) {
				$missing[ $file ] = $info;
			}
		}
		if ( ! $missing ) {
			return;
		}
		echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'نئومورف — پیش‌نیاز‌ها:', 'neomorph' ) . '</strong></p><ul>';
		foreach ( $missing as $file => $info ) {
			$path    = explode( '/', $file );
			$slug    = $path[0];
			$is_core = 0 === strpos( $file, 'neomorph-core' );
			$zip     = $is_core ? NEOMORPH_DIR . '/../neomorph-core.zip' : '';
			if ( $is_core && file_exists( NEOMORPH_DIR . '/../neomorph-core/neomorph-core.php' ) ) {
				$url = wp_nonce_url(
					self_admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $file ) ),
					'activate-plugin_' . $file
				);
				$label = esc_html__( 'فعال‌سازی Neomorph Core', 'neomorph' );
			} else {
				$url   = self_admin_url( 'plugin-install.php?tab=search&type=term&s=' . rawurlencode( $slug ) . '&plugin-search-input=' . rawurlencode( $slug ) );
				$label = esc_html__( 'نصب / فعال‌سازی', 'neomorph' );
			}
			printf(
				'<li>%s — <a href="%s">%s</a>%s</li>',
				esc_html( $info['label'] ),
				esc_url( $url ),
				esc_html( $label ),
				$info['required'] ? ' <em>(' . esc_html__( 'الزامی', 'neomorph' ) . ')</em>' : ''
			);
		}
		echo '</ul><p class="description">' . esc_html__( 'افزونه Neomorph Core در پوشه neomorph-core همین مخزن قرار دارد؛ آن را در wp-content/plugins کپی کنید.', 'neomorph' ) . '</p></div>';
	}

	/**
	 * Is plugin file active (or, for core, defined constant).
	 */
	public static function is_active( $file ) {
		if ( 0 === strpos( $file, 'neomorph-core' ) ) {
			return defined( 'NEOMORPH_CORE_VERSION' );
		}
		return in_array( $file, (array) get_option( 'active_plugins', array() ), true ) || class_exists( 'SitePress' );
	}

	/**
	 * Quick link from plugins list.
	 */
	public static function core_link( $links, $file ) {
		if ( 'neomorph-core/neomorph-core.php' === $file ) {
			$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=neomorph-core-settings' ) ) . '">' . esc_html__( 'تنظیمات', 'neomorph' ) . '</a>';
		}
		return $links;
	}
}

Dependencies::init();
