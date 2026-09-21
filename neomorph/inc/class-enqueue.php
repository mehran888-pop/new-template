<?php
/**
 * Styles & scripts.
 *
 * @package Neomorph
 */

namespace Neomorph;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Enqueue
 */
final class Enqueue {

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_assets' ) );
		add_action( 'wp_head', array( __CLASS__, 'preconnect' ), 5 );
	}

	/**
	 * Front-end assets.
	 */
	public static function assets() {
		// Fonts (built-in Vazirmatn or custom uploaded font).
		self::enqueue_fonts();

		wp_enqueue_style( 'neomorph-style', get_stylesheet_uri(), array(), NEOMORPH_VERSION );
		wp_enqueue_style( 'neomorph-main', NEOMORPH_URI . '/assets/css/neomorph.css', array( 'neomorph-style' ), NEOMORPH_VERSION );

		if ( class_exists( 'WooCommerce' ) ) {
			wp_enqueue_style( 'neomorph-woo', NEOMORPH_URI . '/assets/css/woocommerce.css', array( 'neomorph-main' ), NEOMORPH_VERSION );
		}

		if ( class_exists( 'GFForms' ) || class_exists( 'GFCommon' ) ) {
			wp_enqueue_style( 'neomorph-gf', NEOMORPH_URI . '/assets/css/gravity-forms.css', array( 'neomorph-main' ), NEOMORPH_VERSION );
		}

		wp_enqueue_script( 'neomorph-main', NEOMORPH_URI . '/assets/js/main.js', array(), NEOMORPH_VERSION, true );

		wp_localize_script(
			'neomorph-main',
			'neomorphData',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'restUrl'  => esc_url_raw( rest_url( 'neomorph/v1/' ) ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'i18n'     => array(
					'loading'  => esc_html__( 'در حال بارگذاری…', 'neomorph' ),
					'error'    => esc_html__( 'خطایی رخ داد. دوباره تلاش کنید.', 'neomorph' ),
					'required' => esc_html__( 'تکمیل این فیلد الزامی است.', 'neomorph' ),
				),
				'currency' => neomorph_option( 'currency', 'تومان' ),
			)
		);

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Register + enqueue body/heading fonts based on options.
	 */
	public static function enqueue_fonts() {
		$body_font    = neomorph_option( 'font_body', 'vazirmatn' );
		$heading_font = neomorph_option( 'font_heading', 'vazirmatn' );

		$registry = Custom_Fonts::get_available_fonts();

		foreach ( array_unique( array( $body_font, $heading_font ) ) as $handle ) {
			if ( 'system' === $handle ) {
				continue;
			}
			if ( isset( $registry[ $handle ]['custom'] ) ) {
				// Uploaded font-face.
				$faces = $registry[ $handle ]['custom'];
				$css   = '';
				foreach ( (array) $faces as $face ) {
					$format = isset( $face['format'] ) ? $face['format'] : 'woff2';
					$css   .= sprintf(
						"@font-face{font-family:'%s';font-style:%s;font-weight:%s;font-display:swap;src:url('%s') format('%s');}",
						esc_attr( $registry[ $handle ]['family'] ),
						esc_attr( isset( $face['style'] ) ? $face['style'] : 'normal' ),
						esc_attr( isset( $face['weight'] ) ? $face['weight'] : '400' ),
						esc_url( $face['url'] ),
						esc_attr( $format )
					);
				}
				wp_register_style( 'neomorph-font-' . $handle, false, array(), NEOMORPH_VERSION );
				wp_add_inline_style( 'neomorph-font-' . $handle, $css );
				wp_enqueue_style( 'neomorph-font-' . $handle );
			} elseif ( isset( $registry[ $handle ]['google'] ) ) {
				wp_enqueue_style(
					'neomorph-font-' . $handle,
					$registry[ $handle ]['google'],
					array(),
					null
				);
			}
		}
	}

	/**
	 * Preconnect for Google Fonts.
	 */
	public static function preconnect() {
		echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	}

	/**
	 * Admin assets for theme options + demo importer + wp-admin skin.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public static function admin_assets( $hook ) {
		// Professional wp-admin skin (all screens, toggle in theme options).
		if ( neomorph_option( 'admin_style', '1' ) ) {
			wp_enqueue_style( 'neomorph-admin-theme', NEOMORPH_URI . '/assets/css/admin-theme.css', array(), NEOMORPH_VERSION );
			$density = neomorph_option( 'admin_density', 'comfortable' );
			wp_add_inline_style(
				'neomorph-admin-theme',
				':root{--neoa-accent:' . ( neomorph_option( 'color_accent', '#6c5ce7' ) ) . ';--neoa-accent-2:' . ( neomorph_option( 'color_accent_2', '#00b894' ) ) . ';}'
			);
			add_filter(
				'admin_body_class',
				function ( $classes ) use ( $density ) {
					$classes .= ' neo-admin-skin';
					if ( 'compact' === $density ) {
						$classes .= ' neo-admin-compact';
					}
					return $classes;
				}
			);
		}

		$screen  = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		$is_ours = ( false !== strpos( (string) $hook, 'neomorph' ) || ( $screen && in_array( $screen->id, array( 'appearance_page_neomorph-settings' ), true ) ) );
		if ( ! $is_ours ) {
			return;
		}
		wp_enqueue_style( 'neomorph-admin', NEOMORPH_URI . '/assets/css/admin.css', array(), NEOMORPH_VERSION );
		wp_enqueue_script( 'neomorph-admin', NEOMORPH_URI . '/assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), NEOMORPH_VERSION, true );
		wp_enqueue_style( 'wp-color-picker' );
	}
}

Enqueue::init();
