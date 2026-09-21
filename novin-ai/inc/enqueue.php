<?php
/**
 * بارگذاری استایل‌ها و اسکریپت‌ها.
 *
 * ثبت دارایی‌ها روی init انجام می‌شود تا در ویرایشگر المنتور و پیش‌نمایش
 * نیز همیشه در دسترس باشند.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'novin_ai_localized_settings' ) ) {
	/**
	 * تنظیمات ارسالی به اسکریپت فرانت‌اند.
	 *
	 * @return array<string, mixed>
	 */
	function novin_ai_localized_settings() {
		return array(
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'novin_ai_nonce' ),
			'tilt'          => novin_ai_option( 'enable_tilt' ) ? 1 : 0,
			'tiltStrength'  => (int) novin_ai_option( 'tilt_strength', 12 ),
			'particles'     => novin_ai_option( 'enable_particles' ) ? 1 : 0,
			'cursor'        => novin_ai_option( 'enable_cursor' ) ? 1 : 0,
			'reveal'        => novin_ai_option( 'enable_reveal' ) ? 1 : 0,
			'orbs'          => novin_ai_option( 'enable_orbs' ) ? 1 : 0,
			'gridFloor'     => novin_ai_option( 'enable_grid_floor' ) ? 1 : 0,
			'marquee'       => novin_ai_option( 'enable_marquee' ) ? 1 : 0,
			'magnetic'      => novin_ai_option( 'enable_magnetic' ) ? 1 : 0,
			'reducedMotion' => novin_ai_option( 'respect_reduced_motion' ) ? 1 : 0,
			'isRtl'         => is_rtl() ? 1 : 0,
		);
	}
}

if ( ! function_exists( 'novin_ai_register_assets' ) ) {
	/**
	 * ثبت دارایی‌های قالب.
	 *
	 * @return void
	 */
	function novin_ai_register_assets() {
		wp_register_style( 'novin-ai-base', NOVIN_AI_URI . 'assets/css/theme.css', array(), NOVIN_AI_VERSION );
		wp_register_style( 'novin-ai-widgets', NOVIN_AI_URI . 'assets/css/widgets.css', array( 'novin-ai-base' ), NOVIN_AI_VERSION );

		wp_register_script(
			'novin-ai-frontend',
			NOVIN_AI_URI . 'assets/js/frontend.js',
			array(),
			NOVIN_AI_VERSION,
			true
		);

		wp_localize_script( 'novin-ai-frontend', 'NovinAiSettings', novin_ai_localized_settings() );

		if ( novin_ai_is_woocommerce_active() ) {
			wp_register_style( 'novin-ai-woocommerce', NOVIN_AI_URI . 'assets/css/woocommerce.css', array( 'novin-ai-base' ), NOVIN_AI_VERSION );
		}
	}
}
add_action( 'init', 'novin_ai_register_assets', 20 );

if ( ! function_exists( 'novin_ai_enqueue_assets' ) ) {
	/**
	 * بارگذاری دارایی‌ها در فرانت‌اند.
	 *
	 * @return void
	 */
	function novin_ai_enqueue_assets() {
		// فونت فارسی (قابل غیرفعال کردن از سفارشی‌ساز).
		if ( novin_ai_option( 'load_google_fonts' ) ) {
			wp_enqueue_style(
				'novin-ai-fonts',
				'https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap',
				array(),
				null
			);
		}

		wp_enqueue_style( 'novin-ai-style', get_stylesheet_uri(), array(), NOVIN_AI_VERSION );

		// الحاق CSS تولیدشده از سفارشی‌ساز.
		wp_add_inline_style( 'novin-ai-base', novin_ai_dynamic_css() );

		wp_enqueue_style( 'novin-ai-base' );
		wp_enqueue_style( 'novin-ai-widgets' );

		if ( novin_ai_is_woocommerce_active() ) {
			wp_enqueue_style( 'novin-ai-woocommerce' );
		}

		wp_enqueue_script( 'novin-ai-frontend' );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'novin_ai_enqueue_assets', 20 );

if ( ! function_exists( 'novin_ai_admin_assets' ) ) {
	/**
	 * دارایی‌های صفحات مدیریت.
	 *
	 * @param string $hook صفحه فعلی.
	 * @return void
	 */
	function novin_ai_admin_assets( $hook ) {
		if ( in_array( $hook, array( 'appearance_page_novin-ai-about' ), true ) ) {
			wp_enqueue_style( 'novin-ai-admin', NOVIN_AI_URI . 'assets/css/admin.css', array(), NOVIN_AI_VERSION );
		}
	}
}
add_action( 'admin_enqueue_scripts', 'novin_ai_admin_assets' );
