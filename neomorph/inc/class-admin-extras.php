<?php
/**
 * Admin extras: professional dashboard widget + footer credit.
 *
 * @package Neomorph
 */

namespace Neomorph;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin_Extras
 */
final class Admin_Extras {

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'wp_dashboard_setup', array( __CLASS__, 'dashboard_widget' ) );
		add_filter( 'admin_footer_text', array( __CLASS__, 'footer_text' ) );
	}

	/**
	 * "نمای کلی نئومورف" dashboard widget with stats + quick links + integration status.
	 */
	public static function dashboard_widget() {
		if ( ! neomorph_option( 'admin_style', '1' ) ) {
			return;
		}
		wp_add_dashboard_widget(
			'neomorph_overview',
			esc_html__( '🌈 نمای کلی نئومورف', 'neomorph' ),
			array( __CLASS__, 'render_widget' )
		);
	}

	/**
	 * Widget content.
	 */
	public static function render_widget() {
		$users = (int) count_users()['total_users'];

		$stats = array(
			array(
				'num'  => (int) wp_count_posts( 'post' )->publish,
				'label' => esc_html__( 'مقاله', 'neomorph' ),
			),
			array(
				'num'   => class_exists( 'WooCommerce' ) ? (int) wp_count_posts( 'product' )->publish : 0,
				'label' => esc_html__( 'محصول', 'neomorph' ),
			),
			array(
				'num'   => ( class_exists( 'WooCommerce' ) && function_exists( 'wc_orders_count' ) ) ? (int) wc_orders_count( 'completed' ) : 0,
				'label' => esc_html__( 'سفارش کامل', 'neomorph' ),
			),
			array(
				'num'   => $users,
				'label' => esc_html__( 'کاربر', 'neomorph' ),
			),
		);

		$checks = array(
			array(
				'label' => esc_html__( 'افزونه Neomorph Core', 'neomorph' ),
				'ok'    => neomorph_core_active(),
			),
			array(
				'label' => esc_html__( 'المنتور', 'neomorph' ),
				'ok'    => defined( 'ELEMENTOR_VERSION' ),
			),
			array(
				'label' => esc_html__( 'ووکامرس', 'neomorph' ),
				'ok'    => class_exists( 'WooCommerce' ),
			),
			array(
				'label' => esc_html__( 'گراویتی فرم', 'neomorph' ),
				'ok'    => class_exists( 'GFForms' ) || class_exists( 'GFCommon' ),
			),
		);
		?>
		<div class="nmc-dash">
			<div class="nmc-dash__head">
				<div class="nmc-dash__logo">N</div>
				<div>
					<p class="nmc-dash__title"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
					<p class="nmc-dash__sub"><?php esc_html_e( 'پنل حرفه‌ای نئومورف — همه چیز یک نگاه', 'neomorph' ); ?></p>
				</div>
			</div>

			<div class="nmc-dash__stats">
				<?php foreach ( $stats as $stat ) : ?>
					<div class="nmc-dash__stat">
						<b><?php echo esc_html( number_format_i18n( $stat['num'] ) ); ?></b>
						<span><?php echo esc_html( $stat['label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="nmc-dash__status">
				<?php foreach ( $checks as $check ) : ?>
					<div class="nmc-dash__status-row">
						<span><?php echo esc_html( $check['label'] ); ?></span>
						<span class="nmc-dash__pill <?php echo $check['ok'] ? 'nmc-dash__pill--ok' : 'nmc-dash__pill--miss'; ?>">
							<?php echo $check['ok'] ? esc_html__( 'فعال', 'neomorph' ) : esc_html__( 'غیرفعال', 'neomorph' ); ?>
						</span>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="nmc-dash__links">
				<a class="button button-primary button-small" href="<?php echo esc_url( admin_url( 'themes.php?page=neomorph-settings' ) ); ?>"><?php esc_html_e( 'تنظیمات قالب', 'neomorph' ); ?></a>
				<a class="button button-small" href="<?php echo esc_url( admin_url( 'themes.php?page=neomorph-demo' ) ); ?>"><?php esc_html_e( 'دموی اولیه', 'neomorph' ); ?></a>
				<?php if ( neomorph_core_active() ) : ?>
					<a class="button button-small" href="<?php echo esc_url( admin_url( 'admin.php?page=nmc-crm' ) ); ?>"><?php esc_html_e( 'CRM', 'neomorph' ); ?></a>
					<a class="button button-small" href="<?php echo esc_url( admin_url( 'admin.php?page=nmc-automation' ) ); ?>"><?php esc_html_e( 'اتوماسیون', 'neomorph' ); ?></a>
					<a class="button button-small" href="<?php echo esc_url( admin_url( 'admin.php?page=nmc-core-settings' ) ); ?>"><?php esc_html_e( 'اتصال‌ها', 'neomorph' ); ?></a>
				<?php endif; ?>
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<a class="button button-small" href="<?php echo esc_url( admin_url( 'edit.php?post_type=shop_order' ) ); ?>"><?php esc_html_e( 'سفارش‌ها', 'neomorph' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Brand the admin footer subtly.
	 */
	public static function footer_text( $text ) {
		if ( ! neomorph_option( 'admin_style', '1' ) ) {
			return $text;
		}
		return sprintf(
			'%1$s <span style="color:var(--neoa-accent)">◆</span> <a href="%2$s">%3$s</a>',
			esc_html__( 'با عشق ساخته شده با', 'neomorph' ),
			esc_url( admin_url( 'themes.php?page=neomorph-settings' ) ),
			esc_html__( 'قالب نئومورف', 'neomorph' )
		);
	}
}

Admin_Extras::init();
