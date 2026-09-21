<?php
/**
 * Bale (بله) Bot API — messages to a user or the admin channel.
 * API: https://tapi.bale.ai/bot{token}/sendMessage
 *
 * @package NeomorphCore\Integrations
 */

namespace NeomorphCore\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Bale
 */
final class Bale implements Provider {

	/**
	 * Send a Bale message.
	 *
	 * @param string $message Text.
	 * @param string $to      Chat id (defaults to configured admin chat).
	 * @return true|\WP_Error
	 */
	public function send( $message, $to = '', $args = array() ) {
		$token = nmc_setting( 'bale_token', '' );
		if ( ! $token ) {
			return new \WP_Error( 'nmc_bale_config', esc_html__( 'توکن ربات بله تنظیم نشده است.', 'neomorph-core' ) );
		}
		$chat_id = $to ? $to : nmc_setting( 'bale_chat_id', '' );
		if ( ! $chat_id ) {
			return new \WP_Error( 'nmc_bale_chat', esc_html__( 'شناسه گفتگوی بله تنظیم نشده است.', 'neomorph-core' ) );
		}

		$response = wp_remote_post(
			sprintf( 'https://tapi.bale.ai/bot%s/sendMessage', $token ),
			array(
				'timeout' => 15,
				'body'    => array(
					'chat_id'    => $chat_id,
					'text'       => $message,
					'parse_mode' => 'HTML',
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( isset( $body['ok'] ) && $body['ok'] ) {
			return true;
		}
		return new \WP_Error( 'nmc_bale_failed', esc_html__( 'ارسال پیام بله ناموفق بود.', 'neomorph-core' ) );
	}
}
