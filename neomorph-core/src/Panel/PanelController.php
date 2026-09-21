<?php
/**
 * Customer panel + auth forms (shortcodes).
 *
 * [neomorph_login]   — OTP / password login
 * [neomorph_register]— signup with OTP + loyalty notice
 * [neomorph_panel]   — full customer panel:
 *      dashboard | orders | finance | tickets | loyalty | consulting | profile
 *
 * @package NeomorphCore\Panel
 */

namespace NeomorphCore\Panel;

use NeomorphCore\Auth\OtpController;
use NeomorphCore\Finance\Invoice;
use NeomorphCore\Integrations\Notifier;
use NeomorphCore\Loyalty\LoyaltyEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PanelController
 */
final class PanelController {

	public static function init() {
		add_shortcode( 'neomorph_login', array( __CLASS__, 'login_shortcode' ) );
		add_shortcode( 'neomorph_register', array( __CLASS__, 'register_shortcode' ) );
		add_shortcode( 'neomorph_panel', array( __CLASS__, 'panel_shortcode' ) );
		add_action( 'admin_post_nmc_update_profile', array( __CLASS__, 'handle_profile' ) );
		add_action( 'admin_post_nopriv_nmc_update_profile', array( __CLASS__, 'handle_profile' ) );
		add_action( 'admin_post_nmc_consult_create', array( __CLASS__, 'handle_consult' ) );
		add_action( 'admin_post_nopriv_nmc_consult_create', array( __CLASS__, 'handle_consult' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'maybe_enqueue' ) );
	}

	/**
	 * Enqueue panel assets when shortcodes present (also forced by widgets).
	 */
	public static function maybe_enqueue() {
		wp_register_style( 'nmc-panel', NEOMORPH_CORE_URL . 'assets/css/panel.css', array(), NEOMORPH_CORE_VERSION );
		wp_register_script( 'nmc-otp', NEOMORPH_CORE_URL . 'assets/js/otp.js', array(), NEOMORPH_CORE_VERSION, true );
		wp_register_script( 'nmc-panel-js', NEOMORPH_CORE_URL . 'assets/js/panel.js', array( 'jquery' ), NEOMORPH_CORE_VERSION, true );
	}

	/**
	 * Shared localizations.
	 */
	private static function localize() {
		self::maybe_enqueue();
		wp_enqueue_style( 'nmc-panel' );
		wp_enqueue_script( 'nmc-otp' );
		wp_enqueue_script( 'nmc-panel-js' );
		wp_localize_script(
			'nmc-otp',
			'nmcOtp',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'nmc_otp' ),
				'panelNonce' => wp_create_nonce( 'nmc_panel' ),
				'messages' => array(
					'sending' => esc_html__( 'در حال ارسال کد…', 'neomorph-core' ),
					'sent'    => esc_html__( 'کد تأیید ارسال شد.', 'neomorph-core' ),
					'error'   => esc_html__( 'خطا رخ داد؛ دوباره تلاش کنید.', 'neomorph-core' ),
					'verify'  => esc_html__( 'در حال تأیید…', 'neomorph-core' ),
				),
			)
		);
		wp_localize_script(
			'nmc-panel-js',
			'nmcPanel',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'nmc_panel' ),
			)
		);
	}

	/* ── Login ───────────────────────────────────────────────────── */

	/**
	 * [neomorph_login title subtitle methods="otp,password" layout="card|split|inline" register_text register_url]
	 */
	public static function login_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'title'        => esc_html__( 'ورود به حساب کاربری', 'neomorph-core' ),
				'subtitle'     => esc_html__( 'با شماره موبایل و کد یک‌بار مصرف وارد شوید', 'neomorph-core' ),
				'methods'      => 'otp,password',
				'layout'       => 'card',
				'register_text'=> esc_html__( 'حساب ندارید؟ ثبت‌نام کنید', 'neomorph-core' ),
				'register_url' => '',
			),
			$atts,
			'neomorph_login'
		);
		self::localize();

		if ( is_user_logged_in() ) {
			return '<div class="neo-surface neo-auth"><p>' . esc_html__( 'شما وارد شده‌اید.', 'neomorph-core' ) . '</p><a class="neo-btn neo-btn--primary" href="' . esc_url( self::panel_url() ) . '">' . esc_html__( 'رفتن به پنل', 'neomorph-core' ) . '</a></div>';
		}

		$methods = array_filter( array_map( 'trim', explode( ',', $atts['methods'] ) ) );
		$register_url = $atts['register_url'] ? $atts['register_url'] : self::panel_url( 'register' );

		ob_start();
		?>
		<div class="neo-surface neo-auth neo-auth--<?php echo esc_attr( $atts['layout'] ); ?>" data-mode="login">
			<h2 class="neo-section__title"><?php echo esc_html( $atts['title'] ); ?></h2>
			<p class="neo-muted"><?php echo esc_html( $atts['subtitle'] ); ?></p>

			<?php if ( in_array( 'otp', $methods, true ) ) : ?>
				<form class="neo-form neo-otp-form" data-otp-form>
					<label class="neo-label"><?php esc_html_e( 'شماره موبایل', 'neomorph-core' ); ?>
						<span class="otp-row">
							<input class="neo-input" type="tel" name="phone" inputmode="numeric" placeholder="09…" required>
							<button type="button" class="neo-btn" data-otp-send><?php esc_html_e( 'ارسال کد', 'neomorph-core' ); ?></button>
						</span>
					</label>
					<span class="otp-timer" data-otp-timer-text></span>

					<div class="otp-verify-row" hidden>
						<label class="neo-label"><?php esc_html_e( 'کد تأیید', 'neomorph-core' ); ?>
							<span class="otp-boxes">
								<?php for ( $i = 0; $i < 5; $i++ ) : ?><input type="text" inputmode="numeric" maxlength="1"><?php endfor; ?>
							</span>
							<input type="hidden" name="otp_code">
						</label>
						<button type="button" class="neo-btn neo-btn--primary neo-btn--block" data-otp-verify><?php esc_html_e( 'ورود', 'neomorph-core' ); ?></button>
						<button type="button" class="neo-btn neo-btn--ghost neo-btn--block" data-otp-resend disabled><?php esc_html_e( 'ارسال مجدد کد', 'neomorph-core' ); ?></button>
					</div>
					<p class="neo-form__msg" aria-live="polite"></p>
				</form>
			<?php endif; ?>

			<?php if ( in_array( 'password', $methods, true ) ) : ?>
				<details class="neo-auth__alt">
					<summary><?php esc_html_e( 'ورود با رمز عبور', 'neomorph-core' ); ?></summary>
					<form class="neo-form" method="post" action="<?php echo esc_url( wp_login_url() ); ?>">
						<label class="neo-label"><?php esc_html_e( 'نام کاربری', 'neomorph-core' ); ?><input class="neo-input" type="text" name="log"></label>
						<label class="neo-label"><?php esc_html_e( 'رمز عبور', 'neomorph-core' ); ?><input class="neo-input" type="password" name="pwd"></label>
						<input type="hidden" name="redirect_to" value="<?php echo esc_url( self::panel_url() ); ?>">
						<button class="neo-btn neo-btn--primary neo-btn--block" type="submit"><?php esc_html_e( 'ورود', 'neomorph-core' ); ?></button>
					</form>
				</details>
			<?php endif; ?>

			<?php if ( $atts['register_text'] ) : ?>
				<p class="neo-auth__foot"><a href="<?php echo esc_url( $register_url ); ?>"><?php echo esc_html( $atts['register_text'] ); ?></a></p>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/* ── Register ────────────────────────────────────────────────── */

	/**
	 * [neomorph_register …] — same OTP flow with name fields (handled in verify AJAX).
	 */
	public static function register_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'title'       => esc_html__( 'ساخت حساب کاربری', 'neomorph-core' ),
				'subtitle'    => esc_html__( 'ثبت‌نام با شماره موبایل و کد تأیید', 'neomorph-core' ),
				'fields'      => 'first_name,last_name,phone,email,password',
				'loyalty'     => 'yes',
				'loyalty_text'=> esc_html__( 'با ثبت‌نام، عضو باشگاه مشتریان می‌شوید و امتیاز هدیه بگیرید.', 'neomorph-core' ),
				'terms'       => esc_html__( 'قوانین و مقررات را می‌پذیرم', 'neomorph-core' ),
				'login_text'  => esc_html__( 'قبلاً ثبت‌نام کرده‌اید؟ ورود', 'neomorph-core' ),
				'login_url'   => '',
			),
			$atts,
			'neomorph_register'
		);
		self::localize();
		$fields = array_filter( array_map( 'trim', explode( ',', $atts['fields'] ) ) );
		$login_url = $atts['login_url'] ? $atts['login_url'] : self::panel_url( 'login' );

		ob_start();
		?>
		<div class="neo-surface neo-auth neo-auth--register" data-mode="register">
			<h2 class="neo-section__title"><?php echo esc_html( $atts['title'] ); ?></h2>
			<p class="neo-muted"><?php echo esc_html( $atts['subtitle'] ); ?></p>
			<?php if ( 'yes' === $atts['loyalty'] && $atts['loyalty_text'] ) : ?>
				<p class="neo-badge neo-badge--success"><?php echo esc_html( $atts['loyalty_text'] ); ?></p>
			<?php endif; ?>

			<form class="neo-form neo-otp-form" data-otp-form>
				<div class="neo-form__row">
					<?php if ( in_array( 'first_name', $fields, true ) ) : ?>
						<label class="neo-label"><?php esc_html_e( 'نام', 'neomorph-core' ); ?><input class="neo-input" type="text" name="first_name"></label>
					<?php endif; ?>
					<?php if ( in_array( 'last_name', $fields, true ) ) : ?>
						<label class="neo-label"><?php esc_html_e( 'نام خانوادگی', 'neomorph-core' ); ?><input class="neo-input" type="text" name="last_name"></label>
					<?php endif; ?>
				</div>
				<?php if ( in_array( 'email', $fields, true ) ) : ?>
					<label class="neo-label"><?php esc_html_e( 'ایمیل', 'neomorph-core' ); ?><input class="neo-input" type="email" name="email"></label>
				<?php endif; ?>
				<label class="neo-label"><?php esc_html_e( 'شماره موبایل', 'neomorph-core' ); ?> *
					<span class="otp-row">
						<input class="neo-input" type="tel" name="phone" inputmode="numeric" placeholder="09…" required>
						<button type="button" class="neo-btn" data-otp-send><?php esc_html_e( 'ارسال کد', 'neomorph-core' ); ?></button>
					</span>
				</label>
				<span class="otp-timer" data-otp-timer-text></span>

				<div class="otp-verify-row" hidden>
					<label class="neo-label"><?php esc_html_e( 'کد تأیید', 'neomorph-core' ); ?>
						<span class="otp-boxes">
							<?php for ( $i = 0; $i < 5; $i++ ) : ?><input type="text" inputmode="numeric" maxlength="1"><?php endfor; ?>
						</span>
						<input type="hidden" name="otp_code">
					</label>
					<?php if ( $atts['terms'] ) : ?>
						<label class="neo-label neo-check"><input type="checkbox" name="terms" required> <?php echo esc_html( $atts['terms'] ); ?></label>
					<?php endif; ?>
					<button type="button" class="neo-btn neo-btn--primary neo-btn--block" data-otp-verify><?php esc_html_e( 'ثبت‌نام', 'neomorph-core' ); ?></button>
					<button type="button" class="neo-btn neo-btn--ghost neo-btn--block" data-otp-resend disabled><?php esc_html_e( 'ارسال مجدد کد', 'neomorph-core' ); ?></button>
				</div>
				<p class="neo-form__msg" aria-live="polite"></p>
			</form>

			<?php if ( $atts['login_text'] ) : ?>
				<p class="neo-auth__foot"><a href="<?php echo esc_url( $login_url ); ?>"><?php echo esc_html( $atts['login_text'] ); ?></a></p>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/* ── Panel ───────────────────────────────────────────────────── */

	/**
	 * [neomorph_panel layout="sidebar|tabs|cards" tabs="dashboard,orders,…" guest="login|message"]
	 */
	public static function panel_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'layout' => 'sidebar',
				'tabs'   => 'dashboard,orders,finance,tickets,loyalty,consulting,profile',
				'guest'  => 'login',
			),
			$atts,
			'neomorph_panel'
		);
		self::localize();

		if ( ! is_user_logged_in() ) {
			if ( 'message' === $atts['guest'] ) {
				return '<div class="neo-surface neo-empty"><p>' . esc_html__( 'برای مشاهده پنل، وارد شوید.', 'neomorph-core' ) . '</p><a class="neo-btn neo-btn--primary" href="' . esc_url( self::panel_url( 'login' ) ) . '">' . esc_html__( 'ورود', 'neomorph-core' ) . '</a></div>';
			}
			return self::login_shortcode( array() );
		}

		$tabs = array_filter( array_map( 'trim', explode( ',', $atts['tabs'] ) ) );
		$labels = self::tab_labels();
		$active = isset( $_GET['neo-tab'] ) ? sanitize_key( $_GET['neo-tab'] ) : ( $tabs ? $tabs[0] : 'dashboard' ); // phpcs:ignore WordPress.Security.NonceVerification
		if ( ! in_array( $active, $tabs, true ) ) {
			$active = 'dashboard';
		}

		ob_start();
		?>
		<div class="neo-panel neo-panel--<?php echo esc_attr( $atts['layout'] ); ?>" data-neo-panel-root>
			<nav class="neo-panel__nav neo-surface" aria-label="<?php esc_attr_e( 'منوی پنل', 'neomorph-core' ); ?>">
				<?php foreach ( $tabs as $slug ) : ?>
					<a href="#<?php echo esc_attr( $slug ); ?>" data-neo-tab="<?php echo esc_attr( $slug ); ?>" class="<?php echo $active === $slug ? 'is-active' : ''; ?>">
						<?php echo esc_html( isset( $labels[ $slug ] ) ? $labels[ $slug ] : $slug ); ?>
					</a>
				<?php endforeach; ?>
				<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>">🚪 <?php esc_html_e( 'خروج', 'neomorph-core' ); ?></a>
			</nav>

			<div class="neo-panel__body">
				<?php foreach ( $tabs as $slug ) : ?>
					<section class="neo-panel__section neo-surface" data-neo-panel="<?php echo esc_attr( $slug ); ?>" <?php echo $active === $slug ? '' : 'hidden'; ?>>
						<?php
						$method = 'tab_' . $slug;
						if ( method_exists( __CLASS__, $method ) ) {
							self::$method();
						}
						?>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Tab labels.
	 */
	public static function tab_labels() {
		return array(
			'dashboard'  => '🏠 ' . esc_html__( 'داشبورد', 'neomorph-core' ),
			'orders'     => '🛍 ' . esc_html__( 'خریدهای انجام شده', 'neomorph-core' ),
			'finance'    => '💳 ' . esc_html__( 'مالی و حسابداری', 'neomorph-core' ),
			'tickets'    => '🎫 ' . esc_html__( 'تیکت و پشتیبانی', 'neomorph-core' ),
			'loyalty'    => '🎁 ' . esc_html__( 'باشگاه مشتریان', 'neomorph-core' ),
			'consulting' => '💬 ' . esc_html__( 'مشاوره و درخواست', 'neomorph-core' ),
			'profile'    => '👤 ' . esc_html__( 'پروفایل', 'neomorph-core' ),
		);
	}

	/**
	 * Panel page URL helper.
	 */
	public static function panel_url( $view = '' ) {
		$page = (int) nmc_setting( 'panel_page', 0 );
		$url  = $page ? get_permalink( $page ) : home_url( '/panel/' );
		if ( 'login' === $view ) {
			return add_query_arg( 'neo-auth', 'login', $url );
		}
		if ( 'register' === $view ) {
			return add_query_arg( 'neo-auth', 'register', $url );
		}
		return $url;
	}

	/* ── Tabs ────────────────────────────────────────────────────── */

	/**
	 * Dashboard: greeting, quick stats, next follow-ups.
	 */
	public static function tab_dashboard() {
		$user    = wp_get_current_user();
		$balance = LoyaltyEngine::balance( $user->ID );
		$tier    = LoyaltyEngine::tier_of( $user->ID );
		$invoices = get_posts(
			array(
				'post_type' => 'nmc_invoice', 'numberposts' => -1, 'fields' => 'ids',
				'meta_key'  => '_nmc_user_id', 'meta_value' => $user->ID, // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);
		$unpaid = 0;
		foreach ( $invoices as $inv ) {
			if ( 'unpaid' === \nmc_get_meta( $inv, 'status' ) ) {
				$unpaid++;
			}
		}
		$tickets = count(
			get_posts(
				array(
					'post_type' => 'nmc_ticket', 'numberposts' => -1, 'fields' => 'ids',
					'meta_key'  => '_nmc_user_id', 'meta_value' => $user->ID, // phpcs:ignore WordPress.DB.SlowDBQuery
				)
			)
		);
		printf( '<h2>%s %s 👋</h2>', esc_html__( 'سلام', 'neomorph-core' ), esc_html( $user->display_name ) );
		echo '<div class="neo-grid neo-grid--4">';
		printf( '<div class="neo-card"><span class="neo-stat__number">%s</span><span class="neo-stat__label">%s</span></div>', esc_html( number_format_i18n( $balance ) ), esc_html__( 'امتیاز باشگاه', 'neomorph-core' ) );
		printf( '<div class="neo-card"><span class="neo-stat__number">%s</span><span class="neo-stat__label">%s</span></div>', esc_html( $tier ? $tier['label'] : '—' ), esc_html__( 'سطح عضویت', 'neomorph-core' ) );
		printf( '<div class="neo-card"><span class="neo-stat__number">%d</span><span class="neo-stat__label">%s</span></div>', count( $invoices ), esc_html__( 'فاکتور', 'neomorph-core' ) );
		printf( '<div class="neo-card"><span class="neo-stat__number">%d</span><span class="neo-stat__label">%s</span></div>', (int) $tickets, esc_html__( 'تیکت', 'neomorph-core' ) );
		echo '</div>';
		if ( $unpaid ) {
			printf(
				'<p class="neo-badge neo-badge--danger">%s</p>',
				esc_html( sprintf( 'شما %d فاکتور پرداخت‌نشده دارید.', $unpaid ) )
			);
		}
		echo '<p class="neo-muted">' . esc_html__( 'از منوی کناری بخش‌های خریدها، مالی، تیکت، باشگاه مشتریان و درخواست مشاوره در دسترس است.', 'neomorph-core' ) . '</p>';
	}

	/**
	 * Orders tab (WooCommerce my-orders style).
	 */
	public static function tab_orders() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<p class="neo-empty">' . esc_html__( 'فروشگاه فعال نیست.', 'neomorph-core' ) . '</p>';
			return;
		}
		echo do_shortcode( '[woocommerce_my_account]' ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Finance tab: invoices list + balances.
	 */
	public static function tab_finance() {
		$user_id  = get_current_user_id();
		$invoices = get_posts(
			array(
				'post_type'   => 'nmc_invoice',
				'numberposts' => 50,
				'fields'      => 'ids',
				'meta_key'    => '_nmc_user_id', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'  => $user_id, // phpcs:ignore WordPress.DB.SlowDBQuery
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);
		$ltv = \NeomorphCore\Crm\ContactController::lifetime_value( $user_id );
		printf( '<h3>%s</h3><p class="neo-badge">%s %s</p>', esc_html__( 'مالی و حسابداری', 'neomorph-core' ), esc_html__( 'ارزش خرید شما:', 'neomorph-core' ), esc_html( \nmc_price( $ltv ) ) );

		if ( ! $invoices ) {
			echo '<p class="neo-empty">' . esc_html__( 'هنوز فاکتوری ندارید.', 'neomorph-core' ) . '</p>';
			return;
		}
		echo '<table class="neo-table"><thead><tr><th>' . esc_html__( 'شماره', 'neomorph-core' ) . '</th><th>' . esc_html__( 'تاریخ', 'neomorph-core' ) . '</th><th>' . esc_html__( 'مبلغ', 'neomorph-core' ) . '</th><th>' . esc_html__( 'وضعیت', 'neomorph-core' ) . '</th><th></th></tr></thead><tbody>';
		foreach ( $invoices as $inv ) {
			$status   = \nmc_get_meta( $inv, 'status' );
			$statuses = array( 'paid' => array( 'success', 'پرداخت شده' ), 'unpaid' => array( 'info', 'در انتظار پرداخت' ), 'cancelled' => array( 'danger', 'لغو شده' ), 'expired' => array( 'danger', 'منقضی' ), 'draft' => array( 'info', 'پیش‌نویس' ) );
			$badge    = isset( $statuses[ $status ] ) ? $statuses[ $status ] : array( 'info', $status );
			printf(
				'<tr><td>%s</td><td>%s</td><td>%s</td><td><span class="neo-badge neo-badge--%s">%s</span></td><td><a class="neo-btn neo-btn--sm" href="%s">%s</a></td></tr>',
				esc_html( \nmc_get_meta( $inv, 'number' ) ),
				esc_html( \nmc_get_meta( $inv, 'created_at' ) ),
				esc_html( \nmc_price( \nmc_get_meta( $inv, 'total', 0 ) ) ),
				esc_attr( $badge[0] ),
				esc_html( $badge[1] ),
				esc_url( Invoice::payment_url( $inv ) ),
				esc_html__( 'مشاهده / پرداخت', 'neomorph-core' )
			);
		}
		echo '</tbody></table>';
	}

	/**
	 * Tickets tab: list + create form (+ open thread).
	 */
	public static function tab_tickets() {
		$user_id  = get_current_user_id();
		$tickets  = get_posts(
			array(
				'post_type'   => 'nmc_ticket',
				'numberposts' => 50,
				'fields'      => 'ids',
				'meta_key'    => '_nmc_user_id', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'  => $user_id, // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);

		$open_id = isset( $_GET['ticket'] ) ? (int) $_GET['ticket'] : 0; // phpcs:ignore WordPress.Security.NonceVerification
		if ( $open_id && (int) \nmc_get_meta( $open_id, 'user_id' ) === $user_id ) {
			self::render_ticket_thread( $open_id );
			return;
		}

		printf( '<h3>%s</h3>', esc_html__( 'تیکت‌های پشتیبانی', 'neomorph-core' ) );
		if ( $tickets ) {
			echo '<table class="neo-table"><thead><tr><th>' . esc_html__( 'شماره', 'neomorph-core' ) . '</th><th>' . esc_html__( 'عنوان', 'neomorph-core' ) . '</th><th>' . esc_html__( 'وضعیت', 'neomorph-core' ) . '</th><th></th></tr></thead><tbody>';
			foreach ( $tickets as $t ) {
				$status = \nmc_get_meta( $t, 'status', 'open' );
				$map    = array( 'open' => array( 'info', 'باز' ), 'answered' => array( 'success', 'پاسخ داده شد' ), 'closed' => array( 'danger', 'بسته' ) );
				$badge  = isset( $map[ $status ] ) ? $map[ $status ] : array( 'info', $status );
				printf(
					'<tr><td>%s</td><td>%s</td><td><span class="neo-badge neo-badge--%s">%s</span></td><td><a class="neo-btn neo-btn--sm" href="%s">%s</a></td></tr>',
					esc_html( \nmc_get_meta( $t, 'number' ) ),
					esc_html( get_the_title( $t ) ),
					esc_attr( $badge[0] ),
					esc_html( $badge[1] ),
					esc_url( add_query_arg( array( 'neo-tab' => 'tickets', 'ticket' => $t ), self::panel_url() ) ),
					esc_html__( 'ادامه گفتگو', 'neomorph-core' )
				);
			}
			echo '</tbody></table>';
		}

		$depts = get_terms( array( 'taxonomy' => 'ticket_dept', 'hide_empty' => false ) );
		?>
		<h4><?php esc_html_e( 'ثبت تیکت جدید', 'neomorph-core' ); ?></h4>
		<form class="neo-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'nmc_ticket_create' ); ?>
			<input type="hidden" name="action" value="nmc_ticket_create">
			<label class="neo-label"><?php esc_html_e( 'عنوان', 'neomorph-core' ); ?><input class="neo-input" type="text" name="ticket_title" required></label>
			<label class="neo-label"><?php esc_html_e( 'واحد', 'neomorph-core' ); ?>
				<select name="ticket_dept">
					<?php foreach ( $depts as $dept ) : ?>
						<option value="<?php echo esc_attr( $dept->term_id ); ?>"><?php echo esc_html( $dept->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<label class="neo-label"><?php esc_html_e( 'اولویت', 'neomorph-core' ); ?>
				<select name="ticket_priority">
					<option value="normal"><?php esc_html_e( 'عادی', 'neomorph-core' ); ?></option>
					<option value="high"><?php esc_html_e( 'مهم', 'neomorph-core' ); ?></option>
					<option value="urgent"><?php esc_html_e( 'فوری', 'neomorph-core' ); ?></option>
				</select>
			</label>
			<label class="neo-label"><?php esc_html_e( 'پیام', 'neomorph-core' ); ?><textarea class="neo-input neo-textarea" name="ticket_message" rows="5" required></textarea></label>
			<button class="neo-btn neo-btn--primary" type="submit"><?php esc_html_e( 'ارسال تیکت', 'neomorph-core' ); ?></button>
		</form>
		<?php
	}

	/**
	 * Ticket thread view + AJAX reply box.
	 */
	private static function render_ticket_thread( $ticket_id ) {
		$messages = (array) \nmc_get_meta( $ticket_id, 'messages', array() );
		printf( '<h3>%s %s</h3>', esc_html__( 'تیکت', 'neomorph-core' ), esc_html( \nmc_get_meta( $ticket_id, 'number' ) ) );
		echo '<div class="nmc-thread neo-inset">';
		foreach ( $messages as $msg ) {
			printf(
				'<div class="neo-ticket__msg neo-ticket__msg--%s"><strong>%s</strong> <span class="neo-muted">%s</span><p>%s</p></div>',
				'user' === $msg['role'] ? 'user' : 'staff',
				'user' === $msg['role'] ? esc_html__( 'شما', 'neomorph-core' ) : esc_html__( 'پشتیبانی', 'neomorph-core' ),
				esc_html( $msg['time'] ),
				nl2br( esc_html( $msg['text'] ) )
			);
		}
		echo '</div>';
		?>
		<form class="neo-form" data-ticket-reply data-ticket="<?php echo esc_attr( $ticket_id ); ?>">
			<label class="neo-label"><?php esc_html_e( 'پاسخ شما', 'neomorph-core' ); ?><textarea class="neo-input neo-textarea" name="message" rows="4" required></textarea></label>
			<button class="neo-btn neo-btn--primary" type="submit"><?php esc_html_e( 'ارسال پاسخ', 'neomorph-core' ); ?></button>
			<p class="neo-form__msg" aria-live="polite"></p>
		</form>
		<p><a class="neo-btn neo-btn--ghost" href="<?php echo esc_url( add_query_arg( 'neo-tab', 'tickets', self::panel_url() ) ); ?>"><?php esc_html_e( 'بازگشت به لیست', 'neomorph-core' ); ?></a></p>
		<?php
	}

	/**
	 * Loyalty tab.
	 */
	public static function tab_loyalty() {
		echo do_shortcode( '[neomorph_loyalty view="card"]' ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<br>' . do_shortcode( '[neomorph_loyalty view="rewards" title="' . esc_attr__( 'تعویض امتیاز', 'neomorph-core' ) . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Consulting tab: consultation / support request form + history.
	 */
	public static function tab_consulting() {
		$user_id = get_current_user_id();
		$requests = get_posts(
			array(
				'post_type'   => 'nmc_consult',
				'numberposts' => 20,
				'fields'      => 'ids',
				'meta_key'    => '_nmc_user_id', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'  => $user_id, // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);
		printf( '<h3>%s</h3>', esc_html__( 'درخواست مشاوره و پشتیبانی', 'neomorph-core' ) );

		$services = get_terms( array( 'taxonomy' => 'crm_service', 'hide_empty' => false ) );
		?>
		<form class="neo-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'nmc_consult_create' ); ?>
			<input type="hidden" name="action" value="nmc_consult_create">
			<label class="neo-label"><?php esc_html_e( 'موضوع', 'neomorph-core' ); ?><input class="neo-input" type="text" name="consult_title" required></label>
			<label class="neo-label"><?php esc_html_e( 'نوع درخواست', 'neomorph-core' ); ?>
				<select name="consult_type">
					<option value="consult"><?php esc_html_e( 'مشاوره', 'neomorph-core' ); ?></option>
					<option value="support"><?php esc_html_e( 'پشتیبانی', 'neomorph-core' ); ?></option>
					<option value="quote"><?php esc_html_e( 'درخواست پیشنهاد قیمت', 'neomorph-core' ); ?></option>
				</select>
			</label>
			<label class="neo-label"><?php esc_html_e( 'خدمت مورد نظر', 'neomorph-core' ); ?>
				<select name="consult_service">
					<?php foreach ( $services as $srv ) : ?>
						<option value="<?php echo esc_attr( $srv->term_id ); ?>"><?php echo esc_html( $srv->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<label class="neo-label"><?php esc_html_e( 'توضیحات', 'neomorph-core' ); ?><textarea class="neo-input neo-textarea" name="consult_message" rows="5" required></textarea></label>
			<button class="neo-btn neo-btn--primary" type="submit"><?php esc_html_e( 'ثبت درخواست', 'neomorph-core' ); ?></button>
		</form>
		<?php
		if ( $requests ) {
			echo '<h4>' . esc_html__( 'درخواست‌های قبلی', 'neomorph-core' ) . '</h4><table class="neo-table"><tbody>';
			foreach ( $requests as $r ) {
				printf(
					'<tr><td>%s</td><td>%s</td><td>%s</td></tr>',
					esc_html( get_the_title( $r ) ),
					esc_html( \nmc_get_meta( $r, 'consult_type' ) ),
					esc_html( \nmc_get_meta( $r, 'created_at' ) )
				);
			}
			echo '</tbody></table>';
		}
	}

	/**
	 * Profile tab: name/phone (re-verify)/email + social chat ids + password.
	 */
	public static function tab_profile() {
		$user = wp_get_current_user();
		?>
		<h3><?php esc_html_e( 'پروفایل', 'neomorph-core' ); ?></h3>
		<form class="neo-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'nmc_update_profile' ); ?>
			<input type="hidden" name="action" value="nmc_update_profile">
			<div class="neo-form__row">
				<label class="neo-label"><?php esc_html_e( 'نام', 'neomorph-core' ); ?><input class="neo-input" type="text" name="first_name" value="<?php echo esc_attr( $user->first_name ); ?>"></label>
				<label class="neo-label"><?php esc_html_e( 'نام خانوادگی', 'neomorph-core' ); ?><input class="neo-input" type="text" name="last_name" value="<?php echo esc_attr( $user->last_name ); ?>"></label>
			</div>
			<label class="neo-label"><?php esc_html_e( 'ایمیل', 'neomorph-core' ); ?><input class="neo-input" type="email" name="email" value="<?php echo esc_attr( get_user_meta( $user->ID, 'nmc_placeholder_email', true ) ? '' : $user->user_email ); ?>"></label>
			<label class="neo-label"><?php esc_html_e( 'شماره موبایل', 'neomorph-core' ); ?><input class="neo-input" type="tel" name="phone" value="<?php echo esc_attr( \nmc_user_phone( $user->ID ) ); ?>"></label>
			<label class="neo-label"><?php esc_html_e( 'شناسه گفتگوی بله (اختیاری)', 'neomorph-core' ); ?><input class="neo-input" type="text" name="bale_chat" value="<?php echo esc_attr( get_user_meta( $user->ID, 'nmc_bale_chat', true ) ); ?>"></label>
			<label class="neo-label"><?php esc_html_e( 'شناسه چت تلگرام (اختیاری)', 'neomorph-core' ); ?><input class="neo-input" type="text" name="telegram_chat" value="<?php echo esc_attr( get_user_meta( $user->ID, 'nmc_telegram_chat', true ) ); ?>"></label>
			<label class="neo-label"><?php esc_html_e( 'رمز عبور جدید (اختیاری)', 'neomorph-core' ); ?><input class="neo-input" type="password" name="new_password"></label>
			<button class="neo-btn neo-btn--primary" type="submit"><?php esc_html_e( 'ذخیره تغییرات', 'neomorph-core' ); ?></button>
		</form>
		<?php
	}

	/* ── Handlers ────────────────────────────────────────────────── */

	/**
	 * Profile update.
	 */
	public static function handle_profile() {
		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'ابتدا وارد شوید.', 'neomorph-core' ) );
		}
		check_admin_referer( 'nmc_update_profile' );
		$user_id = get_current_user_id();

		$first = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
		$last  = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
		wp_update_user( array( 'ID' => $user_id, 'first_name' => $first, 'last_name' => $last ) );

		if ( ! empty( $_POST['email'] ) ) {
			$email = sanitize_email( wp_unslash( $_POST['email'] ) );
			if ( $email && ! email_exists( $email ) ) {
				wp_update_user( array( 'ID' => $user_id, 'user_email' => $email ) );
				delete_user_meta( $user_id, 'nmc_placeholder_email' );
			}
		}
		if ( ! empty( $_POST['phone'] ) ) {
			update_user_meta( $user_id, 'phone', \nmc_normalize_phone( wp_unslash( $_POST['phone'] ) ) );
		}
		foreach ( array( 'bale_chat' => 'nmc_bale_chat', 'telegram_chat' => 'nmc_telegram_chat' ) as $field => $meta ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_user_meta( $user_id, $meta, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
			}
		}
		if ( ! empty( $_POST['new_password'] ) ) {
			wp_set_password( sanitize_text_field( wp_unslash( $_POST['new_password'] ) ), $user_id );
		}

		wp_safe_redirect( add_query_arg( array( 'neo-tab' => 'profile', 'updated' => 1 ), self::panel_url() ) );
		exit;
	}

	/**
	 * Consultation request.
	 */
	public static function handle_consult() {
		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'ابتدا وارد شوید.', 'neomorph-core' ) );
		}
		check_admin_referer( 'nmc_consult_create' );
		$user_id = get_current_user_id();
		$title   = isset( $_POST['consult_title'] ) ? sanitize_text_field( wp_unslash( $_POST['consult_title'] ) ) : '';
		$type    = isset( $_POST['consult_type'] ) ? sanitize_key( $_POST['consult_type'] ) : 'consult';
		$service = isset( $_POST['consult_service'] ) ? (int) $_POST['consult_service'] : 0;
		$message = isset( $_POST['consult_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['consult_message'] ) ) : '';

		$consult_id = wp_insert_post(
			array(
				'post_type'   => 'nmc_consult',
				'post_status' => 'publish',
				'post_title'  => $title ? $title : esc_html__( 'درخواست مشاوره', 'neomorph-core' ),
				'post_content' => $message,
				'post_author' => $user_id,
			)
		);
		if ( $consult_id ) {
			\nmc_update_meta( $consult_id, 'user_id', $user_id );
			\nmc_update_meta( $consult_id, 'consult_type', $type );
			\nmc_update_meta( $consult_id, 'created_at', current_time( 'mysql' ) );
			if ( $service ) {
				wp_set_object_terms( $consult_id, array( $service ), 'crm_service' );
			}
			Notifier::send( 'admin', sprintf( '💬 درخواست %s جدید از %s: %s', $type, wp_get_current_user()->display_name, $title ) );
			\nmc_do_event( 'consult_requested', array( 'post_id' => $consult_id, 'user_id' => $user_id ) );
		}

		wp_safe_redirect( add_query_arg( array( 'neo-tab' => 'consulting', 'created' => 1 ), self::panel_url() ) );
		exit;
	}
}

PanelController::init();
