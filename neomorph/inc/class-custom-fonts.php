<?php
/**
 * Custom font registry: Google Fonts + user-uploaded font families.
 *
 * @package Neomorph
 */

namespace Neomorph;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Custom_Fonts
 */
final class Custom_Fonts {

	const OPTION = 'neomorph_custom_fonts';

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'handle_upload' ) );
	}

	/**
	 * All fonts: built-in (google/system) + uploaded.
	 *
	 * @return array
	 */
	public static function get_available_fonts() {
		$fonts = array(
			'system'      => array(
				'label'  => esc_html__( 'فونت سیستم (بدون بارگذاری)', 'neomorph' ),
				'family' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Tahoma, Arial, sans-serif',
			),
			'vazirmatn'   => array(
				'label'  => 'وزیرمتن (Vazirmatn)',
				'family' => "'Vazirmatn', Tahoma, sans-serif",
				'google' => 'https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700;800&display=swap',
			),
			'iranyekan'   => array(
				'label'  => 'ایران‌یکان (بارگذاری دستی)',
				'family' => "'IRANYekan', Tahoma, sans-serif",
			),
			'iransansx'   => array(
				'label'  => 'ایران‌سنس ایکس (بارگذاری دستی)',
				'family' => "'IRANSansX', Tahoma, sans-serif",
			),
			'estedad'     => array(
				'label'  => 'استعداد (Estedad)',
				'family' => "'Estedad', Tahoma, sans-serif",
			),
			'rubik'       => array(
				'label'  => 'Rubik (Google)',
				'family' => "'Rubik', Tahoma, sans-serif",
				'google' => 'https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;700&display=swap',
			),
			'playfair'    => array(
				'label'  => 'Playfair Display (Google — نمایشی)',
				'family' => "'Playfair Display', serif",
				'google' => 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap',
			),
		);

		$custom = get_option( self::OPTION, array() );
		if ( is_array( $custom ) ) {
			foreach ( $custom as $slug => $data ) {
				$fonts[ 'custom-' . $slug ] = $data;
			}
		}

		return $fonts;
	}

	/**
	 * CSS font-family string for a handle.
	 */
	public static function family( $handle ) {
		$fonts = self::get_available_fonts();
		return isset( $fonts[ $handle ]['family'] ) ? $fonts[ $handle ]['family'] : $fonts['system']['family'];
	}

	/**
	 * Handle font ZIP/woff2 upload from theme options (multipart form).
	 */
	public static function handle_upload() {
		if ( empty( $_POST['neomorph_font_upload_nonce'] ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_key( $_POST['neomorph_font_upload_nonce'] ), 'neomorph_font_upload' ) ) {
			return;
		}
		if ( empty( $_FILES['neomorph_font_files'] ) || empty( $_FILES['neomorph_font_files']['name'][0] ) ) {
			return;
		}

		$family = isset( $_POST['neomorph_font_family'] ) ? sanitize_text_field( wp_unslash( $_POST['neomorph_font_family'] ) ) : '';
		$slug   = isset( $_POST['neomorph_font_slug'] ) ? sanitize_key( $_POST['neomorph_font_slug'] ) : '';
		if ( ! $family || ! $slug ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$faces    = array();
		$files    = $_FILES['neomorph_font_files'];
		$accepted = array( 'woff2', 'woff', 'ttf', 'otf' );

		foreach ( $files['name'] as $i => $name ) {
			$ext = strtolower( pathinfo( $name, PATHINFO_EXTENSION ) );
			if ( ! in_array( $ext, $accepted, true ) ) {
				continue;
			}
			$file = array(
				'name'     => sanitize_file_name( $slug . '-' . $i . '.' . $ext ),
				'type'     => $files['type'][ $i ],
				'tmp_name' => $files['tmp_name'][ $i ],
				'error'    => $files['error'][ $i ],
				'size'     => $files['size'][ $i ],
			);
			$upload = wp_handle_upload( $file, array( 'test_form' => false ) );
			if ( isset( $upload['url'] ) ) {
				$weight = '400';
				if ( preg_match( '/(100|200|300|400|500|600|700|800|900)/', $name, $m ) ) {
					$weight = $m[1];
				}
				$faces[] = array(
					'url'    => $upload['url'],
					'weight' => $weight,
					'style'  => ( false !== strpos( $name, 'italic' ) ) ? 'italic' : 'normal',
					'format' => 'woff2' === $ext ? 'woff2' : ( 'woff' === $ext ? 'woff' : ( 'truetype' === $ext || 'ttf' === $ext ? 'truetype' : 'opentype' ) ),
				);
			}
		}

		if ( $faces ) {
			$custom                = get_option( self::OPTION, array() );
			$custom[ $slug ]       = array(
				'label'  => sprintf( '%s (سفارشی)', $family ),
				'family' => "'" . $family . "', Tahoma, sans-serif",
				'custom' => $faces,
			);
			update_option( self::OPTION, $custom );
		}

		wp_safe_redirect( add_query_arg( array( 'page' => 'neomorph-settings', 'tab' => 'typography', 'font-uploaded' => count( $faces ) ), admin_url( 'themes.php' ) ) );
		exit;
	}
}

Custom_Fonts::init();
