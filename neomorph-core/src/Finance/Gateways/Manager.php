<?php
/**
 * Gateway manager — pay buttons, redirect handling, verification.
 *
 * @package NeomorphCore\Finance\Gateways
 */

namespace NeomorphCore\Finance\Gateways;

use NeomorphCore\Finance\Invoice;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Manager
 */
final class Manager {

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'admin_post_nmc_pay_invoice', array( __CLASS__, 'handle_pay' ) );
		add_action( 'admin_post_nopriv_nmc_pay_invoice', array( __CLASS__, 'handle_pay' ) );
	}

	/**
	 * Registered gateways.
	 *
	 * @return AbstractGateway[]
	 */
	public static function gateways() {
		return apply_filters(
			'nmc_payment_gateways',
			array(
				new Zarinpal(),
				new Idpay(),
			)
		);
	}

	/**
	 * Active gateway instance.
	 */
	public static function active() {
		$id = nmc_setting( 'payment_gateway', 'zarinpal' );
		foreach ( self::gateways() as $gateway ) {
			if ( $gateway->id() === $id ) {
				return $gateway;
			}
		}
		return null;
	}

	/**
	 * Render the pay button block for an invoice.
	 */
	public static function render_pay_button( $invoice_id ) {
		$gateway = self::active();
		if ( ! $gateway || ! $gateway->is_configured() || 'manual' === nmc_setting( 'payment_gateway' ) ) {
			return '<p class="neo-inset">' . esc_html__( 'پرداخت آنلاین غیرفعال است. مبلغ را کارت‌به‌کارت کنید و رسید را از طریق تیکت ارسال نمایید.', 'neomorph-core' ) . '</p>';
		}
		return sprintf(
			'<a class="neo-btn neo-btn--primary" href="%s">%s — %s</a>',
			esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=nmc_pay_invoice&invoice=' . $invoice_id ), 'nmc_pay_' . $invoice_id ) ),
			esc_html__( 'پرداخت آنلاین', 'neomorph-core' ),
			esc_html( $gateway->label() )
		);
	}

	/**
	 * Handle pay button: redirect to gateway.
	 */
	public static function handle_pay() {
		$invoice_id = isset( $_GET['invoice'] ) ? (int) $_GET['invoice'] : 0;
		check_admin_referer( 'nmc_pay_' . $invoice_id );

		$gateway = self::active();
		if ( ! $gateway ) {
			wp_die( esc_html__( 'درگاه پرداخت تنظیم نشده است.', 'neomorph-core' ) );
		}
		$payment_url = $gateway->request_payment( $invoice_id, home_url( '/' ) );
		if ( is_wp_error( $payment_url ) ) {
			wp_die( esc_html( $payment_url->get_error_message() ) );
		}
		wp_redirect( $payment_url ); // phpcs:ignore WordPress.Security.SafeRedirect
		exit;
	}

	/**
	 * Verify on return from gateway.
	 */
	public static function verify_return( $invoice_id ) {
		$gw_id    = isset( $_GET['nmc-gw'] ) ? sanitize_key( $_GET['nmc-gw'] ) : '';
		$gateways = self::gateways();
		foreach ( $gateways as $gateway ) {
			if ( $gateway->id() === $gw_id ) {
				$ok = $gateway->verify( $invoice_id );
				if ( $ok ) {
					Invoice::mark_paid( $invoice_id, $gw_id, \nmc_get_meta( $invoice_id, 'gw_ref' ) );
				}
				return $ok;
			}
		}
		return false;
	}
}

Manager::init();
