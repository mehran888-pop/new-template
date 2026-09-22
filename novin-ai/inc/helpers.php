<?php
/**
 * توابع کمکی و پیش‌فرض‌های قالب.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'novin_ai_defaults' ) ) {
	/**
	 * مقادیر پیش‌فرض تمام تنظیمات قالب.
	 *
	 * @return array<string, mixed>
	 */
	function novin_ai_defaults() {
		return array(
			// رنگ‌ها.
			'color_primary'      => '#6d5efc',
			'color_secondary'    => '#22d3ee',
			'color_accent'       => '#ff4ecd',
			'color_bg'           => '#070813',
			'color_bg_alt'       => '#0c1022',
			'color_surface'      => 'rgba(255,255,255,0.045)',
			'color_text'         => '#eef1ff',
			'color_muted'        => '#a3aad0',
			'color_border'       => 'rgba(255,255,255,0.10)',

			// تایپوگرافی.
			'font_body'          => 'Vazirmatn, "Segoe UI", Tahoma, sans-serif',
			'font_heading'       => 'Vazirmatn, "Segoe UI", Tahoma, sans-serif',
			'font_size'          => 16,
			'line_height'        => 1.9,
			'heading_line_height' => 1.25,
			'heading_weight'     => 800,
			'letter_spacing'     => 0,
			'load_google_fonts'  => true,

			// چیدمان.
			'container_width'    => 1240,
			'section_padding'   => 110,
			'radius'             => 20,
			'radius_sm'          => 12,

			// افکت‌های سه‌بعدی و فراگیر.
			'enable_tilt'        => true,
			'enable_particles'   => true,
			'enable_cursor'      => true,
			'enable_reveal'      => true,
			'enable_orbs'        => true,
			'enable_grid_floor'  => true,
			'enable_marquee'     => true,
			'enable_magnetic'    => true,
			'hover_glow'         => true,
			'tilt_strength'      => 12,
			'respect_reduced_motion' => true,

			// وبلاگ.
			'blog_layout'        => 'grid',
			'blog_sidebar'       => false,
			'excerpt_length'     => 22,
			'read_more_text'     => 'ادامه مطلب',
			'show_reading_time'  => true,

			// فروشگاه.
			'shop_columns'       => 3,
			'shop_per_page'      => 12,
			'shop_card_style'    => 'glass',
			'shop_hover_zoom'    => true,

			// هدر و فوتر پیش‌فرض.
			'header_cta_text'    => 'دریافت مشاوره رایگان',
			'footer_copyright'   => 'تمامی حقوق این وب‌سایت محفوظ است.',
		);
	}
}

if ( ! function_exists( 'novin_ai_option' ) ) {
	/**
	 * خواندن یک تنظیم از سفارشی‌ساز (Customizer).
	 *
	 * @param string $key     کلید بدون پیشوند.
	 * @param mixed  $default مقدار جایگزین.
	 * @return mixed
	 */
	function novin_ai_option( $key, $default = null ) {
		$defaults = novin_ai_defaults();

		if ( null === $default ) {
			$default = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
		}

		return get_theme_mod( 'novin_ai_' . $key, $default );
	}
}

if ( ! function_exists( 'novin_ai_is_true' ) ) {
	/**
	 * بررسی بولینِ مقدار تنظیمات (value may be 1/'1'/true/'yes').
	 *
	 * @param mixed $value مقدار.
	 * @return bool
	 */
	function novin_ai_is_true( $value ) {
		return in_array( $value, array( true, 1, '1', 'yes', 'on' ), true );
	}
}

if ( ! function_exists( 'novin_ai_is_woocommerce_active' ) ) {
	/**
	 * فعال بودن ووکامرس.
	 *
	 * @return bool
	 */
	function novin_ai_is_woocommerce_active() {
		return class_exists( 'WooCommerce' );
	}
}

if ( ! function_exists( 'novin_ai_is_elementor_active' ) ) {
	/**
	 * فعال بودن المنتور.
	 *
	 * @return bool
	 */
	function novin_ai_is_elementor_active() {
		return did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' );
	}
}

if ( ! function_exists( 'novin_ai_is_elementor_pro' ) ) {
	/**
	 * فعال بودن المنتور پرو (برای Theme Builder).
	 *
	 * @return bool
	 */
	function novin_ai_is_elementor_pro() {
		return defined( 'ELEMENTOR_PRO_VERSION' ) || class_exists( '\ElementorPro\Plugin' );
	}
}

if ( ! function_exists( 'novin_ai_get_menus' ) ) {
	/**
	 * فهرست منوهای تعریف‌شده برای کنترل‌های المنتور.
	 *
	 * @return array<int|string, string>
	 */
	function novin_ai_get_menus() {
		$menus = wp_get_nav_menus();
		$list  = array(
			'' => esc_html__( '— انتخاب منو —', 'novin-ai' ),
		);

		if ( ! empty( $menus ) ) {
			foreach ( $menus as $menu ) {
				$list[ $menu->term_id ] = $menu->name;
			}
		}

		return $list;
	}
}

if ( ! function_exists( 'novin_ai_kses' ) ) {
	/**
	 * اجازه دادن به تگ‌های امن برای خروجی‌های html کنترل‌شده.
	 *
	 * @param string $html رشته ورودی.
	 * @return string
	 */
	function novin_ai_kses( $html ) {
		$allowed = array(
			'a'      => array( 'href' => true, 'title' => true, 'target' => true, 'rel' => true, 'class' => true, 'id' => true ),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'b'      => array(),
			'i'      => array(),
			'u'      => array(),
			'span'   => array( 'class' => true, 'style' => true, 'dir' => true ),
			'code'   => array( 'class' => true ),
			'mark'   => array( 'class' => true ),
			'small'  => array( 'class' => true ),
			'p'      => array( 'class' => true, 'style' => true ),
			'ul'     => array( 'class' => true ),
			'ol'     => array( 'class' => true ),
			'li'     => array( 'class' => true ),
			'img'    => array( 'src' => true, 'alt' => true, 'class' => true, 'width' => true, 'height' => true, 'loading' => true ),
			'svg'    => array( 'class' => true, 'width' => true, 'height' => true, 'viewbox' => true, 'fill' => true, 'aria-hidden' => true ),
			'path'   => array( 'd' => true, 'fill' => true ),
		);

		return wp_kses( $html, $allowed );
	}
}

if ( ! function_exists( 'novin_ai_excerpt' ) ) {
	/**
	 * بریدن متن به تعداد کلمات مشخص (پشتیبانی از فارسی).
	 *
	 * @param string $text  متن.
	 * @param int    $words تعداد کلمات.
	 * @param string $more  پسوند.
	 * @return string
	 */
	function novin_ai_excerpt( $text, $words = 22, $more = '…' ) {
		$text = wp_strip_all_tags( strip_shortcodes( $text ) );
		$text = trim( preg_replace( '/\s+/u', ' ', $text ) );

		if ( ! $words || ! $text ) {
			return $text;
		}

		$parts = preg_split( '/[\s\u200c]+/u', $text );

		if ( count( $parts ) <= $words ) {
			return $text;
		}

		return implode( ' ', array_slice( $parts, 0, (int) $words ) ) . $more;
	}
}

if ( ! function_exists( 'novin_ai_reading_time' ) ) {
	/**
	 * زمان تقریبی مطالعه مطلب.
	 *
	 * @param int|WP_Post|null $post نوشته.
	 * @return int دقیقه.
	 */
	function novin_ai_reading_time( $post = null ) {
		$content = get_post_field( 'post_content', $post );
		$content = wp_strip_all_tags( strip_shortcodes( (string) $content ) );
		$words   = count( preg_split( '/[\s\u200c]+/u', trim( $content ) ) );

		return max( 1, (int) ceil( $words / 200 ) );
	}
}

if ( ! function_exists( 'novin_ai_get_meta' ) ) {
	/**
	 * خواندن متادیتا با مقدار پیش‌فرض.
	 *
	 * @param int    $post_id شناسه نوشته.
	 * @param string $key     کلید متا.
	 * @param mixed  $default مقدار پیش‌فرض.
	 * @return mixed
	 */
	function novin_ai_get_meta( $post_id, $key, $default = '' ) {
		$value = get_post_meta( $post_id, '_novin_' . $key, true );

		return ( '' === $value || null === $value || false === $value ) ? $default : $value;
	}
}

if ( ! function_exists( 'novin_ai_meta_lines' ) ) {
	/**
	 * تبدیل متادیتای چندخطی (هر خط یک آیتم) به آرایه.
	 *
	 * @param string $value متن خام.
	 * @return array<int, string>
	 */
	function novin_ai_meta_lines( $value ) {
		if ( empty( $value ) ) {
			return array();
		}

		$lines = preg_split( '/\r\n|\r|\n/', (string) $value );
		$lines = array_map( 'trim', $lines );

		return array_values( array_filter( $lines ) );
	}
}

if ( ! function_exists( 'novin_ai_meta_rows' ) ) {
	/**
	 * تبدیل متادیتای چندستونه به آرایه.
	 *
	 * هر خط با «|» به ستون‌های جداگانه تقسیم می‌شود؛ مناسب برای رزومه:
	 * مهارت‌ها:        Python|90
	 * سوابق کاری:     ۱۴۰۰-۱۴۰۳|مدیر فنی|شرکت الف
	 * تحصیلات:        کارشناسی|مهندسی کامپیوتر|دانشگاه تهران
	 *
	 * @param string $value  متن خام.
	 * @param int    $columns تعداد ستون‌های مورد انتظار (مقادیر خالی پر می‌شود).
	 * @return array<int, array<int, string>>
	 */
	function novin_ai_meta_rows( $value, $columns = 3 ) {
		$rows = array();

		foreach ( novin_ai_meta_lines( $value ) as $line ) {
			$parts = array_map( 'trim', explode( '|', $line ) );

			for ( $i = 0; $i < $columns; $i++ ) {
				if ( ! isset( $parts[ $i ] ) ) {
					$parts[ $i ] = '';
				}
			}

			$rows[] = array_slice( $parts, 0, $columns );
		}

		return $rows;
	}
}

if ( ! function_exists( 'novin_ai_social_networks' ) ) {
	/**
	 * شبکه‌های اجتماعی پشتیبانی‌شده.
	 *
	 * @return array<string, string>
	 */
	function novin_ai_social_networks() {
		return array(
			'instagram' => esc_html__( 'اینستاگرام', 'novin-ai' ),
			'telegram'  => esc_html__( 'تلگرام', 'novin-ai' ),
			'whatsapp'  => esc_html__( 'واتس‌اپ', 'novin-ai' ),
			'linkedin'  => esc_html__( 'لینکدین', 'novin-ai' ),
			'twitter'   => esc_html__( 'ایکس / توییتر', 'novin-ai' ),
			'youtube'   => esc_html__( 'یوتیوب', 'novin-ai' ),
			'aparat'    => esc_html__( 'آپارات', 'novin-ai' ),
			'github'    => esc_html__( 'گیت‌هاب', 'novin-ai' ),
			'dribbble'  => esc_html__( 'دریبل', 'novin-ai' ),
			'email'     => esc_html__( 'ایمیل', 'novin-ai' ),
			'phone'     => esc_html__( 'تلفن', 'novin-ai' ),
		);
	}
}

if ( ! function_exists( 'novin_ai_social_icon' ) ) {
	/**
	 * آیکون داخلی (SVG) شبکه‌های اجتماعی.
	 *
	 * @param string $network کلید شبکه.
	 * @return string
	 */
	function novin_ai_social_icon( $network ) {
		$icons = array(
			'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2.2c3.2 0 3.6 0 4.9.07 1.2.05 1.8.25 2.2.42.6.22 1 .48 1.4.9.4.4.7.8.9 1.4.2.4.4 1 .4 2.2.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c0 1.2-.2 1.8-.4 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1 .4-2.2.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2 0-1.8-.2-2.2-.4-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.2-.4-.4-1-.4-2.2C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c0-1.2.2-1.8.4-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1-.4 2.2-.4C8.4 2.2 8.8 2.2 12 2.2Zm0 3.2A6.6 6.6 0 1 0 18.6 12 6.6 6.6 0 0 0 12 5.4Zm0 10.9A4.3 4.3 0 1 1 16.3 12 4.3 4.3 0 0 1 12 16.3Zm6.9-11a1.5 1.5 0 1 1-1.5-1.5 1.5 1.5 0 0 1 1.5 1.5Z"/></svg>',
			'telegram'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M21.7 4.3 19 19.1c-.2 1-.8 1.2-1.6.8l-4.4-3.2-2.1 2c-.2.3-.4.4-.9.4l.3-4.3 7.8-7c.3-.3 0-.5-.5-.2L8.6 13.3 4.4 12c-.9-.3-.9-.9.2-1.3l16.4-6.3c.8-.3 1.5.2 1.2 1.4-.1.2-.1.3-.5.5Z"/></svg>',
			'whatsapp'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1 1 12 20Zm4.5-5.9c-.2-.1-1.4-.7-1.6-.8s-.4-.1-.5.1-.6.8-.7.9-.3.1-.5 0a6.5 6.5 0 0 1-1.9-1.2 7.2 7.2 0 0 1-1.3-1.7c-.1-.3 0-.4.1-.5l.4-.5s.2-.2.3-.4a.5.5 0 0 0 0-.5c-.1-.2-.5-1.2-.7-1.6s-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 2.9 2.9 0 0 0-.9 2.1 5.1 5.1 0 0 0 1 2.7 11.4 11.4 0 0 0 4.5 4 5.2 5.2 0 0 0 2.6.9 3.1 3.1 0 0 0 2-.8 2.4 2.4 0 0 0 .5-1.6c-.1-.2-.2-.3-.4-.4Z"/></svg>',
			'linkedin'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.9 21H3.4V9h3.5v12ZM5.2 7.5A2 2 0 1 1 7.2 5.5a2 2 0 0 1-2 2ZM21 21h-3.5v-6.3c0-1.6-.6-2.5-1.9-2.5a2 2 0 0 0-1.9 1.4 2.6 2.6 0 0 0-.1.9V21H10s.1-10.9 0-12h3.6v1.7c.5-.8 1.4-1.9 3.4-1.9 2.5 0 4 1.6 4 5V21Z"/></svg>',
			'twitter'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M17.5 3h3.2l-7 8 7.3 10h-5.6l-4.4-6-5 6H2.8l7.3-8.5L3 3h5.7l4.1 5.6L17.5 3Zm-1.1 16h1.8L7.9 4.9H6L16.4 19Z"/></svg>',
			'youtube'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M22.5 7.2a2.6 2.6 0 0 0-1.8-1.8C19 5 12 5 12 5s-7 0-8.7.4a2.6 2.6 0 0 0-1.8 1.8A27 27 0 0 0 1.2 12a27 27 0 0 0 .3 4.8 2.6 2.6 0 0 0 1.8 1.8C5 19 12 19 12 19s7 0 8.7-.4a2.6 2.6 0 0 0 1.8-1.8 27 27 0 0 0 .3-4.8 27 27 0 0 0-.3-4.8ZM9.9 15.3V8.7l5.5 3.3-5.5 3.3Z"/></svg>',
			'aparat'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm4.3 13.7a1.4 1.4 0 0 1-2 0l-2.4-2.4-2.4 2.4a1.4 1.4 0 0 1-2-2l2.4-2.4-2.4-2.4a1.4 1.4 0 1 1 2-2l2.4 2.4 2.4-2.4a1.4 1.4 0 0 1 2 2l-2.4 2.4 2.4 2.4a1.4 1.4 0 0 1 0 2Z"/></svg>',
			'github'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 0 0-3.2 19.5c.5.1.7-.2.7-.5v-1.8c-2.8.6-3.4-1.3-3.4-1.3-.4-1.2-1.1-1.5-1.1-1.5-.9-.6.1-.6.1-.6 1 .1 1.5 1 1.5 1 .9 1.5 2.3 1.1 2.9.8.1-.6.4-1.1.7-1.3-2.2-.3-4.6-1.1-4.6-5 0-1.1.4-2 1-2.7-.1-.3-.4-1.3.1-2.6 0 0 .8-.3 2.7 1a9.3 9.3 0 0 1 5 0c1.9-1.3 2.7-1 2.7-1 .5 1.3.2 2.3.1 2.6a3.9 3.9 0 0 1 1 2.7c0 3.9-2.4 4.7-4.6 5 .4.3.7.9.7 1.9v2.8c0 .3.2.6.7.5A10 10 0 0 0 12 2Z"/></svg>',
			'dribbble'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm6.6 6.1a8.2 8.2 0 0 1 1.3 4.4 12 12 0 0 1-4.4.7 22 22 0 0 0-.8-3.8c1.6-.6 3.1-1.2 3.9-1.3ZM12 3.9c2.2 0 4.1.8 5.6 2.2-.7.2-2.1.7-3.7 1.3A21.4 21.4 0 0 0 10.4 4 8.4 8.4 0 0 1 12 3.9ZM8.2 4.7a25 25 0 0 1 3.4 3.4 20.4 20.4 0 0 1-8 2.2 8.3 8.3 0 0 1 4.6-5.6ZM3.9 12.1v-.3c2 0 5.3-.4 8.6-2.4a24.3 24.3 0 0 1 1 4.8 16.7 16.7 0 0 1-8.2 2.3 8.3 8.3 0 0 1-1.4-4.4Zm2.6 6a16 16 0 0 0 7.4-2 19 19 0 0 1 .6 4.6 8.3 8.3 0 0 1-8-2.6Zm10 3.1a20.4 20.4 0 0 0-.7-5c1.5-.2 2.9-.5 4-.9a8.3 8.3 0 0 1-3.3 5.9Z"/></svg>',
			'email'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm17 4.2-7 4.4a1 1 0 0 1-1.1 0L4.9 9.2V18h14.2V9.2ZM19.4 7H4.6l7.4 4.6L19.4 7Z"/></svg>',
			'phone'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1 0c1.1.4 2.3.6 3.5.6a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.2a1 1 0 0 1 1 1c0 1.2.2 2.4.6 3.5a1 1 0 0 1 0 1l-2.2 2.3Z"/></svg>',
			'link'      => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M10.6 13.4a1 1 0 0 1 0-1.4l1-1a1 1 0 0 1 1.4 1.4l-1 1a1 1 0 0 1-1.4 0Zm3.3-3.3a1 1 0 0 1 0-1.4l1-1a1 1 0 0 1 1.4 1.4l-1 1a1 1 0 0 1-1.4 0ZM8.5 15.5a4.2 4.2 0 0 1 0-6l2.1-2.2a4.2 4.2 0 0 1 6 6l-1 1a1 1 0 0 1-1.4-1.4l1-1a2.2 2.2 0 0 0-3.1-3.1L10 10.9a2.2 2.2 0 0 0 0 3.1 1 1 0 0 1-1.4 1.5Z"/></svg>',
		);

		return isset( $icons[ $network ] ) ? $icons[ $network ] : $icons['link'];
	}
}

if ( ! function_exists( 'novin_ai_icon' ) ) {
	/**
	 * آیکون‌های عمومی قالب (برای هدر، جستجو، دکمه‌ها و ...).
	 *
	 * @param string $name نام آیکون.
	 * @return string
	 */
	function novin_ai_icon( $name ) {
		$icons = array(
			'arrow'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="m13.3 5.3 6 6a1 1 0 0 1 0 1.4l-6 6a1 1 0 0 1-1.4-1.4l4.3-4.3H4a1 1 0 0 1 0-2h12.2l-4.3-4.3a1 1 0 0 1 1.4-1.4Z"/></svg>',
			'arrow-left' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M10.7 5.3a1 1 0 0 1 0 1.4L6.4 11H20a1 1 0 0 1 0 2H6.4l4.3 4.3a1 1 0 0 1-1.4 1.4l-6-6a1 1 0 0 1 0-1.4l6-6a1 1 0 0 1 1.4 0Z"/></svg>',
			'check'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.3 6.3a1 1 0 0 1 0 1.4l-9.6 9.6a1 1 0 0 1-1.4 0L4.7 12.7a1 1 0 0 1 1.4-1.4l3.9 3.9 8.9-8.9a1 1 0 0 1 1.4 0Z"/></svg>',
			'close'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.7 5.3a1 1 0 0 0-1.4 1.4L10.6 12l-5.3 5.3a1 1 0 1 0 1.4 1.4L12 13.4l5.3 5.3a1 1 0 0 0 1.4-1.4L13.4 12l5.3-5.3a1 1 0 0 0-1.4-1.4L12 10.6 6.7 5.3Z"/></svg>',
			'search'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M10.5 3a7.5 7.5 0 1 0 4.6 13.4l4.2 4.3a1 1 0 0 0 1.4-1.5l-4.2-4.2A7.5 7.5 0 0 0 10.5 3Zm0 2a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11Z"/></svg>',
			'menu'      => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M3 6h18a1 1 0 0 0 0-2H3a1 1 0 0 0 0 2Zm18 5H3a1 1 0 0 0 0 2h18a1 1 0 0 0 0-2Zm0 7H3a1 1 0 0 0 0 2h18a1 1 0 0 0 0-2Z"/></svg>',
			'cart'      => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M7 4h11.9a1 1 0 0 1 1 .8l1.6 8A1 1 0 0 1 20.5 14H8.2l.3 1.5H19a1 1 0 0 1 0 2H8a2 2 0 0 1-2-1.6L4.3 5H2a1 1 0 0 1 0-2h4.4a1 1 0 0 1 .6.2ZM8.9 18.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm7.7 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/></svg>',
			'user'      => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 3a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9Zm0 11c4.4 0 8 2.2 8 5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1c0-2.8 3.6-5 8-5Z"/></svg>',
			'star'      => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="m12 2.6 2.9 5.9 6.5 1-4.7 4.6 1.1 6.5L12 17.5l-5.8 3.1 1.1-6.5L2.6 9.5l6.5-1L12 2.6Z"/></svg>',
			'spark'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2l1.9 5.6L19.5 9l-5.6 1.9L12 16.5l-1.9-5.6L4.5 9l5.6-1.4L12 2Zm6.5 11 1 2.9 2.9 1-2.9 1-1 2.9-1-2.9-2.9-1 2.9-1 1-2.9ZM5.5 12l.8 2.2 2.2.8-2.2.8L5.5 18l-.8-2.2L2.5 15l2.2-.8.8-2.2Z"/></svg>',
			'quote'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M9.5 5C6.5 6.6 4.5 9.6 4.5 13v6h6v-7H7.4c0-2.2 1.2-3.9 3.3-5L9.5 5Zm9.8 0c-3 1.6-5 4.6-5 8v6h6v-7h-3.1c0-2.2 1.2-3.9 3.3-5L19.3 5Z"/></svg>',
		);

		return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
	}
}
