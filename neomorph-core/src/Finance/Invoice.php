<?php
/**
 * Invoice model (CPT nmc_invoice + meta).
 *
 * Meta (_nmc_*):
 *  token, user_id, status (draft|unpaid|paid|cancelled|expired),
 *  items (serialized array: title, qty, price, total), subtotal, discount, tax, total,
 *  due_date, paid_at, gateway, gateway_ref, description
 *
 * @package NeomorphCore\Finance
 */

namespace NeomorphCore\Finance;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Invoice
 */
final class Invoice {

	/**
	 * Create an invoice. Returns post ID.
	 *
	 * @param int   $user_id Customer user id.
	 * @param array $items   Line items: [ ['title','qty','price','total'] ].
	 * @param array $args    { discount, tax_percent, description, due_date, title, status }.
	 * @return int
	 */
	public static function create( $user_id, $items, $args = array() ) {
		$subtotal = 0;
		$clean    = array();
		foreach ( (array) $items as $item ) {
			$price = isset( $item['price'] ) ? (float) $item['price'] : 0;
			$qty   = isset( $item['qty'] ) ? max( 1, (int) $item['qty'] ) : 1;
			$total = isset( $item['total'] ) ? (float) $item['total'] : $price * $qty;
			$clean[] = array(
				'title' => isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : '',
				'qty'   => $qty,
				'price' => $price,
				'total' => $total,
			);
			$subtotal += $total;
		}

		$discount    = isset( $args['discount'] ) ? (float) $args['discount'] : 0;
		$tax_percent = isset( $args['tax_percent'] ) ? (float) $args['tax_percent'] : 0;
		$tax         = $tax_percent > 0 ? ( $subtotal - $discount ) * $tax_percent / 100 : 0;
		$total       = max( 0, $subtotal - $discount + $tax );

		$number = self::next_number();

		$post_id = wp_insert_post(
			array(
				'post_type'   => 'nmc_invoice',
				'post_status' => 'publish',
				'post_title'  => sprintf( 'فاکتور %s', $number ),
				'post_author' => $user_id,
				'post_content' => isset( $args['description'] ) ? wp_kses_post( $args['description'] ) : '',
			)
		);
		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return 0;
		}

		\nmc_update_meta( $post_id, 'number', $number );
		\nmc_update_meta( $post_id, 'token', \nmc_generate_token( 40 ) );
		\nmc_update_meta( $post_id, 'user_id', (int) $user_id );
		\nmc_update_meta( $post_id, 'status', isset( $args['status'] ) ? sanitize_key( $args['status'] ) : 'unpaid' );
		\nmc_update_meta( $post_id, 'items', $clean );
		\nmc_update_meta( $post_id, 'subtotal', $subtotal );
		\nmc_update_meta( $post_id, 'discount', $discount );
		\nmc_update_meta( $post_id, 'tax', $tax );
		\nmc_update_meta( $post_id, 'total', $total );
		\nmc_update_meta( $post_id, 'due_date', isset( $args['due_date'] ) ? sanitize_text_field( $args['due_date'] ) : '' );
		\nmc_update_meta( $post_id, 'created_at', current_time( 'mysql' ) );

		\nmc_do_event( 'invoice_created', array( 'post_id' => $post_id, 'user_id' => $user_id, 'amount' => $total ) );

		return $post_id;
	}

	/**
	 * Sequential invoice number: NMC-1001…
	 */
	public static function next_number() {
		$seq = (int) get_option( 'nmc_invoice_seq', 1000 );
		update_option( 'nmc_invoice_seq', $seq + 1, false );
		return 'NMC-' . $seq;
	}

	/**
	 * Public payment URL for invoice token.
	 */
	public static function payment_url( $post_id ) {
		$token = \nmc_get_meta( $post_id, 'token' );
		$page  = (int) nmc_setting( 'invoice_page', 0 );
		$base  = $page ? get_permalink( $page ) : home_url( '/' );
		return add_query_arg( 'nmc-invoice', rawurlencode( $token ), $base );
	}

	/**
	 * Mark invoice paid.
	 */
	public static function mark_paid( $post_id, $gateway = '', $ref = '' ) {
		if ( 'paid' === \nmc_get_meta( $post_id, 'status' ) ) {
			return;
		}
		\nmc_update_meta( $post_id, 'status', 'paid' );
		\nmc_update_meta( $post_id, 'paid_at', current_time( 'mysql' ) );
		\nmc_update_meta( $post_id, 'gateway', $gateway );
		\nmc_update_meta( $post_id, 'gateway_ref', $ref );

		$user_id = (int) \nmc_get_meta( $post_id, 'user_id' );
		$total   = (float) \nmc_get_meta( $post_id, 'total', 0 );

		\nmc_do_event( 'invoice_paid', array( 'post_id' => $post_id, 'user_id' => $user_id, 'amount' => $total, 'ref' => $ref ) );
	}

	/**
	 * Find invoice ID by token.
	 */
	public static function id_by_token( $token ) {
		$ids = get_posts(
			array(
				'post_type'   => 'nmc_invoice',
				'post_status' => array( 'publish', 'draft' ),
				'meta_key'    => '_nmc_token', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'  => sanitize_text_field( $token ), // phpcs:ignore WordPress.DB.SlowDBQuery
				'numberposts' => 1,
				'fields'      => 'ids',
			)
		);
		return $ids ? (int) $ids[0] : 0;
	}

	/**
	 * Build invoice from a WooCommerce order (used by automation glue).
	 */
	public static function from_order( $order_id ) {
		if ( ! function_exists( 'wc_get_order' ) ) {
			return 0;
		}
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return 0;
		}
		$items = array();
		foreach ( $order->get_items() as $item ) {
			$items[] = array(
				'title' => $item->get_name(),
				'qty'   => $item->get_quantity(),
				'price' => (float) $item->get_subtotal() / max( 1, $item->get_quantity() ),
				'total' => (float) $item->get_total(),
			);
		}
		return self::create(
			$order->get_customer_id() ? $order->get_customer_id() : get_current_user_id(),
			$items,
			array(
				'description' => sprintf( 'سفارش فروشگاه #%d', $order_id ),
				'title'       => 'فاکتور سفارش',
				'discount'    => (float) $order->get_discount_total(),
				'status'      => $order->is_paid() ? 'paid' : 'unpaid',
			)
		);
	}
}
