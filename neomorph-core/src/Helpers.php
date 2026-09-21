<?php
/**
 * Global helpers for Neomorph Core.
 *
 * @package NeomorphCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get core setting with default.
 *
 * @param string $key     Dot-less key in neomorph_core_settings.
 * @param mixed  $default Default.
 * @return mixed
 */
function nmc_setting( $key, $default = '' ) {
	$settings = get_option( 'neomorph_core_settings', array() );
	if ( is_array( $settings ) && isset( $settings[ $key ] ) && '' !== $settings[ $key ] ) {
		return $settings[ $key ];
	}
	return $default;
}

/**
 * Format money (theme aware).
 */
function nmc_price( $amount ) {
	if ( function_exists( 'neomorph_price' ) ) {
		return neomorph_price( $amount );
	}
	return number_format_i18n( (float) $amount, 0, '.', ',' ) . ' تومان';
}

/**
 * Current user's phone (meta `phone`).
 */
function nmc_user_phone( $user_id = 0 ) {
	$user_id = $user_id ? (int) $user_id : get_current_user_id();
	return (string) get_user_meta( $user_id, 'phone', true );
}

/**
 * Find user ID by phone (E.164-ish normalization for Iranian numbers).
 */
function nmc_user_id_by_phone( $phone ) {
	$phone = nmc_normalize_phone( $phone );
	if ( ! $phone ) {
		return 0;
	}
	$users = get_users(
		array(
			'meta_key'   => 'phone', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value' => $phone, // phpcs:ignore WordPress.DB.SlowDBQuery
			'number'     => 1,
			'fields'     => 'ID',
		)
	);
	return $users ? (int) $users[0] : 0;
}

/**
 * Normalize Iranian mobile numbers to 09xxxxxxxxx.
 */
function nmc_normalize_phone( $phone ) {
	$phone = preg_replace( '/[^0-9]/', '', (string) $phone );
	$phone = ltrim( $phone, '0' );
	if ( 0 === strpos( $phone, '98' ) ) {
		$phone = substr( $phone, 2 );
	}
	if ( 0 === strpos( $phone, '9' ) && 10 === strlen( $phone ) ) {
		return '0' . $phone;
	}
	return '';
}

/**
 * Valid Iranian mobile?
 */
function nmc_is_valid_phone( $phone ) {
	return (bool) preg_match( '/^09[0-9]{9}$/', nmc_normalize_phone( $phone ) );
}

/**
 * Generate a human-friendly unique token.
 */
function nmc_generate_token( $length = 32 ) {
	return substr( md5( wp_generate_password( 24, false ) . microtime() . wp_rand() ), 0, $length );
}

/**
 * Invoice meta helpers (works on any post ID).
 */
function nmc_get_meta( $post_id, $key, $default = '' ) {
	$value = get_post_meta( $post_id, '_nmc_' . $key, true );
	return '' === $value || false === $value ? $default : $value;
}

function nmc_update_meta( $post_id, $key, $value ) {
	return update_post_meta( $post_id, '_nmc_' . $key, $value );
}

/**
 * Fire a core trigger event (listened by Automation Engine).
 *
 * @param string $event Event name.
 * @param array  $ctx  Context (user_id, post_id, amount…).
 */
function nmc_do_event( $event, $ctx = array() ) {
	do_action( 'nmc_event_' . $event, $ctx );
	do_action( 'nmc_event', $event, $ctx );
}
