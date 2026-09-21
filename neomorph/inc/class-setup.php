<?php
/**
 * Theme setup: supports, menus, sidebars, image sizes.
 *
 * @package Neomorph
 */

namespace Neomorph;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Setup
 */
final class Setup {

	/**
	 * Hook everything.
	 */
	public static function init() {
		add_action( 'after_setup_theme', array( __CLASS__, 'setup' ) );
		add_action( 'widgets_init', array( __CLASS__, 'widgets' ) );
		add_action( 'wp_body_open', array( __CLASS__, 'skip_link' ) );
		add_filter( 'body_class', array( __CLASS__, 'body_classes' ) );
	}

	/**
	 * Theme supports.
	 */
	public static function setup() {
		load_theme_textdomain( 'neomorph', NEOMORPH_DIR . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'wp-block-styles' );

		add_theme_support(
			'html5',
			array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
		);

		add_theme_support(
			'custom-logo',
			array(
				'height'      => 64,
				'width'       => 200,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		add_theme_support(
			'custom-background',
			array(
				'default-color' => 'e8edf5',
			)
		);

		add_theme_support( 'woocommerce', array(
			'thumbnail_image_width' => 420,
			'single_image_width'    => 720,
			'product_grid'         => array(
				'default_columns' => 3,
				'min_columns'     => 2,
				'max_columns'     => 4,
			),
		) );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		// Elementor full-width / canvas locations.
		add_theme_support( 'elementor' );
		add_theme_support( 'elementor-pro' );
		add_theme_support( 'header-footer-elementor' );

		register_nav_menus(
			array(
				'primary' => esc_html__( 'منوی اصلی', 'neomorph' ),
				'mobile'  => esc_html__( 'منوی موبایل', 'neomorph' ),
				'footer'  => esc_html__( 'منوی فوتر', 'neomorph' ),
			)
		);

		add_image_size( 'neomorph-card', 640, 420, true );
		add_image_size( 'neomorph-team', 480, 560, true );

		// RTL: always load logical-properties friendly styles (theme ships RTL-first).
		add_editor_style( 'assets/css/editor.css' );
	}

	/**
	 * Widget areas.
	 */
	public static function widgets() {
		register_sidebar(
			array(
				'name'          => esc_html__( 'سایدبار اصلی', 'neomorph' ),
				'id'            => 'sidebar-main',
				'description'   => esc_html__( 'سایدبار صفحات داخلی و وبلاگ', 'neomorph' ),
				'before_widget' => '<section id="%1$s" class="neo-surface widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
		register_sidebar(
			array(
				'name'          => esc_html__( 'فوتر — ستون ۱', 'neomorph' ),
				'id'            => 'footer-1',
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
		register_sidebar(
			array(
				'name'          => esc_html__( 'فوتر — ستون ۲', 'neomorph' ),
				'id'            => 'footer-2',
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
		register_sidebar(
			array(
				'name'          => esc_html__( 'فوتر — ستون ۳', 'neomorph' ),
				'id'            => 'footer-3',
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}

	/**
	 * Skip link for a11y.
	 */
	public static function skip_link() {
		echo '<a class="skip-link screen-reader-text" href="#content">' . esc_html__( 'پرش به محتوا', 'neomorph' ) . '</a>';
	}

	/**
	 * Extra body classes driven by options.
	 *
	 * @param array $classes Classes.
	 * @return array
	 */
	public static function body_classes( $classes ) {
		$classes[] = 'neomorph-theme';
		$classes[] = 'neo-layout-' . sanitize_html_class( neomorph_option( 'site_layout', 'wide' ) );
		$classes[] = 'neo-style-' . sanitize_html_class( neomorph_option( 'soft_style', 'light' ) );
		if ( is_rtl() ) {
			$classes[] = 'rtl';
		}
		if ( class_exists( 'WooCommerce' ) && is_woocommerce() ) {
			$classes[] = 'neo-woo';
		}
		return $classes;
	}
}

Setup::init();
