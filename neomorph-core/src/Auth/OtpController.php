<?php
/**
 * OTP authentication: send code via SMS providers, verify, login/register.
 *
 * Flow (AJAX via /wp-admin/admin-ajax.php or REST):
 *  neomorph_otp_send   { phone }            → sends 5-digit code (120s cooldown)
 *  neomorph_otp_verify { phone, code, mode: login|register, name… } → session
 *
 * @package NeomorphCore\Auth
 */

namespace NeomorphCore\Auth;

use NeomorphCore\Integrations\Notifier;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OtpController
 */
final class OtpController {

	const TRANSIENT_PREFIX = 'nmc_otp_';
	const COOLDOWN          = 120; // seconds.

	public static function init() {
		add_action( 'wp_ajax_nmc_otp_send', array( __CLASS__, 'ajax_send' ) );
		add_action( 'wp_ajax_nmc_otp_verify', array( __CLASS__, 'ajax_verify' ) );
		add_action( 'wp_ajax_nopriv_nmc_otp_send', array( __CLASS__, 'ajax_send' ) );
		add_action( 'wp_ajax_nopriv_nmc_otp_verify', array( __CLASS__, 'ajax_verify' ) );
	}

	/**
	 * AJAX: send OTP.
	 */
	public static function ajax_send() {
		check_ajax_referer( 'nmc_otp', 'nonce' );
		$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
		$result = self::send_code( $phone );
		wp_send_json( $result );
	}

	/**
	 * AJAX: verify OTP + login/register.
	 */
	public static function ajax_verify() {
		check_ajax_referer( 'nmc_otp', 'nonce' );
		$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
		$code  = isset( $_POST['code'] ) ? sanitize_text_field( wp_unslash( $_POST['code'] ) ) : '';
		$mode  = isset( $_POST['mode'] ) ? sanitize_key( $_POST['mode'] ) : 'login';
		$name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$result = self::verify( $phone, $code, $mode, array( 'name' => $name, 'email' => $email ) );
		wp_send_json( $result );
	}

	/**
	 * Create + send OTP code.
	 *
	 * @return array{success:bool,message:string,retry?:int}
	 */
	public static function send_code( $phone ) {
		$phone = \nmc_normalize_phone( $phone );
		if ( ! \nmc_is_valid_phone( $phone ) ) {
			return array( 'success' => false, 'message' => esc_html__( 'شماره موبایل معتبر نیست.', 'neomorph-core' ) );
		}

		$cooldown_key = self::TRANSIENT_PREFIX . 'cd_' . md5( $phone );
		if ( get_transient( $cooldown_key ) ) {
			return array(
				'success' => false,
				'message' => esc_html__( 'لطفاً کمی صبر کنید و دوباره تلاش کنید.', 'neomorph-core' ),
				'retry'   => (int) get_transient( $cooldown_key ),
			);
		}

		// Do not spam non-existing users in login mode? Allow register-on-first-login; rate limit handles abuse.
		$code = (string) wp_rand( 10000, 99999 );
		set_transient( self::TRANSIENT_PREFIX . md5( $phone ), $code, 5 * MINUTE_IN_SECONDS );
		set_transient( $cooldown_key, self::COOLDOWN, self::COOLDOWN );

		$sent = Notifier::send(
			'sms',
			sprintf(
				/* translators: %s: OTP code */
				esc_html__( 'کد ورود نئومورف: %s', 'neomorph-core' ),
				$code
			),
			$phone
		);

		if ( is_wp_error( $sent ) ) {
			// Fallback: log + surface in dev mode only.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				return array( 'success' => true, 'message' => 'DEBUG CODE: ' . $code );
			}
			return array( 'success' => false, 'message' => esc_html__( 'ارسال پیامک با خطا مواجه شد.', 'neomorph-core' ) );
		}

		return array( 'success' => true, 'message' => esc_html__( 'کد تأیید ارسال شد.', 'neomorph-core' ) );
	}

	/**
	 * Verify code; login or register.
	 *
	 * @return array{success:bool,message:string,redirect?:string}
	 */
	public static function verify( $phone, $code, $mode = 'login', $extra = array() ) {
		$phone = \nmc_normalize_phone( $phone );
		$stored = (string) get_transient( self::TRANSIENT_PREFIX . md5( $phone ) );

		if ( ! $stored || ! hash_equals( $stored, (string) $code ) ) {
			return array( 'success' => false, 'message' => esc_html__( 'کد تأیید نادرست یا منقضی است.', 'neomorph-core' ) );
		}
		delete_transient( self::TRANSIENT_PREFIX . md5( $phone ) );

		$user_id = \nmc_user_id_by_phone( $phone );

		if ( ! $user_id ) {
			if ( 'login' === $mode ) {
				// Auto-register path (Persian-market OTP convention) unless disabled.
				if ( ! nmc_setting( 'otp_auto_register', '1' ) ) {
					return array( 'success' => false, 'message' => esc_html__( 'حسابی با این شماره یافت نشد؛ ابتدا ثبت‌نام کنید.', 'neomorph-core' ) );
				}
			}
			$user_id = self::register_user( $phone, $extra );
			if ( is_wp_error( $user_id ) ) {
				return array( 'success' => false, 'message' => $user_id->get_error_message() );
			}
			\NeomorphCore\Loyalty\LoyaltyEngine::add_points( $user_id, (int) nmc_setting( 'signup_points', 100 ), 'signup', 'bonus' );
			\nmc_do_event( 'user_registered_otp', array( 'user_id' => $user_id ) );
		}

		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id, true );

		$redirect = ! empty( $extra['redirect'] ) ? esc_url_raw( $extra['redirect'] ) : '';
		if ( ! $redirect ) {
			$panel = (int) nmc_setting( 'panel_page', 0 );
			$redirect = $panel ? get_permalink( $panel ) : home_url( '/' );
		}

		return array(
			'success'  => true,
			'message'  => esc_html__( 'با موفقیت وارد شدید.', 'neomorph-core' ),
			'redirect' => $redirect,
		);
	}

	/**
	 * Create WP user from phone + extra data.
	 *
	 * @return int|\WP_Error
	 */
	private static function register_user( $phone, $extra ) {
		$name  = isset( $extra['name'] ) ? trim( (string) $extra['name'] ) : '';
		$email = isset( $extra['email'] ) ? sanitize_email( $extra['email'] ) : '';

		$parts   = preg_split( '/\s+/u', $name, 2 );
		$first   = $parts ? $parts[0] : '';
		$last    = isset( $parts[1] ) ? $parts[1] : '';

		if ( $email && email_exists( $email ) ) {
			return new \WP_Error( 'email_exists', esc_html__( 'این ایمیل قبلاً ثبت شده است.', 'neomorph-core' ) );
		}

		$user_id = wp_insert_user(
			array(
				'user_login' => $phone,
				'user_email' => $email ? $email : $phone . '@nmc.invalid',
				'user_pass'  => wp_generate_password( 24 ),
				'first_name' => $first,
				'last_name'  => $last,
				'role'       => 'nmc_customer',
			)
		);
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}
		update_user_meta( $user_id, 'phone', $phone );
		update_user_meta( $user_id, 'nmc_verified_phone', 1 );
		if ( ! $email ) {
			// Hide placeholder email from display contexts.
			update_user_meta( $user_id, 'nmc_placeholder_email', 1 );
		}

		// Sync into CRM.
		if ( class_exists( '\NeomorphCore\Crm\ContactController' ) ) {
			\NeomorphCore\Crm\ContactController::sync_user( $user_id );
		}
		return $user_id;
	}
}

OtpController::init();
