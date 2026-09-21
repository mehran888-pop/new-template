<?php
/**
 * Loyalty club (باشگاه مشتریان): points ledger, tiers, rewards, leaderboard.
 *
 * @package NeomorphCore\Loyalty
 */

namespace NeomorphCore\Loyalty;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class LoyaltyEngine
 */
final class LoyaltyEngine {

	public static function init() {
		add_shortcode( 'neomorph_loyalty', array( __CLASS__, 'shortcode' ) );
		add_action( 'nmc_event_invoice_paid', array( __CLASS__, 'hook_invoice_paid' ), 10, 1 );
		add_action( 'nmc_event_user_registered_otp', array( __CLASS__, 'hook_registered' ), 10, 1 );
		add_action( 'wp_ajax_nmc_redeem_reward', array( __CLASS__, 'ajax_redeem' ) );
	}

	/* ── Ledger ──────────────────────────────────────────────────── */

	/**
	 * Add points.
	 *
	 * @param int    $user_id  User.
	 * @param int    $points   Points (positive).
	 * @param string $type     earn|spend|adjust.
	 * @param string $reason   Reason code.
	 * @param string $reference Reference id (invoice…).
	 * @return bool
	 */
	public static function add_points( $user_id, $points, $type = 'earn', $reason = '', $reference = '' ) {
		global $wpdb;
		$points  = (int) $points;
		$user_id = (int) $user_id;
		if ( ! $user_id || ! $points ) {
			return false;
		}
		if ( 'spend' === $type ) {
			$points = -abs( $points );
			if ( self::balance( $user_id ) < abs( $points ) ) {
				return false;
			}
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$ok = $wpdb->insert(
			$wpdb->prefix . 'nmc_points',
			array(
				'user_id'    => $user_id,
				'points'     => $points,
				'type'       => $type,
				'reason'     => $reason,
				'reference'  => (string) $reference,
				'created_at' => current_time( 'mysql' ),
			),
			array( '%d', '%d', '%s', '%s', '%s', '%s' )
		);
		if ( $ok ) {
			\nmc_do_event( 'points_changed', array( 'user_id' => $user_id, 'points' => $points, 'balance' => self::balance( $user_id ) ) );
		}
		return (bool) $ok;
	}

	/**
	 * Current balance.
	 */
	public static function balance( $user_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'nmc_points';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(points),0) FROM {$table} WHERE user_id = %d", $user_id ) );
	}

	/**
	 * Ledger rows for user.
	 */
	public static function history( $user_id, $limit = 20 ) {
		global $wpdb;
		$table = $wpdb->prefix . 'nmc_points';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d ORDER BY id DESC LIMIT %d", $user_id, $limit ), ARRAY_A );
	}

	/* ── Tiers & rewards ─────────────────────────────────────────── */

	/**
	 * Tier thresholds (bronze → silver → gold → vip).
	 */
	public static function tiers() {
		$defaults = array(
			'bronze' => array( 'label' => esc_html__( 'برنزی', 'neomorph-core' ), 'min' => 0, 'bonus' => '۰٪' ),
			'silver' => array( 'label' => esc_html__( 'نقره‌ای', 'neomorph-core' ), 'min' => 1000, 'bonus' => '۵٪' ),
			'gold'   => array( 'label' => esc_html__( 'طلایی', 'neomorph-core' ), 'min' => 5000, 'bonus' => '۱۰٪' ),
			'vip'    => array( 'label' => esc_html__( 'الماسی', 'neomorph-core' ), 'min' => 15000, 'bonus' => '۱۵٪' ),
		);
		return apply_filters( 'nmc_loyalty_tiers', get_option( 'neomorph_core_tiers', $defaults ) );
	}

	/**
	 * Current tier of user.
	 */
	public static function tier_of( $user_id ) {
		$balance = self::balance( $user_id );
		$current = null;
		foreach ( self::tiers() as $slug => $tier ) {
			if ( $balance >= (int) $tier['min'] ) {
				$current = array_merge( array( 'slug' => $slug ), $tier );
			}
		}
		return $current;
	}

	/**
	 * Rewards catalog.
	 */
	public static function rewards() {
		$defaults = array(
			array( 'title' => esc_html__( 'کد تخفیف ۱۰٪', 'neomorph-core' ), 'cost' => 500 ),
			array( 'title' => esc_html__( 'ارسال رایگان', 'neomorph-core' ), 'cost' => 300 ),
			array( 'title' => esc_html__( 'مشاوره رایگان ۳۰ دقیقه‌ای', 'neomorph-core' ), 'cost' => 1200 ),
		);
		return apply_filters( 'nmc_loyalty_rewards', get_option( 'neomorph_core_rewards', $defaults ) );
	}

	/* ── Hooks ───────────────────────────────────────────────────── */

	/**
	 * Earn on paid invoice: 1 point per 10,000 toman (configurable).
	 */
	public static function hook_invoice_paid( $ctx ) {
		if ( empty( $ctx['user_id'] ) ) {
			return;
		}
		$rate   = max( 0, (float) nmc_setting( 'points_per_toman', 1 ) );
		$points = (int) floor( ( (float) $ctx['amount'] / 10000 ) * $rate );
		if ( $points > 0 ) {
			self::add_points( (int) $ctx['user_id'], $points, 'earn', 'invoice_paid', isset( $ctx['post_id'] ) ? (string) $ctx['post_id'] : '' );
		}
	}

	/**
	 * Signup bonus handled in OtpController; this fires tier notification.
	 */
	public static function hook_registered( $ctx ) {
		if ( empty( $ctx['user_id'] ) ) {
			return;
		}
		\nmc_do_event( 'loyalty_threshold', array( 'user_id' => (int) $ctx['user_id'], 'balance' => self::balance( (int) $ctx['user_id'] ) ) );
	}

	/**
	 * AJAX: redeem a reward.
	 */
	public static function ajax_redeem() {
		check_ajax_referer( 'nmc_panel', 'nonce' );
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( esc_html__( 'ابتدا وارد شوید.', 'neomorph-core' ) );
		}
		$index    = isset( $_POST['reward'] ) ? (int) $_POST['reward'] : -1;
		$rewards  = self::rewards();
		$user_id  = get_current_user_id();
		if ( ! isset( $rewards[ $index ] ) ) {
			wp_send_json_error( esc_html__( 'جایزه نامعتبر.', 'neomorph-core' ) );
		}
		$reward = $rewards[ $index ];
		if ( ! self::add_points( $user_id, (int) $reward['cost'], 'spend', 'redeem_' . $index, '' ) ) {
			wp_send_json_error( esc_html__( 'امتیاز کافی نیست.', 'neomorph-core' ) );
		}
		// Deliver reward as notification + create consult ticket-like note.
		\NeomorphCore\Integrations\Notifier::send_to_user(
			$user_id,
			sprintf( '🎁 تعویض امتیاز: %s با موفقیت ثبت شد.', $reward['title'] ),
			array( 'sms' )
		);
		wp_send_json_success( array( 'message' => esc_html__( 'جایزه ثبت شد؛ از طریق پیامک اطلاع‌رسانی شد.', 'neomorph-core' ), 'balance' => self::balance( $user_id ) ) );
	}

	/* ── Shortcode views ─────────────────────────────────────────── */

	/**
	 * [neomorph_loyalty view="card|tiers|rewards|leaderboard|intro" title="" count=""]
	 */
	public static function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'view'  => 'card',
				'title' => esc_html__( 'باشگاه مشتریان', 'neomorph-core' ),
				'count' => 5,
			),
			$atts,
			'neomorph_loyalty'
		);
		wp_enqueue_style( 'nmc-panel', NEOMORPH_CORE_URL . 'assets/css/panel.css', array(), NEOMORPH_CORE_VERSION );
		wp_enqueue_script( 'nmc-panel', NEOMORPH_CORE_URL . 'assets/js/panel.js', array( 'jquery' ), NEOMORPH_CORE_VERSION, true );

		ob_start();
		echo '<section class="neo-widget neo-surface neo-loyalty neo-loyalty--' . esc_attr( $atts['view'] ) . '">';
		echo '<h2 class="neo-section__title">' . esc_html( $atts['title'] ) . '</h2>';

		switch ( $atts['view'] ) {
			case 'tiers':
				echo '<div class="neo-grid neo-grid--4">';
				$current = is_user_logged_in() ? self::tier_of( get_current_user_id() ) : null;
				foreach ( self::tiers() as $slug => $tier ) {
					$is_current = $current && $current['slug'] === $slug;
					printf(
						'<div class="neo-card%s"><h3>%s</h3><p class="neo-muted">از %s امتیاز</p><p class="neo-badge">%s %s</p></div>',
						$is_current ? ' is-active' : '',
						esc_html( $tier['label'] ),
						esc_html( number_format_i18n( (int) $tier['min'] ) ),
						esc_html__( 'پاداش:', 'neomorph-core' ),
						esc_html( $tier['bonus'] )
					);
				}
				echo '</div>';
				break;

			case 'rewards':
				if ( ! is_user_logged_in() ) {
					echo '<p class="neo-empty">' . esc_html__( 'برای تعویض امتیاز وارد شوید.', 'neomorph-core' ) . '</p>';
					break;
				}
				wp_localize_script(
					'nmc-panel',
					'nmcPanel',
					array(
						'ajaxUrl' => admin_url( 'admin-ajax.php' ),
						'nonce'   => wp_create_nonce( 'nmc_panel' ),
					)
				);
				$balance = self::balance( get_current_user_id() );
				printf( '<p>' . esc_html__( 'امتیاز شما:', 'neomorph-core' ) . ' <strong class="neo-accent">%s</strong></p>', esc_html( number_format_i18n( $balance ) ) );
				echo '<div class="neo-grid neo-grid--3">';
				foreach ( self::rewards() as $i => $reward ) {
					printf(
						'<div class="neo-card"><h3>%s</h3><p class="neo-badge">%s %s</p><button class="neo-btn neo-btn--primary" data-redeem="%d">%s</button></div>',
						esc_html( $reward['title'] ),
						esc_html( number_format_i18n( (int) $reward['cost'] ) ),
						esc_html__( 'امتیاز', 'neomorph-core' ),
						(int) $i,
						esc_html__( 'تعویض', 'neomorph-core' )
					);
				}
				echo '</div>';
				break;

			case 'leaderboard':
				global $wpdb;
				$table = $wpdb->prefix . 'nmc_points';
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$top = $wpdb->get_results( $wpdb->prepare( "SELECT user_id, SUM(points) AS total FROM {$table} GROUP BY user_id ORDER BY total DESC LIMIT %d", (int) $atts['count'] ), ARRAY_A );
				echo '<ol class="neo-leaderboard">';
				if ( $top ) {
					foreach ( $top as $i => $row ) {
						$user = get_userdata( $row['user_id'] );
						printf(
							'<li class="neo-card"><span class="neo-steps__num">%d</span> %s — %s</li>',
							(int) ( $i + 1 ),
							$user ? esc_html( $user->display_name ) : esc_html__( 'کاربر', 'neomorph-core' ),
							esc_html( number_format_i18n( $row['total'] ) )
						);
					}
				}
				echo '</ol>';
				break;

			case 'intro':
				echo '<p>' . esc_html__( 'با هر خرید امتیاز بگیرید، در سطح عضویت ارتقا پیدا کنید و امتیازها را به کد تخفیف و خدمات ویژه تبدیل کنید.', 'neomorph-core' ) . '</p>';
				echo '<div class="neo-grid neo-grid--3">';
				foreach ( self::tiers() as $tier ) {
					printf( '<div class="neo-card"><h3>%s</h3><p>از %s امتیاز — پاداش خرید %s</p></div>', esc_html( $tier['label'] ), esc_html( number_format_i18n( $tier['min'] ) ), esc_html( $tier['bonus'] ) );
				}
				echo '</div>';
				break;

			default: // card.
				if ( ! is_user_logged_in() ) {
					echo '<p class="neo-empty">' . esc_html__( 'برای مشاهده امتیازها وارد شوید.', 'neomorph-core' ) . '</p>';
					break;
				}
				$user_id = get_current_user_id();
				$balance = self::balance( $user_id );
				$tier    = self::tier_of( $user_id );
				$tiers   = self::tiers();
				$next    = null;
				foreach ( $tiers as $t ) {
					if ( (int) $t['min'] > $balance ) {
						$next = $t;
						break;
					}
				}
				$progress = $next ? min( 100, round( $balance / max( 1, (int) $next['min'] ) * 100 ) ) : 100;
				printf( '<div class="neo-inset nmc-loyalty-card">' );
				printf( '<p class="neo-badge neo-badge--success">%s</p>', esc_html( $tier ? $tier['label'] : '' ) );
				printf( '<p class="neo-stat__number">%s</p><p class="neo-muted">%s</p>', esc_html( number_format_i18n( $balance ) ), esc_html__( 'امتیاز شما', 'neomorph-core' ) );
				printf( '<div class="neo-progress"><div class="neo-progress__bar" style="width:%d%%"></div></div>', (int) $progress );
				if ( $next ) {
					printf( '<p class="neo-muted">%s %s</p>', esc_html__( 'تا سطح بعدی:', 'neomorph-core' ), esc_html( number_format_i18n( (int) $next['min'] - $balance ) ) );
				}
				echo '</div>';
				echo '<h3>' . esc_html__( 'آخرین تراکنش‌ها', 'neomorph-core' ) . '</h3><table class="neo-table"><tbody>';
				foreach ( self::history( $user_id, 6 ) as $row ) {
					printf(
						'<tr><td>%s</td><td>%s</td><td>%s</td></tr>',
						esc_html( $row['created_at'] ),
						esc_html( $row['reason'] ),
						esc_html( ( $row['points'] > 0 ? '+' : '' ) . number_format_i18n( $row['points'] ) )
					);
				}
				echo '</tbody></table>';
		}

		echo '</section>';
		return ob_get_clean();
	}

	/**
	 * Admin screen: rewards + tiers editor (simple forms).
	 */
	public static function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( isset( $_POST['nmc_rewards_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['nmc_rewards_nonce'] ), 'nmc_save_rewards' ) ) {
			$titles = isset( $_POST['reward_title'] ) ? (array) wp_unslash( $_POST['reward_title'] ) : array();
			$costs  = isset( $_POST['reward_cost'] ) ? (array) wp_unslash( $_POST['reward_cost'] ) : array();
			$rewards = array();
			foreach ( $titles as $i => $title ) {
				if ( '' === trim( $title ) ) {
					continue;
				}
				$rewards[] = array( 'title' => sanitize_text_field( $title ), 'cost' => (int) ( $costs[ $i ] ?? 0 ) );
			}
			update_option( 'neomorph_core_rewards', $rewards );
			echo '<div class="notice notice-success is-dismissible"><p>ذخیره شد.</p></div>';
		}
		$rewards = self::rewards();
		?>
		<div class="wrap nmc-admin">
			<h1>🎁 <?php esc_html_e( 'باشگاه مشتریان', 'neomorph-core' ); ?></h1>
			<form method="post">
				<?php wp_nonce_field( 'nmc_save_rewards', 'nmc_rewards_nonce' ); ?>
				<table class="widefat">
					<thead><tr><th><?php esc_html_e( 'جایزه', 'neomorph-core' ); ?></th><th><?php esc_html_e( 'هزینه امتیاز', 'neomorph-core' ); ?></th></tr></thead>
					<tbody>
						<?php
						$rows = $rewards ? $rewards : array( array( 'title' => '', 'cost' => 0 ) );
						foreach ( $rows as $reward ) :
							?>
							<tr>
								<td><input type="text" name="reward_title[]" value="<?php echo esc_attr( $reward['title'] ); ?>" class="regular-text"></td>
								<td><input type="number" name="reward_cost[]" value="<?php echo esc_attr( $reward['cost'] ); ?>" class="small-text"></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<p class="description"><?php esc_html_e( 'برای افزودن ردیف جدید، فرم را با یک ردیف خالی ذخیره کنید یا ردیف‌ها را کپی کنید. سطوح عضویت از طریق فیلتر nmc_loyalty_tiers قابل تغییر است.', 'neomorph-core' ); ?></p>
				<?php submit_button( esc_html__( 'ذخیره جوایز', 'neomorph-core' ) ); ?>
			</form>
		</div>
		<?php
	}
}

LoyaltyEngine::init();
