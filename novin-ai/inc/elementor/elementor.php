<?php
/**
 * یکپارچه‌سازی با المنتور: دسته‌بندی المان‌ها، ثبت ویجت‌ها و Theme Locations.
 *
 * نکته مهم: کلاس‌های ویجت‌ها تنها زمانی بارگذاری می‌شوند که المنتور کاملاً
 * آماده باشد (درون هوک ثبت ویجت‌ها). این کار از بروز خطای
 * «Class Novin_AI\Widgets\Novin_AI_Widget_Base not found» جلوگیری می‌کند.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! did_action( 'elementor/loaded' ) && ! class_exists( '\Elementor\Plugin' ) ) {
	return;
}

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
		 * آیا فایل‌های ویجت‌ها بارگذاری شده‌اند؟
		 *
		 * @var bool
		 */
		private static $included = false;

		/**
		 * آیا ویجت‌ها ثبت شده‌اند؟ (جلوگیری از ثبت دوباره روی هوک قدیمی/جدید)
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

			// بارگذاری فایل‌ها پیش از ثبت (هر دو هوک قدیمی و جدید).
			add_action( 'elementor/widgets/register', array( $this, 'include_widgets' ), 5 );
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'include_widgets' ), 5 );

			// المنتور ۳.۵ به بالا.
			add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ), 20 );
			// نسخه‌های قدیمی‌تر.
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets_legacy' ), 20 );

			add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );

			// دارایی‌ها در ویرایشگر و پیش‌نمایش.
			add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'register_assets' ) );
			add_action( 'elementor/preview/enqueue_styles', array( $this, 'register_assets' ) );
			add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'register_assets' ) );
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
		 * بارگذاری ایمن فایل‌های ویجت‌ها.
		 *
		 * @return bool
		 */
		public function include_widgets() {
			if ( self::$included ) {
				return true;
			}

			// اگر کلاس پایه المنتور در دسترس نیست، ویجت‌ها بارگذاری نشوند.
			if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
				return false;
			}

			$base = NOVIN_AI_INC . 'elementor/widgets/';

			require_once $base . 'class-novin-ai-widget-base.php';

			// اگر کلاس پایه قالب به هر دلیلی تعریف نشد، ادامه ندهیم (جلوگیری از خطای کشنده).
			if ( ! class_exists( '\Novin_AI\Widgets\Novin_AI_Widget_Base' ) ) {
				return false;
			}

			$files = array(
				'class-widget-header.php',
				'class-widget-footer.php',
				'class-widget-hero.php',
				'class-widget-services.php',
				'class-widget-projects.php',
				'class-widget-team.php',
				'class-widget-posts.php',
				'class-widget-products.php',
				'class-widget-packages.php',
				'class-widget-stats.php',
				'class-widget-testimonials.php',
				'class-widget-faq.php',
				'class-widget-cta.php',
				'class-widget-brands.php',
			);

			foreach ( $files as $file ) {
				if ( is_readable( $base . $file ) ) {
					require_once $base . $file;
				}
			}

			self::$included = true;

			return true;
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

			if ( ! $this->include_widgets() ) {
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

			if ( ! $this->include_widgets() ) {
				return;
			}

			if ( ! method_exists( $widgets_manager, 'register_widget_type' ) ) {
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

				$widgets_manager->register_widget_type( new $class() );
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
	}

	Novin_AI_Elementor::instance();
}
