<?php
/**
 * Deactivation: clear cron.
 *
 * @package NeomorphCore
 */

namespace NeomorphCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Deactivator
 */
class Deactivator {

	/**
	 * Run on deactivate.
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'nmc_daily_digest' );
		flush_rewrite_rules();
	}
}
