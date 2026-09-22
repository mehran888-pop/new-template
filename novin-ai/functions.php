<?php
/**
 * فایل اصلی قالب Novin AI
 *
 * تمام قابلیت‌ها در پوشه inc/ تفکیک شده‌اند تا توسعه و شخصی‌سازی ساده باشد.
 *
 * @package Novin_AI
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // دسترسی مستقیم ممنوع.
}

/* ثابت‌های قالب */
define( 'NOVIN_AI_VERSION', '1.1.0' );
define( 'NOVIN_AI_DIR', trailingslashit( get_template_directory() ) );
define( 'NOVIN_AI_URI', trailingslashit( get_template_directory_uri() ) );
define( 'NOVIN_AI_INC', NOVIN_AI_DIR . 'inc/' );
define( 'NOVIN_AI_MIN_PHP', '7.4' );

/* بارگذاری ماژول‌ها */
require_once NOVIN_AI_INC . 'helpers.php';
require_once NOVIN_AI_INC . 'setup.php';
require_once NOVIN_AI_INC . 'enqueue.php';
require_once NOVIN_AI_INC . 'cpt.php';
require_once NOVIN_AI_INC . 'metabox.php';
require_once NOVIN_AI_INC . 'template-tags.php';
require_once NOVIN_AI_INC . 'customizer.php';
require_once NOVIN_AI_INC . 'woocommerce.php';
require_once NOVIN_AI_INC . 'ajax.php';
require_once NOVIN_AI_INC . 'elementor/elementor.php';
require_once NOVIN_AI_INC . 'admin.php';
require_once NOVIN_AI_INC . 'demo-import.php';

if ( ! function_exists( 'novin_ai_php_notice' ) ) {
	/**
	 * هشدار نسخه قدیمی PHP.
	 *
	 * @return void
	 */
	function novin_ai_php_notice() {
		printf(
			'<div class="notice notice-error"><p>%s</p></div>',
			esc_html__( 'قالب Novin AI به نسخه PHP 7.4 یا بالاتر نیاز دارد.', 'novin-ai' )
		);
	}
}

if ( version_compare( PHP_VERSION, NOVIN_AI_MIN_PHP, '<' ) ) {
	add_action( 'admin_notices', 'novin_ai_php_notice' );
}
