<?php
/**
 * IDPay gateway (https://api.idpay.ir).
 *
 * @package NeomorphCore\Finance\Gateways
 */

namespace NeomorphCore\Finance\Gateways;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Idpay
 */
final class Idpay extends AbstractGateway {

	public function id() {
		return 'idpay';
	}

	public function label() {
		return 'IDPay';
	}

	public function is_configured() {
		return (bool) nmc_setting( 'idpay_api_key', '' );
	}

	public function request_payment( $invoice_id, $return_url ) {
		if ( ! $this->is_configured() ) {
			return new \WP_Error( 'nmc_idpay_config', esc_html__( 'کلید API آی‌دی‌پی تنظیم نشده است.', 'neomorph-core' ) );
		}
		$amount  = (float) \nmc_get_meta( $invoice_id, 'total', 0 );
		$user_id = (int) \nmc_get_meta( $invoice_id, 'user_id' );

		$response = wp_remote_post(
			'https://api.idpay.ir/v1.1/payment',
			array(
				'timeout' => 20,
				'headers' => array(
					'Content-Type' => 'application/json',
					'X-API-KEY'    => nmc_setting( 'idpay_api_key' ),
					'X-SANDBOX'    => nmc_setting( 'idpay_sandbox' ) ? '1' : '0',
				),
				'body'    => wp_json_encode(
					array(
						'order_id'   => \nmc_get_meta( $invoice_id, 'number' ),
						'amount'     => round( $amount * 10 ),
						'callback'   => add_query_arg( array( 'nmc-gw' => 'idpay', 'invoice' => $invoice_id ), $return_url ),
						'desc'       => get_the_title( $invoice_id ),
						'mobile'     => \nmc_user_phone( $user_id ),
					)
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( isset( $body['id'], $body['link'] ) ) {
			\nmc_update_meta( $invoice_id, 'gw_authority', $body['id'] );
			return $body['link'];
		}
		return new \WP_Error( 'nmc_idpay_failed', esc_html__( 'پاسخ نامعتبر از آی‌دی‌پی.', 'neomorph-core' ) );
	}

	public function verify( $invoice_id ) {
		$response = wp_remote_post(
			'https://api.idpay.ir/v1.1/payment/verify',
			array(
				'timeout' => 20,
				'headers' => array(
					'Content-Type' => 'application/json',
					'X-API-KEY'    => nmc_setting( 'idpay_api_key' ),
					'X-SANDBOX'    => nmc_setting( 'idpay_sandbox' ) ? '1' : '0',
				),
				'body'    => wp_json_encode(
					array(
						'id'       => \nmc_get_meta( $invoice_id, 'gw_authority' ),
						'order_id' => \nmc_get_meta( $invoice_id, 'number' ),
					)
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			return false;
		}
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( isset( $body['status'] ) && in_array( (int) $body['status'], array( 100, 200 ), true ) ) {
			\nmc_update_meta( $invoice_id, 'gw_ref', isset( $body['track_id'] ) ? $body['track_id'] : '' );
			return true;
		}
		return false;
	}
}
