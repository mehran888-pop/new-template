<?php
/**
 * راه‌اندازی قالب: پشتیبانی‌ها، منوها، سایدبارها، اندازه تصاویر.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'novin_ai_setup' ) ) {
	/**
	 * تنظیمات پایه قالب.
	 *
	 * @return void
	 */
	function novin_ai_setup() {
		// بارگذاری ترجمه‌ها.
		load_theme_textdomain( 'novin-ai', NOVIN_AI_DIR . 'languages' );

		// پشتیبانی‌های هسته.
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support(
			'html5',
			array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
		);
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 60,
				'width'       => 200,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);
		add_theme_support(
			'custom-background',
			array(
				'default-color' => novin_ai_option( 'color_bg' ),
			)
		);

		// استایل ویرایشگر گوتنبرگ مطابق ظاهر قالب.
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/editor.css' );

		// پالت رنگ ویرایشگر هماهنگ با CSS Variables قالب.
		add_theme_support(
			'editor-color-palette',
			array(
				array(
					'name'  => esc_html__( 'اصلی', 'novin-ai' ),
					'slug'  => 'primary',
					'color' => novin_ai_option( 'color_primary' ),
				),
				array(
					'name'  => esc_html__( 'فرعی', 'novin-ai' ),
					'slug'  => 'secondary',
					'color' => novin_ai_option( 'color_secondary' ),
				),
				array(
					'name'  => esc_html__( 'تأکیدی', 'novin-ai' ),
					'slug'  => 'accent',
					'color' => novin_ai_option( 'color_accent' ),
				),
				array(
					'name'  => esc_html__( 'تیره', 'novin-ai' ),
					'slug'  => 'dark',
					'color' => novin_ai_option( 'color_bg' ),
				),
				array(
					'name'  => esc_html__( 'سطح', 'novin-ai' ),
					'slug'  => 'surface',
					'color' => novin_ai_option( 'color_bg_alt' ),
				),
				array(
					'name'  => esc_html__( 'متن', 'novin-ai' ),
					'slug'  => 'text',
					'color' => novin_ai_option( 'color_text' ),
				),
				array(
					'name'  => esc_html__( 'کم‌رنگ', 'novin-ai' ),
					'slug'  => 'muted',
					'color' => novin_ai_option( 'color_muted' ),
				),
			)
		);

		// منوها.
		register_nav_menus(
			array(
				'primary' => esc_html__( 'منوی اصلی', 'novin-ai' ),
				'topbar'  => esc_html__( 'منوی نوار بالا', 'novin-ai' ),
				'mobile'  => esc_html__( 'منوی موبایل', 'novin-ai' ),
				'footer'  => esc_html__( 'منوی فوتر', 'novin-ai' ),
				'footer_2' => esc_html__( 'منوی فوتر (ستون دوم)', 'novin-ai' ),
			)
		);

		// اندازه‌های تصویر برای کارت‌ها.
		add_image_size( 'novin-ai-card', 900, 620, true );
		add_image_size( 'novin-ai-square', 720, 720, true );
		add_image_size( 'novin-ai-portrait', 620, 780, true );
		add_image_size( 'novin-ai-wide', 1400, 780, true );
	}
}
add_action( 'after_setup_theme', 'novin_ai_setup' );

if ( ! function_exists( 'novin_ai_content_width' ) ) {
	/**
	 * عرض محتوای پیش‌فرض.
	 *
	 * @return void
	 */
	function novin_ai_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'novin_ai_content_width', (int) novin_ai_option( 'container_width', 1240 ) );
	}
}
add_action( 'after_setup_theme', 'novin_ai_content_width', 0 );

if ( ! function_exists( 'novin_ai_widgets_init' ) ) {
	/**
	 * ثبت سایدبارها (فالبک زمانی که المنتور استفاده نشود).
	 *
	 * @return void
	 */
	function novin_ai_widgets_init() {
		register_sidebar(
			array(
				'name'          => esc_html__( 'سایدبار وبلاگ', 'novin-ai' ),
				'id'            => 'sidebar-blog',
				'description'   => esc_html__( 'ویجت‌های ستون کناری بلاگ.', 'novin-ai' ),
				'before_widget' => '<section id="%1$s" class="nv-widget nv-glass %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="nv-widget__title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => esc_html__( 'سایدبار فروشگاه', 'novin-ai' ),
				'id'            => 'sidebar-shop',
				'description'   => esc_html__( 'ویجت‌های فیلتر و دسته‌بندی فروشگاه.', 'novin-ai' ),
				'before_widget' => '<section id="%1$s" class="nv-widget nv-glass %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="nv-widget__title">',
				'after_title'   => '</h3>',
			)
		);

		for ( $i = 1; $i <= 4; $i++ ) {
			register_sidebar(
				array(
					/* translators: %d: شماره ستون فوتر. */
					'name'          => sprintf( esc_html__( 'ستون %d فوتر', 'novin-ai' ), $i ),
					'id'            => 'footer-' . $i,
					'description'   => esc_html__( 'در صورت استفاده از المان فوتر اختصاصی نیازی به این بخش نیست.', 'novin-ai' ),
					'before_widget' => '<div id="%1$s" class="nv-widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h4 class="nv-widget__title">',
					'after_title'   => '</h4>',
				)
			);
		}
	}
}
add_action( 'widgets_init', 'novin_ai_widgets_init' );

if ( ! function_exists( 'novin_ai_body_classes' ) ) {
	/**
	 * کلاس‌های بدنه.
	 *
	 * @param array<int, string> $classes کلاس‌ها.
	 * @return array<int, string>
	 */
	function novin_ai_body_classes( $classes ) {
		$classes[] = 'nv-body';
		$classes[] = 'nv-theme-dark';

		if ( is_rtl() ) {
			$classes[] = 'nv-rtl';
		}

		if ( novin_ai_is_elementor_active() ) {
			$classes[] = 'nv-elementor';
		}

		if ( novin_ai_is_woocommerce_active() ) {
			$classes[] = 'nv-woocommerce';
		}

		if ( ! is_singular() && ! is_404() ) {
			$classes[] = 'hfeed';
		}

		return $classes;
	}
}
add_filter( 'body_class', 'novin_ai_body_classes' );
