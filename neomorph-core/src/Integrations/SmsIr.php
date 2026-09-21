<?php
/**
 * sms.ir REST integration (OTP via verify service + bulk text).
 * Docs: https://api.sms.ir / X-API-KEY header.
 *
 * @package NeomorphCore\Integrations
 */

namespace NeomorphCore\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SmsIr
 */
final class SmsIr implements Provider {

	/**
	 * Send through sms.ir.
	 *
	 * @param string $message Message (for OTP pass just the code in message).
	 * @param string $to      Phone.
	 * @param array  $args    { purpose: otp|text, pattern_code, parameters }.
	 * @return true|\WP_Error
	 */
	public function send( $message, $to, $args = array() ) {
		$api_key = nmc_setting( 'smsir_api_key', '' );
		$line     = nmc_setting( 'smsir_line', '' );
		if ( ! $api_key ) {
			return new \WP_Error( 'nmc_smsir_config', esc_html__( 'کلید API سامانه sms.ir تنظیم نشده است.', 'neomorph-core' ) );
		}

		$to = \nmc_normalize_phone( $to );
		// sms.ir expects 09… or international; use 989… for verify endpoint.
		$to_intl = '98' . substr( $to, 1 );

		$purpose = isset( $args['purpose'] ) ? $args['purpose'] : ( isset( $args['pattern_code'] ) ? 'otp' : 'text' );

		if ( 'otp' === $purpose ) {
			$pattern_code = isset( $args['pattern_code'] ) ? $args['pattern_code'] : nmc_setting( 'smsir_pattern', '' );
			$payload      = array(
				'mobile'       => $to_intl,
				'patternCode'  => $pattern_code,
				'parameters'   => isset( $args['parameters'] ) ? $args['parameters'] : array(
					array( 'name' => 'code', 'value' => $message ),
				),
			);
			$endpoint = 'https://api.sms.ir/v1/send/verify';
		} else {
			$payload = array(
				'lineNumber' => (int) $line,
				'messageText' => $message,
				'mobiles'     => array( $to_intl ),
			);
			$endpoint = 'https://api.sms.ir/v1/send/bulk';
		}

		$response = wp_remote_post(
			$endpoint,
			array(
				'timeout' => 15,
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Accept'        => 'application/json',
					'X-API-KEY'     => $api_key,
				),
				'body'    => wp_json_encode( $payload ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 === (int) $code && isset( $body['status'] ) && 1 === (int) $body['status'] ) {
			return true;
		}
		return new \WP_Error(
			'nmc_smsir_failed',
			sprintf(
				/* translators: 1: status 2: message */
				esc_html__( 'خطای sms.ir (%1$s): %2$s', 'neomorph-core' ),
				isset( $body['status'] ) ? $body['status'] : $code,
				isset( $body['message'] ) ? $body['message'] : ''
			)
		);
	}
}
