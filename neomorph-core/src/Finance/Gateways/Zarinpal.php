<?php
/**
 * Zarinpal v4 REST gateway.
 *
 * @package NeomorphCore\Finance\Gateways
 */

namespace NeomorphCore\Finance\Gateways;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Zarinpal
 */
final class Zarinpal extends AbstractGateway {

	const API = 'https://api.zarinpal.com/pg/v4';

	public function id() {
		return 'zarinpal';
	}

	public function label() {
		return 'Zarinpal';
	}

	public function is_configured() {
		return (bool) nmc_setting( 'zarinpal_merchant', '' );
	}

	public function request_payment( $invoice_id, $return_url ) {
		if ( ! $this->is_configured() ) {
			return new \WP_Error( 'nmc_zp_config', esc_html__( 'مرچنت کد زرین‌پال تنظیم نشده است.', 'neomorph-core' ) );
		}
		$amount = (float) \nmc_get_meta( $invoice_id, 'total', 0 );
		$user_id = (int) \nmc_get_meta( $invoice_id, 'user_id' );

		$response = wp_remote_post(
			self::API . '/request.json',
			array(
				'timeout' => 20,
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => wp_json_encode(
					array(
						'merchant_id'  => nmc_setting( 'zarinpal_merchant' ),
						'amount'       => round( $amount * 10 ), // Rial (toman × 10).
						'currency'     => 'IRT',
						'description'  => get_the_title( $invoice_id ),
						'callback_url' => add_query_arg( array( 'nmc-gw' => 'zarinpal', 'invoice' => $invoice_id ), $return_url ),
						'metadata'     => array(
							'mobile' => \nmc_user_phone( $user_id ),
						),
					)
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( isset( $body['data']['authority'] ) ) {
			\nmc_update_meta( $invoice_id, 'gw_authority', $body['data']['authority'] );
			$base = nmc_setting( 'zarinpal_sandbox' ) ? 'https://sandbox.zarinpal.com/pg/StartPay/' : 'https://payment.zarinpal.com/pg/StartPay/';
			return $base . $body['data']['authority'];
		}
		return new \WP_Error( 'nmc_zp_failed', esc_html__( 'پاسخ نامعتبر از زرین‌پال.', 'neomorph-core' ) );
	}

	public function verify( $invoice_id ) {
		$authority = \nmc_get_meta( $invoice_id, 'gw_authority' );
		$amount    = (float) \nmc_get_meta( $invoice_id, 'total', 0 );
		$response  = wp_remote_post(
			self::API . '/verify.json',
			array(
				'timeout' => 20,
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => wp_json_encode(
					array(
						'merchant_id' => nmc_setting( 'zarinpal_merchant' ),
						'amount'      => round( $amount * 10 ),
						'authority'   => $authority,
					)
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			return false;
		}
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		$code = isset( $body['data']['code'] ) ? (int) $body['data']['code'] : 0;
		if ( in_array( $code, array( 100, 101 ), true ) ) {
			\nmc_update_meta( $invoice_id, 'gw_ref', isset( $body['data']['ref_id'] ) ? $body['data']['ref_id'] : '' );
			return true;
		}
		return false;
	}
}
