<?php
/**
 * Breadcrumbs (neumorphic pills).
 *
 * @package Neomorph
 */

namespace Neomorph;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Breadcrumbs
 */
final class Breadcrumbs {

	/**
	 * Print breadcrumb trail.
	 */
	public static function render() {
		if ( is_front_page() || ! neomorph_option( 'show_breadcrumbs', '1' ) ) {
			return;
		}
		$items   = array();
		$items[] = array(
			'label' => esc_html__( 'خانه', 'neomorph' ),
			'url'   => home_url( '/' ),
		);

		if ( is_singular( 'post' ) ) {
			$cats = get_the_category();
			if ( $cats ) {
				$items[] = array(
					'label' => $cats[0]->name,
					'url'   => get_category_link( $cats[0] ),
				);
			}
			$items[] = array(
				'label' => get_the_title(),
				'url'   => '',
			);
		} elseif ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() || is_product() ) ) {
			$items[] = array(
				'label' => function_exists( 'wc_get_page_id' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : esc_html__( 'فروشگاه', 'neomorph' ),
				'url'   => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '',
			);
			if ( is_product() ) {
				$items[] = array(
					'label' => get_the_title(),
					'url'   => '',
				);
			}
		} elseif ( is_page() ) {
			$items[] = array(
				'label' => get_the_title(),
				'url'   => '',
			);
		} elseif ( is_archive() ) {
			$items[] = array(
				'label' => wp_strip_all_tags( get_the_archive_title() ),
				'url'   => '',
			);
		} elseif ( is_search() ) {
			$items[] = array(
				'label' => esc_html__( 'جستجو', 'neomorph' ),
				'url'   => '',
			);
		}

		echo '<nav class="neo-breadcrumbs" aria-label="' . esc_attr__( 'مسیر راهنما', 'neomorph' ) . '"><ol class="neo-breadcrumbs__list">';
		foreach ( $items as $i => $item ) {
			echo '<li class="neo-breadcrumbs__item">';
			if ( $item['url'] && $i < count( $items ) - 1 ) {
				printf( '<a href="%s">%s</a>', esc_url( $item['url'] ), esc_html( $item['label'] ) );
			} else {
				printf( '<span aria-current="page">%s</span>', esc_html( $item['label'] ) );
			}
			echo '</li>';
		}
		echo '</ol></nav>';
	}
}
