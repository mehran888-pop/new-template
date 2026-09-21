<?php
/**
 * Activation routines: custom table, roles, cron, pages hint.
 *
 * @package NeomorphCore
 */

namespace NeomorphCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Activator
 */
class Activator {

	const DB_VERSION = '1.0.0';

	/**
	 * Run on activate.
	 */
	public static function activate() {
		self::create_tables();
		self::register_roles();
		self::schedule_cron();
		update_option( 'neomorph_core_db_version', self::DB_VERSION );
		flush_rewrite_rules();
	}

	/**
	 * Points ledger table (dbDelta).
	 */
	public static function create_tables() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table   = $wpdb->prefix . 'nmc_points';
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			points BIGINT(20) NOT NULL,
			type VARCHAR(20) NOT NULL DEFAULT 'earn',
			reason VARCHAR(190) NOT NULL DEFAULT '',
			reference VARCHAR(64) NOT NULL DEFAULT '',
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY type (type)
		) {$charset};";

		dbDelta( $sql );
	}

	/**
	 * Roles: customer + crm_manager caps.
	 */
	public static function register_roles() {
		add_role(
			'nmc_customer',
			esc_html__( 'مشتری نئومورف', 'neomorph-core' ),
			array(
				'read' => true,
			)
		);

		$admin = get_role( 'administrator' );
		if ( $admin ) {
			foreach ( array( 'manage_nmc_crm', 'manage_nmc_invoices', 'manage_nmc_jobs', 'manage_nmc_tickets' ) as $cap ) {
				$admin->add_cap( $cap );
			}
		}

		add_role(
			'nmc_crm_manager',
			esc_html__( 'مدیر CRM', 'neomorph-core' ),
			array(
				'read'            => true,
				'manage_nmc_crm'  => true,
				'manage_nmc_invoices' => true,
				'manage_nmc_tickets'  => true,
				'list_users'      => true,
				'edit_users'      => true,
			)
		);
	}

	/**
	 * Daily automation cron.
	 */
	public static function schedule_cron() {
		if ( ! wp_next_scheduled( 'nmc_daily_digest' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'nmc_daily_digest' );
		}
	}
}
