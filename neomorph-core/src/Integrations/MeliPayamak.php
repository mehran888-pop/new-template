<?php
/**
 * ملی پیامک (Meli Payamak) REST integration.
 * Simple send:  /sms/send/simple/
 * Pattern:      /sms/mix/send/  (pattern code + dynamic parameters)
 *
 * @package NeomorphCore\Integrations
 */

namespace NeomorphCore\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MeliPayamak
 */
final class MeliPayamak implements Provider {

	/**
	 * Send through melipayamak REST.
	 *
	 * @param string $message Message or OTP code.
	 * @param string $to      Phone.
	 * @param array  $args    { purpose, pattern_code, parameters }.
	 * @return true|\WP_Error
	 */
	public function send( $message, $to, $args = array() ) {
		$username = nmc_setting( 'meli_username', '' );
		$password = nmc_setting( 'meli_password', '' );
		$line     = nmc_setting( 'meli_line', '' );
		if ( ! $username || ! $password ) {
			return new \WP_Error( 'nmc_meli_config', esc_html__( 'نام کاربری/رمز ملی پیامک تنظیم نشده است.', 'neomorph-core' ) );
		}

		$to = \nmc_normalize_phone( $to );

		$purpose = isset( $args['purpose'] ) ? $args['purpose'] : ( isset( $args['pattern_code'] ) ? 'otp' : 'text' );

		if ( 'otp' === $purpose ) {
			$payload = array(
				'from'         => $line,
				'to'           => $to,
				'text'         => $message,
				'isflash'      => false,
				'patternCode'  => isset( $args['pattern_code'] ) ? $args['pattern_code'] : nmc_setting( 'meli_pattern', '' ),
				'patternValues' => isset( $args['parameters'] ) ? $args['parameters'] : array( 'code' => $message ),
			);
			$endpoint = 'https://api.melipayamak.com/sms/mix/send/' . rawurlencode( $username ) . '/' . rawurlencode( $password );
		} else {
			$payload = array(
				'from'    => $line,
				'to'      => $to,
				'text'    => $message,
				'isflash' => false,
			);
			$endpoint = 'https://api.melipayamak.com/sms/send/simple/' . rawurlencode( $username ) . '/' . rawurlencode( $password );
		}

		$response = wp_remote_post(
			$endpoint,
			array(
				'timeout' => 15,
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => wp_json_encode( $payload ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( isset( $body['status'] ) && ( 'ارسال موفق' === $body['status'] || 1 === (int) $body['status'] || '1' === (string) $body['status'] ) ) {
			return true;
		}
		if ( isset( $body['recId'] ) && $body['recId'] ) {
			return true;
		}
		return new \WP_Error(
			'nmc_meli_failed',
			sprintf(
				/* translators: %s: error detail */
				esc_html__( 'خطای ملی پیامک: %s', 'neomorph-core' ),
				isset( $body['status'] ) ? ( is_scalar( $body['status'] ) ? $body['status'] : wp_json_encode( $body['status'] ) ) : wp_remote_retrieve_response_code( $response )
			)
		);
	}
}
