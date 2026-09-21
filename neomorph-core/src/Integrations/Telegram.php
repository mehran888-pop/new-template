<?php
/**
 * Telegram Bot API — sendMessage to admin channel/chat.
 *
 * @package NeomorphCore\Integrations
 */

namespace NeomorphCore\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Telegram
 */
final class Telegram implements Provider {

	/**
	 * Send a Telegram message.
	 *
	 * @param string $message Text.
	 * @param string $to      Chat id (defaults to configured admin chat/channel).
	 * @return true|\WP_Error
	 */
	public function send( $message, $to = '', $args = array() ) {
		$token = nmc_setting( 'telegram_token', '' );
		if ( ! $token ) {
			return new \WP_Error( 'nmc_tg_config', esc_html__( 'توکن ربات تلگرام تنظیم نشده است.', 'neomorph-core' ) );
		}
		$chat_id = $to ? $to : nmc_setting( 'telegram_chat_id', '' );
		if ( ! $chat_id ) {
			return new \WP_Error( 'nmc_tg_chat', esc_html__( 'شناسه چت تلگرام تنظیم نشده است.', 'neomorph-core' ) );
		}

		$response = wp_remote_post(
			sprintf( 'https://api.telegram.org/bot%s/sendMessage', $token ),
			array(
				'timeout' => 15,
				'body'    => array(
					'chat_id'    => $chat_id,
					'text'       => $message,
					'parse_mode' => 'HTML',
					'disable_web_page_preview' => true,
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
		return new \WP_Error(
			'nmc_tg_failed',
			sprintf(
				/* translators: %s: detail */
				esc_html__( 'ارسال تلگرام ناموفق: %s', 'neomorph-core' ),
				isset( $body['description'] ) ? $body['description'] : ''
			)
		);
	}
}
