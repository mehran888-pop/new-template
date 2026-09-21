<?php
/**
 * Settings page: SMS gateways (sms.ir / ملی پیامک), Bale & Telegram, OTP, payments, loyalty.
 *
 * @package NeomorphCore\Integrations
 */

namespace NeomorphCore\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SettingsPage
 */
final class SettingsPage {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 5 );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Top-level menu — hub for Core admin screens.
	 */
	public static function menu() {
		add_menu_page(
			esc_html__( 'نئومورف کور', 'neomorph-core' ),
			esc_html__( 'نئومورف کور', 'neomorph-core' ),
			'manage_options',
			'nmc-dashboard',
			array( __CLASS__, 'dashboard_page' ),
			'dashicons-shield-alt',
			56
		);
		add_submenu_page( 'nmc-dashboard', esc_html__( 'داشبورد', 'neomorph-core' ), esc_html__( 'داشبورد', 'neomorph-core' ), 'manage_options', 'nmc-dashboard', array( __CLASS__, 'dashboard_page' ) );
		add_submenu_page( 'nmc-dashboard', esc_html__( 'تنظیمات اتصال‌ها', 'neomorph-core' ), esc_html__( 'تنظیمات اتصال‌ها', 'neomorph-core' ), 'manage_options', 'nmc-core-settings', array( __CLASS__, 'settings_page' ) );
		add_submenu_page( 'nmc-crm', esc_html__( 'CRM و اتوماسیون', 'neomorph-core' ), esc_html__( 'CRM و اتوماسیون', 'neomorph-core' ), 'manage_nmc_crm', 'nmc-crm', array( '\NeomorphCore\Crm\ContactController', 'render_admin_page' ) );
		add_submenu_page( 'nmc-crm', esc_html__( 'قوانین اتوماسیون', 'neomorph-core' ), esc_html__( 'قوانین اتوماسیون', 'neomorph-core' ), 'manage_nmc_crm', 'nmc-automation', array( '\NeomorphCore\Automation\AutomationEngine', 'render_rules_page' ) );
		add_submenu_page( 'nmc-jobs-menu', esc_html__( 'استخدام', 'neomorph-core' ), esc_html__( 'استخدام', 'neomorph-core' ), 'manage_nmc_jobs', 'nmc-jobs-menu', array( '\NeomorphCore\Recruitment\JobController', 'render_menu_page' ) );
		add_submenu_page( 'nmc-dashboard', esc_html__( 'باشگاه مشتریان', 'neomorph-core' ), esc_html__( 'باشگاه مشتریان', 'neomorph-core' ), 'manage_options', 'nmc-loyalty', array( '\NeomorphCore\Loyalty\LoyaltyEngine', 'render_admin_page' ) );
	}

	/**
	 * Settings registration (single option array).
	 */
	public static function register_settings() {
		register_setting(
			'nmc_core_settings',
			'neomorph_core_settings',
			array( 'sanitize_callback' => array( __CLASS__, 'sanitize' ) )
		);
	}

	/**
	 * Sanitize all settings.
	 */
	public static function sanitize( $input ) {
		$out   = get_option( 'neomorph_core_settings', array() );
		$out   = is_array( $out ) ? $out : array();
		$input = is_array( $input ) ? $input : array();

		$text_keys = array( 'smsir_api_key', 'smsir_line', 'smsir_pattern', 'meli_username', 'meli_password', 'meli_line', 'meli_pattern', 'bale_token', 'bale_chat_id', 'telegram_token', 'telegram_chat_id', 'zarinpal_merchant', 'idpay_api_key', 'panel_page', 'invoice_page' );
		foreach ( $text_keys as $key ) {
			if ( isset( $input[ $key ] ) ) {
				$out[ $key ] = sanitize_text_field( $input[ $key ] );
			}
		}
		foreach ( array( 'sms_gateway', 'payment_gateway', 'points_per_toman', 'signup_points', 'otp_auto_register', 'loyalty_enabled' ) as $key ) {
			if ( isset( $input[ $key ] ) ) {
				$out[ $key ] = sanitize_text_field( $input[ $key ] );
			}
		}
		return $out;
	}

	/**
	 * Dashboard hub page.
	 */
	public static function dashboard_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$counts   = function_exists( 'wp_count_posts' ) ? array(
			'invoices' => (int) wp_count_posts( 'nmc_invoice' )->publish,
			'tickets'  => (int) wp_count_posts( 'nmc_ticket' )->publish,
			'cases'    => (int) wp_count_posts( 'nmc_case' )->publish,
			'jobs'     => (int) wp_count_posts( 'job' )->publish,
			'applies'  => (int) wp_count_posts( 'nmc_application' )->publish,
		) : array();
		$stats = array_merge(
			array( 'users' => (int) count_users()['total_users'] ),
			array_merge( array( 'invoices' => 0, 'tickets' => 0, 'cases' => 0, 'jobs' => 0, 'applies' => 0 ), $counts )
		);
		?>
		<div class="wrap nmc-admin">
			<h1>🛡️ <?php esc_html_e( 'داشبورد نئومورف کور', 'neomorph-core' ); ?></h1>
			<div class="nmc-stat-grid">
				<?php
				$labels = array(
					'users'    => esc_html__( 'کاربران', 'neomorph-core' ),
					'invoices' => esc_html__( 'فاکتورها', 'neomorph-core' ),
					'tickets'  => esc_html__( 'تیکت‌ها', 'neomorph-core' ),
					'cases'    => esc_html__( 'پرونده‌های CRM', 'neomorph-core' ),
					'jobs'     => esc_html__( 'موقعیت‌های شغلی', 'neomorph-core' ),
					'applies'  => esc_html__( 'درخواست‌های استخدام', 'neomorph-core' ),
				);
				foreach ( $labels as $key => $label ) :
					?>
					<div class="nmc-stat"><span class="nmc-stat__num"><?php echo esc_html( number_format_i18n( (int) $stats[ $key ] ) ); ?></span><span class="nmc-stat__label"><?php echo esc_html( $label ); ?></span></div>
				<?php endforeach; ?>
			</div>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=nmc-core-settings' ) ); ?>"><?php esc_html_e( 'تنظیمات پیامک، بله، تلگرام و درگاه پرداخت', 'neomorph-core' ); ?></a>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=nmc-automation' ) ); ?>"><?php esc_html_e( 'قوانین اتوماسیون', 'neomorph-core' ); ?></a>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=nmc-loyalty' ) ); ?>"><?php esc_html_e( 'باشگاه مشتریان', 'neomorph-core' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Integrations settings form.
	 */
	public static function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$s = get_option( 'neomorph_core_settings', array() );
		$s = is_array( $s ) ? $s : array();
		?>
		<div class="wrap nmc-admin">
			<h1><?php esc_html_e( 'تنظیمات اتصال‌ها', 'neomorph-core' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'nmc_core_settings' ); ?>
				<div class="nmc-settings-grid">

					<section class="nmc-card">
						<h2>📱 <?php esc_html_e( 'سامانه پیامکی', 'neomorph-core' ); ?></h2>
						<label><?php esc_html_e( 'سامانه فعال', 'neomorph-core' ); ?>
							<select name="neomorph_core_settings[sms_gateway]">
								<?php
								$gw = isset( $s['sms_gateway'] ) ? $s['sms_gateway'] : 'smsir';
								printf( '<option value="smsir" %s>sms.ir</option>', selected( $gw, 'smsir', false ) );
								printf( '<option value="meli" %s>%s</option>', selected( $gw, 'meli', false ), esc_html__( 'ملی پیامک', 'neomorph-core' ) );
								?>
							</select>
						</label>
						<h3>sms.ir</h3>
						<label><?php esc_html_e( 'کلید API (X-API-KEY)', 'neomorph-core' ); ?>
							<input type="text" name="neomorph_core_settings[smsir_api_key]" value="<?php echo esc_attr( $s['smsir_api_key'] ?? '' ); ?>" class="regular-text"></label>
						<label><?php esc_html_e( 'شماره خط', 'neomorph-core' ); ?>
							<input type="text" name="neomorph_core_settings[smsir_line]" value="<?php echo esc_attr( $s['smsir_line'] ?? '' ); ?>" class="small-text"></label>
						<label><?php esc_html_e( 'کد پترن OTP (patternCode)', 'neomorph-core' ); ?>
							<input type="text" name="neomorph_core_settings[smsir_pattern]" value="<?php echo esc_attr( $s['smsir_pattern'] ?? '' ); ?>" class="regular-text"></label>

						<h3><?php esc_html_e( 'ملی پیامک', 'neomorph-core' ); ?></h3>
						<label><?php esc_html_e( 'نام کاربری', 'neomorph-core' ); ?>
							<input type="text" name="neomorph_core_settings[meli_username]" value="<?php echo esc_attr( $s['meli_username'] ?? '' ); ?>" class="regular-text"></label>
						<label><?php esc_html_e( 'رمز عبور', 'neomorph-core' ); ?>
							<input type="text" name="neomorph_core_settings[meli_password]" value="<?php echo esc_attr( $s['meli_password'] ?? '' ); ?>" class="regular-text"></label>
						<label><?php esc_html_e( 'شماره خط', 'neomorph-core' ); ?>
							<input type="text" name="neomorph_core_settings[meli_line]" value="<?php echo esc_attr( $s['meli_line'] ?? '' ); ?>" class="small-text"></label>
						<label><?php esc_html_e( 'کد پترن OTP', 'neomorph-core' ); ?>
							<input type="text" name="neomorph_core_settings[meli_pattern]" value="<?php echo esc_attr( $s['meli_pattern'] ?? '' ); ?>" class="regular-text"></label>
					</section>

					<section class="nmc-card">
						<h2>💬 <?php esc_html_e( 'بله و تلگرام', 'neomorph-core' ); ?></h2>
						<h3><?php esc_html_e( 'ربات بله', 'neomorph-core' ); ?></h3>
						<label><?php esc_html_e( 'توکن ربات', 'neomorph-core' ); ?>
							<input type="text" name="neomorph_core_settings[bale_token]" value="<?php echo esc_attr( $s['bale_token'] ?? '' ); ?>" class="regular-text"></label>
						<label><?php esc_html_e( 'شناسه گفتگو (Chat ID)', 'neomorph-core' ); ?>
							<input type="text" name="neomorph_core_settings[bale_chat_id]" value="<?php echo esc_attr( $s['bale_chat_id'] ?? '' ); ?>" class="regular-text"></label>
						<h3><?php esc_html_e( 'ربات تلگرام', 'neomorph-core' ); ?></h3>
						<label><?php esc_html_e( 'توکن ربات', 'neomorph-core' ); ?>
							<input type="text" name="neomorph_core_settings[telegram_token]" value="<?php echo esc_attr( $s['telegram_token'] ?? '' ); ?>" class="regular-text"></label>
						<label><?php esc_html_e( 'شناسه چت/کانال', 'neomorph-core' ); ?>
							<input type="text" name="neomorph_core_settings[telegram_chat_id]" value="<?php echo esc_attr( $s['telegram_chat_id'] ?? '' ); ?>" class="regular-text"></label>
						<p class="description"><?php esc_html_e( 'اعلان‌های ادمین (فاکتور جدید، تیکت جدید، خلاصه روزانه) روی این کانال‌ها ارسال می‌شود.', 'neomorph-core' ); ?></p>
					</section>

					<section class="nmc-card">
						<h2>💳 <?php esc_html_e( 'درگاه پرداخت', 'neomorph-core' ); ?></h2>
						<label><?php esc_html_e( 'درگاه فعال', 'neomorph-core' ); ?>
							<select name="neomorph_core_settings[payment_gateway]">
								<?php
								$pg = isset( $s['payment_gateway'] ) ? $s['payment_gateway'] : 'zarinpal';
								printf( '<option value="zarinpal" %s>Zarinpal</option>', selected( $pg, 'zarinpal', false ) );
								printf( '<option value="idpay" %s>IDPay</option>', selected( $pg, 'idpay', false ) );
								printf( '<option value="manual" %s>%s</option>', selected( $pg, 'manual', false ), esc_html__( 'پرداخت دستی/کارت‌به‌کارت', 'neomorph-core' ) );
								?>
							</select>
						</label>
						<label><?php esc_html_e( 'مرچنت کد زرین‌پال', 'neomorph-core' ); ?>
							<input type="text" name="neomorph_core_settings[zarinpal_merchant]" value="<?php echo esc_attr( $s['zarinpal_merchant'] ?? '' ); ?>" class="regular-text"></label>
						<label><?php esc_html_e( 'کلید API آی‌دی‌پی', 'neomorph-core' ); ?>
							<input type="text" name="neomorph_core_settings[idpay_api_key]" value="<?php echo esc_attr( $s['idpay_api_key'] ?? '' ); ?>" class="regular-text"></label>
					</section>

					<section class="nmc-card">
						<h2>🎁 <?php esc_html_e( 'باشگاه مشتریان و OTP', 'neomorph-core' ); ?></h2>
						<label><?php esc_html_e( 'امتیاز به ازای هر ۱۰,۰۰۰ تومان خرید', 'neomorph-core' ); ?>
							<input type="number" name="neomorph_core_settings[points_per_toman]" value="<?php echo esc_attr( $s['points_per_toman'] ?? '1' ); ?>" class="small-text"></label>
						<label><?php esc_html_e( 'امتیاز هدیه ثبت‌نام', 'neomorph-core' ); ?>
							<input type="number" name="neomorph_core_settings[signup_points]" value="<?php echo esc_attr( $s['signup_points'] ?? '100' ); ?>" class="small-text"></label>
						<label><?php esc_html_e( 'ثبت‌نام خودکار با OTP', 'neomorph-core' ); ?>
							<select name="neomorph_core_settings[otp_auto_register]">
								<?php
								$ar = $s['otp_auto_register'] ?? '1';
								printf( '<option value="1" %s>%s</option>', selected( $ar, '1', false ), esc_html__( 'فعال', 'neomorph-core' ) );
								printf( '<option value="0" %s>%s</option>', selected( $ar, '0', false ), esc_html__( 'غیرفعال', 'neomorph-core' ) );
								?>
							</select>
						</label>
					</section>
				</div>
				<?php submit_button( esc_html__( 'ذخیره تنظیمات', 'neomorph-core' ) ); ?>
			</form>
		</div>
		<?php
	}
}

SettingsPage::init();
