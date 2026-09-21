<?php
/**
 * Main plugin loader — wires every module.
 *
 * @package NeomorphCore
 */

namespace NeomorphCore;

use NeomorphCore\Auth\OtpController;
use NeomorphCore\Automation\AutomationEngine;
use NeomorphCore\Crm\ContactController;
use NeomorphCore\Crm\PipelineController;
use NeomorphCore\Finance\InvoiceController;
use NeomorphCore\Finance\Gateways\Manager;
use NeomorphCore\GravityForms\Glue;
use NeomorphCore\Integrations\Notifier;
use NeomorphCore\Integrations\SettingsPage;
use NeomorphCore\Loyalty\LoyaltyEngine;
use NeomorphCore\Panel\PanelController;
use NeomorphCore\Recruitment\ApplicationController;
use NeomorphCore\Recruitment\JobController;
use NeomorphCore\Tickets\TicketController;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Plugin
 */
final class Plugin {

	/**
	 * Singleton.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Accessor.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->boot();
		}
		return self::$instance;
	}

	/**
	 * Boot modules.
	 */
	private function boot() {
		load_plugin_textdomain( 'neomorph-core', false, dirname( plugin_basename( NEOMORPH_CORE_FILE ) ) . '/languages' );

		// Data layer.
		PostTypes\Registrar::init();
		Content\StylishMeta::init();

		// Integrations (must load before automation/notifiers).
		SettingsPage::init();
		Notifier::init();
		Manager::init();

		// Features.
		OtpController::init();
		PanelController::init();
		InvoiceController::init();
		LoyaltyEngine::init();
		TicketController::init();
		ContactController::init();
		PipelineController::init();
		AutomationEngine::init();
		JobController::init();
		ApplicationController::init();

		// Glue.
		Glue::init();

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
		add_action( 'nmc_daily_digest', array( $this, 'daily_digest' ) );
	}

	/**
	 * Admin styles for CRM screens.
	 */
	public function admin_assets( $hook ) {
		if ( false === strpos( (string) $hook, 'nmc' ) ) {
			return;
		}
		wp_enqueue_style( 'nmc-admin', NEOMORPH_CORE_URL . 'assets/css/admin-crm.css', array(), NEOMORPH_CORE_VERSION );
		wp_enqueue_script( 'nmc-admin', NEOMORPH_CORE_URL . 'assets/js/admin.js', array( 'jquery' ), NEOMORPH_CORE_VERSION, true );
	}

	/**
	 * Daily digest to CRM manager via channels (follow-ups, unpaid invoices).
	 */
	public function daily_digest() {
		$unpaid = get_posts(
			array(
				'post_type'   => 'nmc_invoice',
				'post_status' => 'publish',
				'meta_key'    => '_nmc_status', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'  => 'unpaid', // phpcs:ignore WordPress.DB.SlowDBQuery
				'numberposts' => 50,
				'fields'      => 'ids',
			)
		);
		if ( ! $unpaid ) {
			return;
		}
		$lines = array();
		foreach ( $unpaid as $inv ) {
			$lines[] = sprintf( '#%s — %s', $inv, nmc_price( nmc_get_meta( $inv, 'total', 0 ) ) );
		}
		$message = "☕ خلاصه روزانه نئومورف\nفاکتورهای پرداخت‌نشده:\n" . implode( "\n", $lines );
		Notifier::send( 'admin', $message );
	}
}

// Theme-compat: expose helper functions used by theme widgets.
if ( ! function_exists( 'neomorph_panel_url' ) ) {
	function neomorph_panel_url( $view = '' ) {
		$page = (int) nmc_setting( 'panel_page', 0 );
		$url  = $page ? get_permalink( $page ) : home_url( '/panel/' );
		if ( $view ) {
			$url = add_query_arg( 'neo-auth', $view, $url );
		}
		return $url;
	}
}

if ( ! function_exists( 'neomorph_core_active' ) ) {
	function neomorph_core_active() {
		return true;
	}
}
