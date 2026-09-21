<?php
/**
 * سازگاری با ووکامرس: پشتیبانی، حلقه محصولات و سبد خرید.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! novin_ai_is_woocommerce_active() ) {
	return;
}

if ( ! function_exists( 'novin_ai_woocommerce_setup' ) ) {
	/**
	 * اعلام پشتیبانی از ووکامرس.
	 *
	 * @return void
	 */
	function novin_ai_woocommerce_setup() {
		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width' => 600,
				'single_image_width'    => 900,
				'product_grid'          => array(
					'default_rows'    => 3,
					'min_rows'        => 2,
					'max_rows'        => 8,
					'default_columns' => (int) novin_ai_option( 'shop_columns', 3 ),
					'min_columns'     => 2,
					'max_columns'     => 5,
				),
			)
		);

		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
}
add_action( 'after_setup_theme', 'novin_ai_woocommerce_setup' );

if ( ! function_exists( 'novin_ai_woocommerce_wrapper_start' ) ) {
	/**
	 * شروع رپر اختصاصی فروشگاه.
	 *
	 * @return void
	 */
	function novin_ai_woocommerce_wrapper_start() {
		$classes = array( 'nv-shop', 'nv-section' );

		if ( is_product() ) {
			$classes[] = 'nv-shop--single';
		}

		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '"><div class="nv-container">';

		if ( ! is_product() ) {
			echo '<div class="nv-shop__layout">';
			if ( is_active_sidebar( 'sidebar-shop' ) && is_shop() ) {
				echo '<aside class="nv-shop__sidebar">';
				dynamic_sidebar( 'sidebar-shop' );
				echo '</aside>';
			}
			echo '<div class="nv-shop__main">';
		}
	}
}

if ( ! function_exists( 'novin_ai_woocommerce_wrapper_end' ) ) {
	/**
	 * پایان رپر اختصاصی فروشگاه.
	 *
	 * @return void
	 */
	function novin_ai_woocommerce_wrapper_end() {
		if ( ! is_product() ) {
			echo '</div><!-- .nv-shop__main -->';
			echo '</div><!-- .nv-shop__layout -->';
		}

		echo '</div></div>';
	}
}

// جایگزینی رپرهای پیش‌فرض ووکامرس.
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', 'novin_ai_woocommerce_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'novin_ai_woocommerce_wrapper_end', 10 );

if ( ! function_exists( 'novin_ai_woocommerce_loop_columns' ) ) {
	/**
	 * تعداد ستون‌های حلقه محصولات.
	 *
	 * @return int
	 */
	function novin_ai_woocommerce_loop_columns() {
		return max( 2, (int) novin_ai_option( 'shop_columns', 3 ) );
	}
}
add_filter( 'loop_shop_columns', 'novin_ai_woocommerce_loop_columns', 20 );

if ( ! function_exists( 'novin_ai_woocommerce_products_per_page' ) ) {
	/**
	 * تعداد محصولات هر صفحه.
	 *
	 * @return int
	 */
	function novin_ai_woocommerce_products_per_page() {
		return max( 3, (int) novin_ai_option( 'shop_per_page', 12 ) );
	}
}
add_filter( 'loop_shop_per_page', 'novin_ai_woocommerce_products_per_page', 20 );

if ( ! function_exists( 'novin_ai_woocommerce_breadcrumb' ) ) {
	/**
	 * تنظیمات breadcrumb ووکامرس.
	 *
	 * @param array<string, mixed> $args آرگومان‌ها.
	 * @return array<string, mixed>
	 */
	function novin_ai_woocommerce_breadcrumb( $args ) {
		$args['delimiter']   = '<span class="nv-breadcrumb__sep">/</span>';
		$args['wrap_before'] = '<nav class="nv-breadcrumb woocommerce-breadcrumb" aria-label="breadcrumb">';
		$args['wrap_after']  = '</nav>';

		return $args;
	}
}
add_filter( 'woocommerce_breadcrumb_defaults', 'novin_ai_woocommerce_breadcrumb' );

if ( ! function_exists( 'novin_ai_cart_fragments' ) ) {
	/**
	 * بروزرسانی شمارنده سبد خرید در هدر (AJAX).
	 *
	 * @param array<string, string> $fragments قطعات.
	 * @return array<string, string>
	 */
	function novin_ai_cart_fragments( $fragments ) {
		$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
		$total = WC()->cart ? WC()->cart->get_cart_total() : '';

		$fragments['.nv-icon-btn__count'] = '<span class="nv-icon-btn__count">' . esc_html( $count ) . '</span>';
		$fragments['.nv-cart-total']      = '<span class="nv-cart-total">' . wp_kses_post( $total ) . '</span>';

		return $fragments;
	}
}
add_filter( 'woocommerce_add_to_cart_fragments', 'novin_ai_cart_fragments' );

if ( ! function_exists( 'novin_ai_woocommerce_body_class' ) ) {
	/**
	 * کلاس‌های بدنه فروشگاه.
	 *
	 * @param array<int, string> $classes کلاس‌ها.
	 * @return array<int, string>
	 */
	function novin_ai_woocommerce_body_class( $classes ) {
		if ( is_woocommerce() || is_cart() || is_checkout() ) {
			$classes[] = 'nv-shop-page';
			$classes[] = 'nv-card-style-' . novin_ai_option( 'shop_card_style' );
		}

		return $classes;
	}
}
add_filter( 'body_class', 'novin_ai_woocommerce_body_class' );

if ( ! function_exists( 'novin_ai_woocommerce_product_query_controls' ) ) {
	/**
	 * کوئری‌های آماده برای المان محصولات.
	 *
	 * @param array<string, mixed> $args آرگومان‌های WP_Query.
	 * @return array<string, mixed>
	 */
	function novin_ai_woocommerce_product_query( $args ) {
		return $args;
	}
}
