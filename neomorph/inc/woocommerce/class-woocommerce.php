<?php
/**
 * WooCommerce integration: layouts from theme options, card styles, catalog mode.
 *
 * @package Neomorph
 */

namespace Neomorph;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WooCommerce
 */
final class WooCommerce {

	/**
	 * Hooks.
	 */
	public static function init() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		add_filter( 'loop_shop_columns', array( __CLASS__, 'columns' ) );
		add_filter( 'body_class', array( __CLASS__, 'body_classes' ) );
		add_filter( 'woocommerce_product_loop_start', array( __CLASS__, 'loop_start' ) );
		add_filter( 'post_class', array( __CLASS__, 'product_card_class' ), 20, 3 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'shop_columns_var' ), 30 );
		add_filter( 'woocommerce_show_page_title', '__return_false' );

		// Catalog mode.
		if ( neomorph_option( 'catalog_mode', '0' ) ) {
			add_filter( 'woocommerce_is_purchasable', '__return_false', 10, 2 );
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		}

		// Product single layout wrapper.
		add_filter( 'woocommerce_product_class', array( __CLASS__, 'noop' ), 10, 2 );
		add_action( 'woocommerce_before_single_product_summary', array( __CLASS__, 'open_layout' ), 5 );
		add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'close_layout' ), 5 );
	}

	/**
	 * Shop columns from options.
	 */
	public static function columns() {
		return (int) neomorph_option( 'shop_columns', 3 );
	}

	/**
	 * Inline CSS var for grid.
	 */
	public static function shop_columns_var() {
		wp_add_inline_style( 'neomorph-woo', ':root{--neo-shop-cols:' . (int) neomorph_option( 'shop_columns', 3 ) . ';}' );
	}

	/**
	 * Body classes for product layout + card style.
	 */
	public static function body_classes( $classes ) {
		if ( is_product() ) {
			$layout = neomorph_option( 'product_layout', 'gallery-right' );
			$map    = array(
				'gallery-right'  => 'product-gallery-right',
				'gallery-left'   => 'product-gallery-left',
				'gallery-center' => 'product-gallery-center',
				'sticky-info'    => 'sticky-info',
			);
			$classes[] = 'neo-product-layout';
			$classes[] = isset( $map[ $layout ] ) ? $map[ $layout ] : 'product-gallery-right';
		}
		if ( is_shop() || is_product_taxonomy() ) {
			$classes[] = 'neo-shop-card--' . sanitize_html_class( neomorph_option( 'product_card_style', 'raised' ) );
		}
		return $classes;
	}

	/**
	 * Open product layout wrapper classes on <div class="product"> via JS-free approach:
	 * WooCommerce wraps summary/upsells; we simply add classes through body_class (above).
	 */
	public static function open_layout() {}
	public static function close_layout() {}
	public static function noop( $classname ) {
		return $classname;
	}

	/**
	 * Add neo card class to loop product items.
	 */
	public static function product_card_class( $classes, $class, $post_id ) {
		if ( 'product' === get_post_type( $post_id ) && ( is_shop() || is_product_taxonomy() || is_product() ) ) {
			$classes[] = 'neo-product-card';
			$classes[] = 'neo-product-card--' . sanitize_html_class( neomorph_option( 'product_card_style', 'raised' ) );
		}
		return $classes;
	}

	/**
	 * Loop start adds grid wrapper class (products ul handled in CSS).
	 */
	public static function loop_start( $html ) {
		return $html;
	}
}

WooCommerce::init();
