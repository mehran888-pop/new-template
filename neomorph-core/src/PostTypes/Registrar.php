<?php
/**
 * Post types & taxonomies for the data layer.
 *
 * CPTs:
 *  - nmc_invoice     (فاکتور)
 *  - nmc_ticket      (تیکت پشتیبانی)
 *  - nmc_case        (پرونده CRM / فرصت)
 *  - nmc_job         (موقعیت شغلی)
 *  - nmc_application (درخواست استخدام)
 *  - nmc_consult     (درخواست مشاوره/پشتیبانی)
 * Taxonomies:
 *  - crm_segment     (دسته‌بندی مشتریان)
 *  - crm_service     (خدمات انتخابی)
 *  - ticket_dept     (واحد پشتیبانی)
 *  - job_type        (نوع همکاری: تمام‌وقت…)
 *
 * @package NeomorphCore
 */

namespace NeomorphCore\PostTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Registrar
 */
final class Registrar {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ), 5 );
	}

	/**
	 * Register everything.
	 */
	public static function register() {
		$labels_base = array(
			'menu_icon' => 'dashicons-shield-alt',
		);

		// ── Invoice ────────────────────────────────────────────────
		register_post_type(
			'nmc_invoice',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'فاکتورها', 'neomorph-core' ),
					'singular_name' => esc_html__( 'فاکتور', 'neomorph-core' ),
					'add_new_item'  => esc_html__( 'فاکتور جدید', 'neomorph-core' ),
					'edit_item'     => esc_html__( 'ویرایش فاکتور', 'neomorph-core' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => 'nmc-dashboard',
				'supports'     => array( 'title', 'editor' ),
				'capability_type' => 'post',
				'capabilities' => array(
					'create_posts' => 'manage_nmc_invoices',
				),
				'map_meta_cap' => true,
			)
		);

		// ── Ticket ─────────────────────────────────────────────────
		register_post_type(
			'nmc_ticket',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'تیکت‌ها', 'neomorph-core' ),
					'singular_name' => esc_html__( 'تیکت', 'neomorph-core' ),
					'add_new_item'  => esc_html__( 'تیکت جدید', 'neomorph-core' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => 'nmc-dashboard',
				'supports'     => array( 'title', 'editor' ),
				'map_meta_cap' => true,
				'capabilities' => array(
					'create_posts' => 'manage_nmc_tickets',
				),
			)
		);

		// ── CRM case (lead / deal / service follow-up) ─────────────
		register_post_type(
			'nmc_case',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'پرونده‌های CRM', 'neomorph-core' ),
					'singular_name' => esc_html__( 'پرونده CRM', 'neomorph-core' ),
					'add_new_item'  => esc_html__( 'پرونده جدید', 'neomorph-core' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => 'nmc-crm',
				'supports'     => array( 'title', 'editor' ),
				'map_meta_cap' => true,
				'capabilities' => array(
					'create_posts' => 'manage_nmc_crm',
				),
			)
		);

		// ── Job (public) ──────────────────────────────────────────
		register_post_type(
			'job',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'موقعیت‌های شغلی', 'neomorph-core' ),
					'singular_name' => esc_html__( 'موقعیت شغلی', 'neomorph-core' ),
					'add_new_item'  => esc_html__( 'موقعیت جدید', 'neomorph-core' ),
				),
				'public'       => true,
				'has_archive'  => 'careers',
				'rewrite'      => array( 'slug' => 'careers' ),
				'menu_icon'    => 'dashicons-businessperson',
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			)
		);

		register_taxonomy(
			'job_type',
			'job',
			array(
				'labels'       => array(
					'name' => esc_html__( 'نوع همکاری', 'neomorph-core' ),
				),
				'public'       => true,
				'hierarchical' => true,
				'rewrite'      => array( 'slug' => 'job-type' ),
				'show_in_rest' => true,
			)
		);

		// ── Job application ───────────────────────────────────────
		register_post_type(
			'nmc_application',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'درخواست‌های استخدام', 'neomorph-core' ),
					'singular_name' => esc_html__( 'درخواست استخدام', 'neomorph-core' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => 'nmc-jobs-menu',
				'supports'     => array( 'title' ),
				'map_meta_cap' => true,
				'capabilities' => array(
					'create_posts' => 'manage_nmc_jobs',
				),
			)
		);

		// ── Consultation / support request ─────────────────────────
		register_post_type(
			'nmc_consult',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'درخواست‌های مشاوره', 'neomorph-core' ),
					'singular_name' => esc_html__( 'درخواست مشاوره', 'neomorph-core' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => 'nmc-crm',
				'supports'     => array( 'title', 'editor' ),
				'map_meta_cap' => true,
				'capabilities' => array(
					'create_posts' => 'manage_nmc_crm',
				),
			)
		);

		// ── Taxonomies ────────────────────────────────────────────
		foreach (
			array(
				'crm_segment' => array( esc_html__( 'دسته‌بندی مشتریان', 'neomorph-core' ), array( 'nmc_case' ) ),
				'crm_service' => array( esc_html__( 'خدمات انتخابی', 'neomorph-core' ), array( 'nmc_case', 'nmc_consult' ) ),
				'ticket_dept' => array( esc_html__( 'واحدهای پشتیبانی', 'neomorph-core' ), array( 'nmc_ticket' ) ),
			) as $tax => $info
		) {
			register_taxonomy(
				$tax,
				$info[1],
				array(
					'labels'       => array( 'name' => $info[0] ),
					'public'       => false,
					'show_ui'      => true,
					'hierarchical' => true,
					'show_in_rest' => false,
				)
			);
		}

		// Seed default ticket departments + segments + services.
		self::seed_terms();
	}

	/**
	 * Seed minimal terms once.
	 */
	private static function seed_terms() {
		if ( get_option( 'nmc_terms_seeded' ) ) {
			return;
		}
		foreach ( array( 'فروش', 'فنی', 'امور مالی' ) as $dept ) {
			if ( ! term_exists( $dept, 'ticket_dept' ) ) {
				wp_insert_term( $dept, 'ticket_dept' );
			}
		}
		foreach ( array( 'مشتری تازه', 'مشتری فعال', 'VIP', 'در معرض ریزش' ) as $seg ) {
			if ( ! term_exists( $seg, 'crm_segment' ) ) {
				wp_insert_term( $seg, 'crm_segment' );
			}
		}
		foreach ( array( 'طراحی وب', 'سئو', 'پشتیبانی', 'مشاوره' ) as $srv ) {
			if ( ! term_exists( $srv, 'crm_service' ) ) {
				wp_insert_term( $srv, 'crm_service' );
			}
		}
		foreach ( array( 'تمام‌وقت', 'پاره‌وقت', 'دورکاری', 'کارآموزی' ) as $type ) {
			if ( ! term_exists( $type, 'job_type' ) ) {
				wp_insert_term( $type, 'job_type' );
			}
		}
		update_option( 'nmc_terms_seeded', 1 );
	}
}

Registrar::init();
