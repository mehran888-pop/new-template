<?php
/**
 * Uninstall: remove plugin options and custom table (keeps CPT content + user data).
 *
 * @package NeomorphCore
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

delete_option( 'neomorph_core_settings' );
delete_option( 'neomorph_core_automation_rules' );
delete_option( 'neomorph_core_rewards' );
delete_option( 'neomorph_core_tiers' );
delete_option( 'neomorph_core_db_version' );
delete_option( 'nmc_terms_seeded' );

// phpcs:ignore WordPress.DB.DirectDatabaseQuery
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}nmc_points" ); // phpcs:ignore WordPress.DB
