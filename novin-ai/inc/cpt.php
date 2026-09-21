<?php
/**
 * ثبت انواع نوشته و طبقه‌بندی‌های اختصاصی:
 * پروژه‌ها / نمونه‌کارها · خدمات · تیم · پکیج‌های خدماتی · نظرات مشتریان
 *
 * تمام این نوع نوشته‌ها در المان‌های اختصاصی المنتور قابل استفاده‌اند.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'novin_ai_register_post_types' ) ) {
	/**
	 * ثبت CPTها.
	 *
	 * @return void
	 */
	function novin_ai_register_post_types() {

		/* ---------------- پروژه‌ها و نمونه‌کارها ---------------- */
		register_post_type(
			'novin_project',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'پروژه‌ها و نمونه‌کارها', 'novin-ai' ),
					'singular_name' => esc_html__( 'پروژه', 'novin-ai' ),
					'add_new'      => esc_html__( 'افزودن پروژه', 'novin-ai' ),
					'add_new_item' => esc_html__( 'پروژه جدید', 'novin-ai' ),
					'edit_item'    => esc_html__( 'ویرایش پروژه', 'novin-ai' ),
					'all_items'    => esc_html__( 'همه پروژه‌ها', 'novin-ai' ),
					'search_items' => esc_html__( 'جستجوی پروژه', 'novin-ai' ),
					'not_found'    => esc_html__( 'پروژه‌ای یافت نشد', 'novin-ai' ),
				),
				'public'       => true,
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => 'projects', 'with_front' => false ),
				'menu_icon'    => 'dashicons-portfolio',
				'menu_position' => 24,
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'comments' ),
				'taxonomies'   => array( 'novin_project_cat' ),
			)
		);

		register_taxonomy(
			'novin_project_cat',
			'novin_project',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'دسته‌بندی پروژه‌ها', 'novin-ai' ),
					'singular_name' => esc_html__( 'دسته پروژه', 'novin-ai' ),
					'add_new_item' => esc_html__( 'افزودن دسته', 'novin-ai' ),
					'edit_item'    => esc_html__( 'ویرایش دسته', 'novin-ai' ),
				),
				'public'       => true,
				'hierarchical' => true,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => 'project-category', 'with_front' => false ),
			)
		);

		/* ---------------- خدمات ---------------- */
		register_post_type(
			'novin_service',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'خدمات', 'novin-ai' ),
					'singular_name' => esc_html__( 'خدمت', 'novin-ai' ),
					'add_new'      => esc_html__( 'افزودن خدمت', 'novin-ai' ),
					'add_new_item' => esc_html__( 'خدمت جدید', 'novin-ai' ),
					'edit_item'    => esc_html__( 'ویرایش خدمت', 'novin-ai' ),
					'all_items'    => esc_html__( 'همه خدمات', 'novin-ai' ),
					'search_items' => esc_html__( 'جستجوی خدمت', 'novin-ai' ),
					'not_found'    => esc_html__( 'خدمتی یافت نشد', 'novin-ai' ),
				),
				'public'       => true,
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => 'services', 'with_front' => false ),
				'menu_icon'    => 'dashicons-analytics',
				'menu_position' => 25,
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
				'taxonomies'   => array( 'novin_service_cat' ),
			)
		);

		register_taxonomy(
			'novin_service_cat',
			'novin_service',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'دسته‌بندی خدمات', 'novin-ai' ),
					'singular_name' => esc_html__( 'دسته خدمت', 'novin-ai' ),
					'add_new_item' => esc_html__( 'افزودن دسته', 'novin-ai' ),
					'edit_item'    => esc_html__( 'ویرایش دسته', 'novin-ai' ),
				),
				'public'       => true,
				'hierarchical' => true,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => 'service-category', 'with_front' => false ),
			)
		);

		/* ---------------- اعضای تیم ---------------- */
		register_post_type(
			'novin_team',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'تیم ما', 'novin-ai' ),
					'singular_name' => esc_html__( 'عضو تیم', 'novin-ai' ),
					'add_new'      => esc_html__( 'افزودن عضو', 'novin-ai' ),
					'add_new_item' => esc_html__( 'عضو جدید', 'novin-ai' ),
					'edit_item'    => esc_html__( 'ویرایش عضو', 'novin-ai' ),
					'all_items'    => esc_html__( 'همه اعضا', 'novin-ai' ),
					'search_items' => esc_html__( 'جستجوی عضو', 'novin-ai' ),
					'not_found'    => esc_html__( 'عضوی یافت نشد', 'novin-ai' ),
				),
				'public'       => true,
				'has_archive'  => false,
				'rewrite'      => array( 'slug' => 'team', 'with_front' => false ),
				'menu_icon'    => 'dashicons-groups',
				'menu_position' => 26,
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
				'taxonomies'   => array( 'novin_team_group' ),
			)
		);

		register_taxonomy(
			'novin_team_group',
			'novin_team',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'دپارتمان‌ها', 'novin-ai' ),
					'singular_name' => esc_html__( 'دپارتمان', 'novin-ai' ),
					'add_new_item' => esc_html__( 'افزودن دپارتمان', 'novin-ai' ),
				),
				'public'       => true,
				'hierarchical' => true,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => 'team-group', 'with_front' => false ),
			)
		);

		/* ---------------- پکیج‌های خدماتی / تعرفه‌ها ---------------- */
		register_post_type(
			'novin_package',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'پکیج‌های خدماتی', 'novin-ai' ),
					'singular_name' => esc_html__( 'پکیج', 'novin-ai' ),
					'add_new'      => esc_html__( 'افزودن پکیج', 'novin-ai' ),
					'add_new_item' => esc_html__( 'پکیج جدید', 'novin-ai' ),
					'edit_item'    => esc_html__( 'ویرایش پکیج', 'novin-ai' ),
					'all_items'    => esc_html__( 'همه پکیج‌ها', 'novin-ai' ),
					'search_items' => esc_html__( 'جستجوی پکیج', 'novin-ai' ),
					'not_found'    => esc_html__( 'پکیجی یافت نشد', 'novin-ai' ),
				),
				'public'       => true,
				'has_archive'  => false,
				'rewrite'      => array( 'slug' => 'packages', 'with_front' => false ),
				'menu_icon'    => 'dashicons-money-alt',
				'menu_position' => 27,
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
				'taxonomies'   => array( 'novin_package_cat' ),
			)
		);

		register_taxonomy(
			'novin_package_cat',
			'novin_package',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'دسته‌بندی پکیج‌ها', 'novin-ai' ),
					'singular_name' => esc_html__( 'دسته پکیج', 'novin-ai' ),
				),
				'public'       => true,
				'hierarchical' => true,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => 'package-category', 'with_front' => false ),
			)
		);

		/* ---------------- نظرات مشتریان ---------------- */
		register_post_type(
			'novin_testimonial',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'نظرات مشتریان', 'novin-ai' ),
					'singular_name' => esc_html__( 'نظر مشتری', 'novin-ai' ),
					'add_new'      => esc_html__( 'افزودن نظر', 'novin-ai' ),
					'add_new_item' => esc_html__( 'نظر جدید', 'novin-ai' ),
					'edit_item'    => esc_html__( 'ویرایش نظر', 'novin-ai' ),
					'all_items'    => esc_html__( 'همه نظرات', 'novin-ai' ),
					'not_found'    => esc_html__( 'نظری یافت نشد', 'novin-ai' ),
				),
				'public'       => false,
				'publicly_queryable' => false,
				'show_ui'      => true,
				'has_archive'  => false,
				'menu_icon'    => 'dashicons-format-quote',
				'menu_position' => 28,
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			)
		);
	}
}
add_action( 'init', 'novin_ai_register_post_types', 5 );

if ( ! function_exists( 'novin_ai_flush_rewrite' ) ) {
	/**
	 * بروزرسانی قوانین لینک‌ها پس از فعال‌سازی قالب.
	 *
	 * @return void
	 */
	function novin_ai_flush_rewrite() {
		novin_ai_register_post_types();
		flush_rewrite_rules();
	}
}
add_action( 'after_switch_theme', 'novin_ai_flush_rewrite' );

if ( ! function_exists( 'novin_ai_cpt_choices' ) ) {
	/**
	 * لیست انواع نوشته برای کنترل‌های المنتور.
	 *
	 * @return array<string, string>
	 */
	function novin_ai_cpt_choices() {
		return array(
			'post'           => esc_html__( 'نوشته‌ها (مقالات)', 'novin-ai' ),
			'product'        => esc_html__( 'محصولات ووکامرس', 'novin-ai' ),
			'novin_project'  => esc_html__( 'پروژه‌ها و نمونه‌کارها', 'novin-ai' ),
			'novin_service'  => esc_html__( 'خدمات', 'novin-ai' ),
			'novin_team'     => esc_html__( 'اعضای تیم', 'novin-ai' ),
			'novin_package'  => esc_html__( 'پکیج‌های خدماتی', 'novin-ai' ),
			'page'           => esc_html__( 'برگه‌ها', 'novin-ai' ),
		);
	}
}

if ( ! function_exists( 'novin_ai_get_terms_choices' ) ) {
	/**
	 * گزینه‌های ترم‌های یک طبقه‌بندی برای کنترل‌های المنتور.
	 *
	 * @param string $taxonomy نام طبقه‌بندی.
	 * @return array<int|string, string>
	 */
	function novin_ai_get_terms_choices( $taxonomy ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			)
		);

		$choices = array();

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$choices[ $term->term_id ] = $term->name;
			}
		}

		return $choices;
	}
}
