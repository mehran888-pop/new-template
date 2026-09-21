<?php
/**
 * SMS provider contract.
 *
 * @package NeomorphCore\Integrations
 */

namespace NeomorphCore\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface Provider
 */
interface Provider {

	/**
	 * Send a text message (or pattern/OTP) to a number.
	 *
	 * @param string $message Message body.
	 * @param string $to      Destination phone (normalized 09…).
	 * @param array  $args    Extra args (pattern variables…).
	 * @return true|\WP_Error
	 */
	public function send( $message, $to, $args = array() );
}
