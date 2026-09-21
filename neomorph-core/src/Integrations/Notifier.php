<?php
/**
 * Notifier — channel dispatcher (sms / bale / telegram / admin / email).
 *
 * @package NeomorphCore\Integrations
 */

namespace NeomorphCore\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Notifier
 */
final class Notifier {

	/**
	 * Hooks (provider instances).
	 */
	public static function init() {
		// Reserved for third-party provider registration.
		add_filter( 'nmc_sms_provider', array( __CLASS__, 'default_sms_provider' ), 5 );
	}

	/**
	 * Choose sms.ir or ملی پیامک based on settings.
	 *
	 * @return Provider
	 */
	public static function default_sms_provider() {
		$gateway = nmc_setting( 'sms_gateway', 'smsir' );
		return 'meli' === $gateway ? new MeliPayamak() : new SmsIr();
	}

	/**
	 * Send to a channel.
	 *
	 * @param string $channel sms|bale|telegram|admin|email.
	 * @param string $message Message body.
	 * @param string $to      Phone / chat id / email (optional per channel).
	 * @param array  $args    Extra.
	 * @return true|\WP_Error
	 */
	public static function send( $channel, $message, $to = '', $args = array() ) {
		switch ( $channel ) {
			case 'sms':
				$provider = apply_filters( 'nmc_sms_provider', null );
				if ( ! $provider instanceof Provider ) {
					$provider = self::default_sms_provider();
				}
				$result = $provider->send( $message, $to, $args );
				break;

			case 'bale':
				$result = ( new Bale() )->send( $message, $to, $args );
				break;

			case 'telegram':
				$result = ( new Telegram() )->send( $message, $to, $args );
				break;

			case 'admin':
				// Mirror to all configured admin channels; success if at least one works.
				$results = array();
				if ( nmc_setting( 'bale_token' ) ) {
					$results[] = ( new Bale() )->send( $message, '', $args );
				}
				if ( nmc_setting( 'telegram_token' ) ) {
					$results[] = ( new Telegram() )->send( $message, '', $args );
				}
				$admin_email = get_option( 'admin_email' );
				$headers     = array( 'Content-Type: text/plain; charset=UTF-8' );
				wp_mail( $admin_email, '[Neomorph] ' . wp_strip_all_tags( mb_substr( $message, 0, 60 ) ), $message, $headers );
				$ok = false;
				foreach ( $results as $r ) {
					if ( ! is_wp_error( $r ) ) {
						$ok = true;
					}
				}
				$result = ( $ok || empty( $results ) ) ? true : end( $results );
				break;

			case 'email':
				$sent = wp_mail(
					$to ? $to : get_option( 'admin_email' ),
					'[Neomorph] ' . wp_strip_all_tags( mb_substr( $message, 0, 60 ) ),
					$message,
					array( 'Content-Type: text/plain; charset=UTF-8' )
				);
				$result = $sent ? true : new \WP_Error( 'nmc_mail_failed', esc_html__( 'ارسال ایمیل ناموفق بود.', 'neomorph-core' ) );
				break;

			default:
				$result = new \WP_Error( 'nmc_channel', esc_html__( 'کانال نامعتبر.', 'neomorph-core' ) );
		}

		/**
		 * Audit hook for every outbound notification.
		 */
		do_action( 'nmc_notification_sent', $channel, $message, $to, $result );

		return $result;
	}

	/**
	 * Convenience: message to the phone of a user.
	 */
	public static function send_to_user( $user_id, $message, $channels = array( 'sms' ) ) {
		$phone = \nmc_user_phone( $user_id );
		foreach ( (array) $channels as $channel ) {
			self::send( $channel, $message, 'sms' === $channel ? $phone : '' );
		}
	}

	/**
	 * Message to a raw phone number (guests / applicants without WP accounts).
	 */
	public static function send_to_user_by_phone( $phone, $message, $channels = array( 'sms' ) ) {
		foreach ( (array) $channels as $channel ) {
			self::send( $channel, $message, 'sms' === $channel ? $phone : '' );
		}
	}
}
