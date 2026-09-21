<?php
/**
 * Elementor integration: categories, widget registration, theme locations.
 *
 * @package Neomorph
 */

namespace Neomorph\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Elementor
 */
final class Elementor {

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'category' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets' ) );
		add_action( 'elementor/frontend/after_register_styles', array( __CLASS__, 'widget_styles' ) );
		add_action( 'elementor/theme/register_locations', array( __CLASS__, 'theme_locations' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'front_assets' ) );
	}

	/**
	 * Widget category in Elementor panel.
	 */
	public static function category( $elements_manager ) {
		$elements_manager->add_category(
			'neomorph',
			array(
				'title' => esc_html__( 'نئومورف', 'neomorph' ),
				'icon'  => 'eicon-nested-elements',
			)
		);
	}

	/**
	 * Elementor Pro theme-builder header/footer locations.
	 */
	public static function theme_locations( $manager ) {
		$manager->register_all_core_location();
		$manager->register_location(
			'neomorph_footer',
			array(
				'hook'     => 'wp_footer',
				'remove'   => '',
			)
		);
	}

	/**
	 * Shared widget stylesheet (front + editor preview).
	 */
	public static function widget_styles() {
		wp_register_style(
			'neomorph-elementor',
			NEOMORPH_URI . '/assets/css/elementor-widgets.css',
			array(),
			NEOMORPH_VERSION
		);
	}

	/**
	 * Load widget styles on the front as well (when Elementor widgets render).
	 */
	public static function front_assets() {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			wp_enqueue_style( 'neomorph-elementor' );
		}
	}

	/**
	 * Register all Neomorph widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Manager.
	 */
	public static function register_widgets( $widgets_manager ) {
		require_once __DIR__ . '/class-neo-widget-base.php';

		$widgets = array(
			'hero'      => 'Widget_Hero',
			'services'  => 'Widget_Services',
			'team'      => 'Widget_Team',
			'about'     => 'Widget_About',
			'contact'   => 'Widget_Contact',
			'social'    => 'Widget_Social',
			'posts'     => 'Widget_Posts',
			'products'  => 'Widget_Products',
			'header'    => 'Widget_Header',
			'footer'    => 'Widget_Footer',
			'cart'      => 'Widget_Cart',
			'login'     => 'Widget_Login',
			'register'  => 'Widget_Register',
			'loyalty'   => 'Widget_Loyalty',
			'panel'     => 'Widget_Panel',
			'jobs'      => 'Widget_Jobs',
		);

		foreach ( $widgets as $file => $class ) {
			$path = __DIR__ . '/widgets/class-' . str_replace( '_', '-', strtolower( $file ) ) . '.php';
			if ( is_readable( $path ) ) {
				require_once $path;
				$fqcn = '\\Neomorph\\Elementor\\Widgets\\' . $class;
				$widgets_manager->register( new $fqcn() );
			}
		}
	}
}

Elementor::init();
