<?php
/**
 * Demo importer — builds all starter pages (Home, About, Contact, Shop, Blog, Careers, Panel…)
 * with Elementor data in neumorphism style. No XML import needed.
 *
 * @package Neomorph
 */

namespace Neomorph\Demo;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Demo_Importer
 */
final class Demo_Importer {

	const NONCE = 'neomorph_demo_import';

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'admin_post_neomorph_import_demo', array( __CLASS__, 'handle' ) );
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 20 );
	}

	/**
	 * Demo submenu (inside theme settings).
	 */
	public static function menu() {
		add_theme_page(
			esc_html__( 'دموی اولیه نئومورف', 'neomorph' ),
			esc_html__( 'دموی اولیه', 'neomorph' ),
			'manage_options',
			'neomorph-demo',
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * UI page.
	 */
	public static function render_page() {
		?>
		<div class="wrap neomorph-settings">
			<h1>🧪 <?php esc_html_e( 'نصب دموی اولیه نئومورف', 'neomorph' ); ?></h1>
			<div class="neo-surface-admin" style="max-width:760px;margin-top:18px;">
				<p><?php esc_html_e( 'با یک کلیک، صفحات «صفحه اصلی، درباره ما، ارتباط با ما، فروشگاه، مقالات، استخدام، پنل مشتریان، ورود، ثبت‌نام، فاکتور» با چیدمان نئومورفیسم ساخته می‌شوند (المنتوری — قابل ویرایش کامل).', 'neomorph' ); ?></p>
				<p class="description"><?php esc_html_e( 'پیش‌نیازها: افزونه‌های Elementor، WooCommerce و Neomorph Core. صفحات موجود بازنویسی نمی‌شوند؛ صفحات جدید با پیشوند ساخته می‌شوند.', 'neomorph' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( self::NONCE, 'neomorph_demo_nonce' ); ?>
					<input type="hidden" name="action" value="neomorph_import_demo">
					<p>
						<label>
							<input type="checkbox" name="import[elementor]" value="1" checked>
							<?php esc_html_e( 'ساخت صفحات با محتوای المنتور', 'neomorph' ); ?>
						</label><br>
						<label>
							<input type="checkbox" name="import[menus]" value="1" checked>
							<?php esc_html_e( 'ساخت منوهای نمونه و اتصال به موقعیت‌ها', 'neomorph' ); ?>
						</label><br>
						<label>
							<input type="checkbox" name="import[settings]" value="1" checked>
							<?php esc_html_e( 'اعمال تنظیمات پیش‌فرض قالب (فونت وزیرمتن، پالت نئو)', 'neomorph' ); ?>
						</label><br>
						<label>
							<input type="checkbox" name="import[woo]" value="1" checked>
							<?php esc_html_e( 'ساخت صفحات فروشگاه ووکامرس و محصول نمونه', 'neomorph' ); ?>
						</label>
					</p>
					<?php submit_button( esc_html__( 'شروع نصب دمو', 'neomorph' ), 'primary', 'submit', true ); ?>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Run import steps.
	 */
	public static function handle() {
		if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( sanitize_key( $_POST['neomorph_demo_nonce'] ?? '' ), self::NONCE ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز.', 'neomorph' ) );
		}
		$what = isset( $_POST['import'] ) && is_array( $_POST['import'] ) ? array_map( 'sanitize_key', $_POST['import'] ) : array();

		if ( ! empty( $what['settings'] ) ) {
			self::apply_settings();
		}
		if ( ! empty( $what['elementor'] ) ) {
			self::create_pages();
		}
		if ( ! empty( $what['woo'] ) && class_exists( 'WooCommerce' ) ) {
			self::create_woo();
		}
		if ( ! empty( $what['menus'] ) ) {
			self::create_menus();
		}

		wp_safe_redirect( add_query_arg( array( 'page' => 'neomorph-demo', 'imported' => 1 ), admin_url( 'themes.php' ) ) );
		exit;
	}

	/**
	 * Default theme settings.
	 */
	private static function apply_settings() {
		$options = get_option( 'neomorph_options', array() );
		$defaults = \Neomorph\Options\Options::defaults();
		update_option( 'neomorph_options', wp_parse_args( is_array( $options ) ? $options : array(), $defaults ) );
	}

	/**
	 * Page map: slug => [title, template file|null, json data file].
	 */
	public static function page_map() {
		return array(
			'home'      => array( 'title' => 'صفحه اصلی', 'template' => 'page-templates/template-fullwidth.php', 'data' => 'home.json', 'front' => true ),
			'about'     => array( 'title' => 'درباره ما', 'template' => 'page-templates/template-fullwidth.php', 'data' => 'about.json' ),
			'contact'   => array( 'title' => 'ارتباط با ما', 'template' => 'page-templates/template-fullwidth.php', 'data' => 'contact.json' ),
			'blog'      => array( 'title' => 'مقالات و آموزش', 'template' => null, 'data' => 'blog.json' ),
			'careers'   => array( 'title' => 'استخدام', 'template' => null, 'data' => 'careers.json' ),
			'panel'     => array( 'title' => 'پنل مشتریان', 'template' => 'page-templates/template-panel.php', 'data' => null ),
			'login'     => array( 'title' => 'ورود', 'template' => 'page-templates/template-fullwidth.php', 'data' => 'login.json' ),
			'register'  => array( 'title' => 'ثبت‌نام', 'template' => 'page-templates/template-fullwidth.php', 'data' => 'register.json' ),
			'invoice'   => array( 'title' => 'پرداخت فاکتور', 'template' => 'page-templates/template-invoice.php', 'data' => null ),
			'interview' => array( 'title' => 'مصاحبه ویدیویی', 'template' => 'page-templates/template-fullwidth.php', 'data' => 'interview.json' ),
			'consult'   => array( 'title' => 'درخواست مشاوره', 'template' => 'page-templates/template-fullwidth.php', 'data' => 'consult.json' ),
		);
	}

	/**
	 * Create pages + Elementor data.
	 */
	private static function create_pages() {
		$existing_slugs = array();
		foreach ( self::page_map() as $slug => $page ) {
			$exists = get_page_by_path( $slug );
			if ( $exists ) {
				$existing_slugs[ $slug ] = $exists->ID;
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_title'   => $page['title'],
					'post_name'    => $slug,
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_content' => '',
				)
			);
			if ( is_wp_error( $id ) ) {
				continue;
			}
			if ( $page['template'] ) {
				update_post_meta( $id, '_wp_page_template', $page['template'] );
			}
			if ( $page['data'] ) {
				$json = self::load_data( $page['data'] );
				if ( $json ) {
					update_post_meta( $id, '_elementor_edit_mode', 'builder' );
					update_post_meta( $id, '_elementor_template_type', 'wp-page' );
					update_post_meta( $id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.16.0' );
					update_post_meta( $id, '_elementor_data', wp_slash( $json ) );
				}
			}
			$existing_slugs[ $slug ] = $id;

			if ( ! empty( $page['front'] ) ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $id );
			}
			if ( 'blog' === $slug ) {
				update_option( 'page_for_posts', $id );
			}
		}

		// Map system pages into theme options.
		$options = get_option( 'neomorph_options', array() );
		foreach ( array( 'panel' => 'panel_page', 'login' => 'login_page', 'register' => 'register_page', 'invoice' => 'invoice_page', 'blog' => 'blog_page' ) as $slug => $key ) {
			if ( isset( $existing_slugs[ $slug ] ) ) {
				$options[ $key ] = $existing_slugs[ $slug ];
			}
		}
		if ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_id' ) ) {
			$options['shop_page'] = wc_get_page_id( 'shop' );
		}
		update_option( 'neomorph_options', $options );
	}

	/**
	 * Load Elementor JSON template and swap widget types to Neomorph widgets.
	 */
	private static function load_data( $file ) {
		$path = NEOMORPH_DIR . '/inc/demo/data/' . $file;
		if ( ! is_readable( $path ) ) {
			return '';
		}
		return file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}

	/**
	 * WooCommerce pages + demo product.
	 */
	private static function create_woo() {
		if ( function_exists( 'WC_Install' ) ) {
			WC_Install::create_pages();
		}
		if ( ! get_page_by_path( 'shop' ) && function_exists( 'wc_get_page_id' ) ) {
			$shop_id = wc_get_page_id( 'shop' );
			if ( $shop_id > 0 ) {
				update_post_meta( $shop_id, '_wp_page_template', 'page-templates/template-fullwidth.php' );
			}
		}
		if ( ! get_page_by_path( 'product-sample' ) ) {
			$product_id = wp_insert_post(
				array(
					'post_title'  => 'محصول نمونه نئومورف',
					'post_name'   => 'product-sample',
					'post_type'   => 'product',
					'post_status' => 'publish',
					'post_content' => 'محصول نمونه برای پیش‌نمایش استایل نئومورفیسم فروشگاه.',
				)
			);
			if ( ! is_wp_error( $product_id ) ) {
				wp_set_object_terms( $product_id, 'simple', 'product_type' );
				update_post_meta( $product_id, '_price', '299000' );
				update_post_meta( $product_id, '_regular_price', '299000' );
				update_post_meta( $product_id, '_virtual', 'yes' );
				update_post_meta( $product_id, '_featured', 'yes' );
			}
		}
		$shop_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
		if ( $shop_id > 0 ) {
			$options          = get_option( 'neomorph_options', array() );
			$options['shop_page'] = $shop_id;
			update_option( 'neomorph_options', $options );
		}
	}

	/**
	 * Sample menus wired to theme locations.
	 */
	private static function create_menus() {
		$locations = get_theme_mod( 'nav_menu_locations', array() );

		$primary = wp_get_nav_menu_object( 'منوی اصلی نئومورف' );
		if ( ! $primary ) {
			$primary_id = wp_create_nav_menu( 'منوی اصلی نئومورف' );
			$pages = array( 'home' => 'خانه', 'shop' => 'فروشگاه', 'blog' => 'مقالات', 'about' => 'درباره ما', 'careers' => 'استخدام', 'contact' => 'ارتباط با ما' );
			foreach ( $pages as $slug => $label ) {
				$target = 'shop' === $slug && function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : get_permalink( get_page_by_path( $slug ) );
				if ( $target ) {
					wp_update_nav_menu_item(
						$primary_id,
						0,
						array(
							'menu-item-title'  => $label,
							'menu-item-url'    => $target,
							'menu-item-type'   => 'custom',
							'menu-item-status' => 'publish',
						)
					);
				}
			}
			$locations['primary'] = $primary_id;
			$locations['mobile']  = $primary_id;
		} else {
			$locations['primary'] = $primary->term_id;
		}

		$footer = wp_get_nav_menu_object( 'منوی فوتر نئومورف' );
		if ( ! $footer ) {
			$footer_id = wp_create_nav_menu( 'منوی فوتر نئومورف' );
			foreach ( array( 'panel' => 'پنل مشتریان', 'consult' => 'مشاوره', 'interview' => 'مصاحبه ویدیویی' ) as $slug => $label ) {
				$target = get_permalink( get_page_by_path( $slug ) );
				if ( $target ) {
					wp_update_nav_menu_item(
						$footer_id,
						0,
						array(
							'menu-item-title'  => $label,
							'menu-item-url'    => $target,
							'menu-item-type'   => 'custom',
							'menu-item-status' => 'publish',
						)
					);
				}
			}
			$locations['footer'] = $footer_id;
		} else {
			$locations['footer'] = $footer->term_id;
		}

		set_theme_mod( 'nav_menu_locations', $locations );
	}
}

Demo_Importer::init();
