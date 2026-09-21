<?php
/**
 * Payment gateway contract.
 *
 * @package NeomorphCore\Finance\Gateways
 */

namespace NeomorphCore\Finance\Gateways;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AbstractGateway
 */
abstract class AbstractGateway {

	/**
	 * Machine name.
	 */
	abstract public function id();

	/**
	 * Human label.
	 */
	abstract public function label();

	/**
	 * Begin payment → returns redirect URL or WP_Error.
	 *
	 * @param int    $invoice_id Invoice.
	 * @param string $return_url Return URL.
	 * @return string|\WP_Error
	 */
	abstract public function request_payment( $invoice_id, $return_url );

	/**
	 * Verify callback. Return true when payment is confirmed.
	 *
	 * @param int $invoice_id Invoice.
	 * @return bool
	 */
	abstract public function verify( $invoice_id );

	/**
	 * Is the gateway configured?
	 */
	abstract public function is_configured();
}
