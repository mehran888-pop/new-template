<?php
/**
 * یکپارچه‌سازی با المنتور: دسته‌بندی المان‌ها، ثبت ویجت‌ها و Theme Locations.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! did_action( 'elementor/loaded' ) && ! class_exists( '\Elementor\Plugin' ) ) {
	return;
}

require_once NOVIN_AI_INC . 'elementor/widgets/class-novin-ai-widget-base.php';
require_once NOVIN_AI_INC . 'elementor/widgets/class-widget-header.php';
require_once NOVIN_AI_INC . 'elementor/widgets/class-widget-footer.php';
require_once NOVIN_AI_INC . 'elementor/widgets/class-widget-hero.php';
require_once NOVIN_AI_INC . 'elementor/widgets/class-widget-services.php';
require_once NOVIN_AI_INC . 'elementor/widgets/class-widget-projects.php';
require_once NOVIN_AI_INC . 'elementor/widgets/class-widget-team.php';
require_once NOVIN_AI_INC . 'elementor/widgets/class-widget-posts.php';
require_once NOVIN_AI_INC . 'elementor/widgets/class-widget-products.php';
require_once NOVIN_AI_INC . 'elementor/widgets/class-widget-packages.php';
require_once NOVIN_AI_INC . 'elementor/widgets/class-widget-stats.php';
require_once NOVIN_AI_INC . 'elementor/widgets/class-widget-testimonials.php';
require_once NOVIN_AI_INC . 'elementor/widgets/class-widget-faq.php';
require_once NOVIN_AI_INC . 'elementor/widgets/class-widget-cta.php';
require_once NOVIN_AI_INC . 'elementor/widgets/class-widget-brands.php';

if ( ! class_exists( 'Novin_AI_Elementor' ) ) {
	/**
	 * کلاس مدیریت یکپارچه‌سازی المنتور.
	 */
	class Novin_AI_Elementor {

		/**
		 * نمونه یکتا.
		 *
		 * @var Novin_AI_Elementor|null
		 */
		private static $instance = null;

		/**
		 * جلوگیری از ثبت دوباره ویجت‌ها روی هوک‌های قدیمی و جدید.
		 *
		 * @var bool
		 */
		private static $registered = false;

		/**
		 * دریافت نمونه یکتا.
		 *
		 * @return Novin_AI_Elementor
		 */
		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * سازنده: اتصال هوک‌ها.
		 */
		private function __construct() {
			add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );

			// المنتور ۳.۵ به بالا.
			add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ), 20 );
			// نسخه‌های قدیمی‌تر.
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets_legacy' ), 20 );

			add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );

			// ثبت دارایی‌ها در ویرایشگر (در صورت نیاز).
			add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'register_assets' ) );
			add_action( 'elementor/preview/enqueue_styles', array( $this, 'register_assets' ) );

			// اعمال استایل‌های سراسری المنتور روی المان‌ها.
			add_action( 'elementor/element/after_section_start', array( $this, 'nothing' ), 1 );
		}

		/**
		 * ثبت دارایی‌ها در ویرایشگر و پیش‌نمایش.
		 *
		 * @return void
		 */
		public function register_assets() {
			if ( function_exists( 'novin_ai_register_assets' ) ) {
				novin_ai_register_assets();
			}
		}

		/**
		 * افزودن دسته‌بندی اختصاصی المان‌ها.
		 *
		 * @param \Elementor\Elements_Manager $elements_manager مدیر المان‌ها.
		 * @return void
		 */
		public function register_category( $elements_manager ) {
			$elements_manager->add_category(
				'novin-ai',
				array(
					'title' => esc_html__( 'Novin AI — المان‌های اختصاصی', 'novin-ai' ),
					'icon'  => 'eicon-parallax',
				)
			);
		}

		/**
		 * فهرست کلاس‌های ویجت‌ها.
		 *
		 * @return array<int, string>
		 */
		public function get_widgets() {
			return array(
				'Novin_AI\Widgets\Header',
				'Novin_AI\Widgets\Footer',
				'Novin_AI\Widgets\Hero',
				'Novin_AI\Widgets\Services',
				'Novin_AI\Widgets\Projects',
				'Novin_AI\Widgets\Team',
				'Novin_AI\Widgets\Posts',
				'Novin_AI\Widgets\Products',
				'Novin_AI\Widgets\Packages',
				'Novin_AI\Widgets\Stats',
				'Novin_AI\Widgets\Testimonials',
				'Novin_AI\Widgets\FAQ',
				'Novin_AI\Widgets\CTA',
				'Novin_AI\Widgets\Brands',
			);
		}

		/**
		 * ثبت ویجت‌ها (Elementor 3.5+).
		 *
		 * @param \Elementor\Widgets_Manager $widgets_manager مدیر ویجت‌ها.
		 * @return void
		 */
		public function register_widgets( $widgets_manager ) {
			if ( self::$registered ) {
				return;
			}

			self::$registered = true;

			foreach ( $this->get_widgets() as $class ) {
				if ( ! class_exists( $class ) ) {
					continue;
				}

				if ( 'Novin_AI\Widgets\Products' === $class && ! class_exists( 'WooCommerce' ) ) {
					continue;
				}

				$widgets_manager->register( new $class() );
			}
		}

		/**
		 * ثبت ویجت‌ها (نسخه‌های قدیمی المنتور).
		 *
		 * @param \Elementor\Widgets_Manager $widgets_manager مدیر ویجت‌ها.
		 * @return void
		 */
		public function register_widgets_legacy( $widgets_manager ) {
			if ( self::$registered ) {
				return;
			}

			foreach ( $this->get_widgets() as $class ) {
				if ( ! class_exists( $class ) ) {
					continue;
				}

				if ( 'Novin_AI\Widgets\Products' === $class && ! class_exists( 'WooCommerce' ) ) {
					continue;
				}

				if ( method_exists( $widgets_manager, 'register_widget_type' ) ) {
					$widgets_manager->register_widget_type( new $class() );
				}
			}
		}

		/**
		 * ثبت محل‌های قالب (برای المنتور پرو).
		 *
		 * @param mixed $manager مدیر قالب‌های المنتور.
		 * @return void
		 */
		public function register_locations( $manager ) {
			if ( is_object( $manager ) && method_exists( $manager, 'register_all_core_location' ) ) {
				$manager->register_all_core_location();
				return;
			}

			$locations = array(
				'header'  => esc_html__( 'هدر', 'novin-ai' ),
				'footer'  => esc_html__( 'فوتر', 'novin-ai' ),
				'single'  => esc_html__( 'برگه تک‌نوشته', 'novin-ai' ),
				'archive' => esc_html__( 'آرشیو', 'novin-ai' ),
				'search'  => esc_html__( 'نتایج جستجو', 'novin-ai' ),
				'404'     => esc_html__( 'صفحه ۴۰۴', 'novin-ai' ),
			);

			foreach ( $locations as $key => $label ) {
				if ( is_object( $manager ) && method_exists( $manager, 'register_location' ) ) {
					$manager->register_location(
						$key,
						array(
							'label'           => $label,
							'multiple'        => false,
							'edit_in_content' => true,
						)
					);
				}
			}
		}

		/**
		 * متد خالی برای سازگاری با هوک‌های المنتور.
		 *
		 * @return void
		 */
		public function nothing() {
			return;
		}
	}

	Novin_AI_Elementor::instance();
}
