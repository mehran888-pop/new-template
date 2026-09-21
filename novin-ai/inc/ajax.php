<?php
/**
 * پردازش‌های AJAX قالب (خبرنامه و ...).
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'novin_ai_ajax_subscribe' ) ) {
	/**
	 * ثبت ایمیل در خبرنامه.
	 *
	 * به صورت پیش‌فرض ایمیل‌ها در یک گزینه وردپرس ذخیره می‌شوند.
	 * توسعه‌دهندگان می‌توانند با هوک novin_ai_subscribe_email آن را به
	 * سرویس ایمیل مارکتینگ متصل کنند.
	 *
	 * @return void
	 */
	function novin_ai_ajax_subscribe() {
		check_ajax_referer( 'novin_ai_nonce', 'nonce', false );

		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

		if ( ! is_email( $email ) ) {
			wp_send_json_error(
				array( 'message' => esc_html__( 'ایمیل وارد شده معتبر نیست.', 'novin-ai' ) )
			);
		}

		/**
		 * اجرای عملیات سفارشی هنگام عضویت در خبرنامه.
		 *
		 * @param string $email ایمیل کاربر.
		 */
		do_action( 'novin_ai_subscribe_email', $email );

		$subscribers = get_option( 'novin_ai_subscribers', array() );

		if ( ! is_array( $subscribers ) ) {
			$subscribers = array();
		}

		if ( ! in_array( $email, $subscribers, true ) ) {
			$subscribers[] = $email;
			update_option( 'novin_ai_subscribers', $subscribers );
		}

		wp_send_json_success(
			array( 'message' => esc_html__( 'ایمیل شما با موفقیت ثبت شد.', 'novin-ai' ) )
		);
	}
}
add_action( 'wp_ajax_novin_ai_subscribe', 'novin_ai_ajax_subscribe' );
add_action( 'wp_ajax_nopriv_novin_ai_subscribe', 'novin_ai_ajax_subscribe' );

if ( ! function_exists( 'novin_ai_render_noscript_style' ) ) {
	/**
	 * استایل‌های ضروری زمانی که جاوااسکریپت غیرفعال است.
	 *
	 * @return void
	 */
	function novin_ai_render_noscript_style() {
		echo '<noscript><style>.nv-reveal{opacity:1 !important;transform:none !important;}</style></noscript>';
	}
}
add_action( 'wp_footer', 'novin_ai_render_noscript_style', 20 );
