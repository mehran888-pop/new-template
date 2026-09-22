<?php
/**
 * راهنمای مدیریت: اعلان نصب افزونه‌ها و صفحه معرفی قالب.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'novin_ai_required_plugins' ) ) {
	/**
	 * افزونه‌های پیشنهادی.
	 *
	 * @return array<int, array<string, string>>
	 */
	function novin_ai_required_plugins() {
		return array(
			array(
				'name' => 'Elementor',
				'slug' => 'elementor/elementor.php',
				'file' => 'elementor',
				'desc' => esc_html__( 'صفحه‌ساز المنتور برای استفاده از المان‌های اختصاصی قالب.', 'novin-ai' ),
			),
			array(
				'name' => 'WooCommerce',
				'slug' => 'woocommerce/woocommerce.php',
				'file' => 'woocommerce',
				'desc' => esc_html__( 'فروشگاه‌ساز ووکامرس برای فروش نرم‌افزار، لایسنس و خدمات.', 'novin-ai' ),
			),
		);
	}
}

if ( ! function_exists( 'novin_ai_plugin_notice' ) ) {
	/**
	 * نمایش اعلان افزونه‌های پیشنهادی.
	 *
	 * @return void
	 */
	function novin_ai_plugin_notice() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( $screen && 'appearance_page_novin-ai-about' === $screen->id ) {
			return;
		}

		$missing = array();

		foreach ( novin_ai_required_plugins() as $plugin ) {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( ! is_plugin_active( $plugin['slug'] ) && ! is_dir( WP_PLUGIN_DIR . '/' . $plugin['file'] ) ) {
				$missing[] = $plugin;
			}
		}

		if ( empty( $missing ) ) {
			return;
		}

		?>
		<div class="notice notice-info is-dismissible novin-ai-notice">
			<p>
				<strong><?php esc_html_e( 'قالب Novin AI', 'novin-ai' ); ?></strong>
				<?php esc_html_e( 'برای بهترین تجربه، افزونه‌های زیر را نصب و فعال کنید:', 'novin-ai' ); ?>
			</p>
			<ul>
				<?php foreach ( $missing as $plugin ) : ?>
					<li>
						<strong><?php echo esc_html( $plugin['name'] ); ?></strong> — <?php echo esc_html( $plugin['desc'] ); ?>
						<a class="button button-small" href="<?php echo esc_url( wp_nonce_url( admin_url( 'update.php?action=install-plugin&plugin=' . $plugin['file'] ), 'install-plugin_' . $plugin['file'] ) ); ?>">
							<?php esc_html_e( 'نصب افزونه', 'novin-ai' ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'novin_ai_plugin_notice' );

if ( ! function_exists( 'novin_ai_about_page' ) ) {
	/**
	 * افزودن صفحه معرفی قالب.
	 *
	 * @return void
	 */
	function novin_ai_about_page() {
		add_theme_page(
			esc_html__( 'راهنمای Novin AI', 'novin-ai' ),
			esc_html__( 'راهنمای Novin AI', 'novin-ai' ),
			'manage_options',
			'novin-ai-about',
			'novin_ai_about_page_render'
		);
	}
}
add_action( 'admin_menu', 'novin_ai_about_page' );

if ( ! function_exists( 'novin_ai_about_page_render' ) ) {
	/**
	 * نمایش صفحه معرفی و مستندات کوتاه.
	 *
	 * @return void
	 */
	function novin_ai_about_page_render() {
		$widgets = array(
			esc_html__( 'هدر اختصاصی (Header Builder)', 'novin-ai' )      => esc_html__( 'لوگو، منو، جستجو، سبد خرید، دکمه اقدام و منوی موبایل', 'novin-ai' ),
			esc_html__( 'فوتر اختصاصی (Footer Builder)', 'novin-ai' )     => esc_html__( 'ستون‌های لینک، شبکه‌های اجتماعی، خبرنامه و کپی‌رایت', 'novin-ai' ),
			esc_html__( 'هیرو سه‌بعدی', 'novin-ai' )                      => esc_html__( 'عنوان، توضیح، دکمه‌ها، آمار و لایه‌های عمق سه‌بعدی', 'novin-ai' ),
			esc_html__( 'خدمات', 'novin-ai' )                             => esc_html__( 'نمایش خدمات به صورت کارت‌های شیشه‌ای با آیکون و لینک', 'novin-ai' ),
			esc_html__( 'پروژه‌ها و نمونه‌کارها', 'novin-ai' )            => esc_html__( 'گرید نمونه‌کار با فیلتر دسته‌بندی و لایه سه‌بعدی', 'novin-ai' ),
			esc_html__( 'تیم ما', 'novin-ai' )                            => esc_html__( 'کارت اعضا همراه با سمت و شبکه‌های اجتماعی', 'novin-ai' ),
			esc_html__( 'مقالات و اخبار', 'novin-ai' )                    => esc_html__( 'آخرین نوشته‌ها با چیدمان شبکه‌ای یا لیستی', 'novin-ai' ),
			esc_html__( 'محصولات', 'novin-ai' )                           => esc_html__( 'محصولات ووکامرس با فیلتر دسته و تخفیف‌ها', 'novin-ai' ),
			esc_html__( 'پکیج‌های خدماتی', 'novin-ai' )                   => esc_html__( 'جدول قیمت‌گذاری با تغییر ماهانه / سالانه', 'novin-ai' ),
			esc_html__( 'آمار و دستاوردها', 'novin-ai' )                  => esc_html__( 'شمارنده اعداد با افکت ظهور', 'novin-ai' ),
			esc_html__( 'نظرات مشتریان', 'novin-ai' )                     => esc_html__( 'اسلایدر یا گرید نظرات با امتیاز', 'novin-ai' ),
			esc_html__( 'سوالات متداول', 'novin-ai' )                     => esc_html__( 'آکاردئون سوالات پرتکرار', 'novin-ai' ),
			esc_html__( 'فراخوان به اقدام (CTA)', 'novin-ai' )            => esc_html__( 'بخش تماس و تبدیل با پس‌زمینه نئونی', 'novin-ai' ),
			esc_html__( 'مشتریان و برندها', 'novin-ai' )                  => esc_html__( 'اسکرول بی‌پایان لوگوها', 'novin-ai' ),
		);
		?>
		<div class="wrap novin-ai-about">
			<h1><?php esc_html_e( 'قالب Novin AI — هوش مصنوعی، نرم‌افزار و فناوری اطلاعات', 'novin-ai' ); ?></h1>
			<p class="about-text">
				<?php esc_html_e( 'قالبی سه‌بعدی و فراگیر برای شرکت‌های هوش مصنوعی، طراحی وب، فروش نرم‌افزار، خدمات پشتیبانی و فناوری اطلاعات؛ سازگار با المنتور و ووکامرس.', 'novin-ai' ); ?>
			</p>

			<div class="novin-ai-about__grid">
				<div class="novin-ai-card">
					<h2><?php esc_html_e( 'المان‌های اختصاصی المنتور', 'novin-ai' ); ?></h2>
					<table class="widefat striped">
						<tbody>
							<?php foreach ( $widgets as $title => $desc ) : ?>
								<tr>
									<td><strong><?php echo esc_html( $title ); ?></strong></td>
									<td><?php echo esc_html( $desc ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<div class="novin-ai-card">
					<h2><?php esc_html_e( 'شروع سریع', 'novin-ai' ); ?></h2>

					<?php if ( function_exists( 'novin_ai_demo_status' ) ) : ?>
						<?php if ( ! empty( novin_ai_demo_status() ) ) : ?>
							<p><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'اطلاعات دمو قبلاً وارد شده است.', 'novin-ai' ); ?></p>
						<?php else : ?>
							<p><?php esc_html_e( 'برای شروع سریع، اطلاعات دمو را وارد کنید: خدمات، پروژه‌ها، تیم با رزومه کامل، پکیج‌ها، نظرات، مقالات و پنج برگه آماده با المان‌های اختصاصی المنتور.', 'novin-ai' ); ?></p>
							<p>
								<a class="button button-primary" href="<?php echo esc_url( admin_url( 'themes.php?page=novin-ai-demo-import' ) ); ?>">
									<?php esc_html_e( 'وارد کردن اطلاعات دمو (یک بار)', 'novin-ai' ); ?>
								</a>
							</p>
						<?php endif; ?>
					<?php endif; ?>

					<ol>
						<li><?php esc_html_e( 'افزونه‌های المنتور و ووکامرس را نصب و فعال کنید.', 'novin-ai' ); ?></li>
						<li><?php esc_html_e( 'از منوی «قالب‌های المنتور» یک قالب هدر و فوتر بسازید و در شرایط نمایش، کل سایت را انتخاب کنید.', 'novin-ai' ); ?></li>
						<li><?php esc_html_e( 'برای بخش‌های صفحه اصلی، المان‌های گروه Novin AI را به صفحه اضافه کنید.', 'novin-ai' ); ?></li>
						<li><?php esc_html_e( 'رنگ‌ها، فونت و افکت‌های سه‌بعدی را از بخش «نمایش » سفارشی‌ساز» تنظیم کنید.', 'novin-ai' ); ?></li>
						<li><?php esc_html_e( 'محصولات و لایسنس‌ها را در ووکامرس ثبت کرده و با المان محصولات نمایش دهید.', 'novin-ai' ); ?></li>
					</ol>

					<h2><?php esc_html_e( 'انواع نوشته اختصاصی', 'novin-ai' ); ?></h2>
					<ul>
						<li><?php esc_html_e( 'پروژه‌ها و نمونه‌کارها (Projects)', 'novin-ai' ); ?></li>
						<li><?php esc_html_e( 'خدمات (Services)', 'novin-ai' ); ?></li>
						<li><?php esc_html_e( 'تیم ما (Team)', 'novin-ai' ); ?></li>
						<li><?php esc_html_e( 'پکیج‌های خدماتی (Packages)', 'novin-ai' ); ?></li>
						<li><?php esc_html_e( 'نظرات مشتریان (Testimonials)', 'novin-ai' ); ?></li>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}
}
