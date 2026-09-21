<?php
/**
 * Automation engine — trigger → condition → action rules (stored in option).
 *
 * Events (triggers):
 *  user_registered_otp, contact_created, invoice_created, invoice_sent, invoice_paid,
 *  ticket_created, case_stage_changed, apply_received, points_changed, loyalty_threshold
 *
 * Actions:
 *  send_sms, send_bale, send_telegram, send_email, add_segment, add_points,
 *  create_task (case), set_case_stage
 *
 * Rule schema:
 *  [ 'id', 'event', 'conditions' => [ 'field' => 'value' ], 'actions' => [ ['type' => 'send_sms', 'channel_target' => 'user|admin', 'template' => '… {name} {amount}'] ], 'enabled' ]
 *
 * @package NeomorphCore\Automation
 */

namespace NeomorphCore\Automation;

use NeomorphCore\Integrations\Notifier;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AutomationEngine
 */
final class AutomationEngine {

	const OPTION = 'neomorph_core_automation_rules';

	public static function init() {
		add_action( 'nmc_event', array( __CLASS__, 'on_event' ), 10, 2 );
		add_action( 'admin_post_nmc_save_rules', array( __CLASS__, 'handle_save' ) );
	}

	/**
	 * All rules.
	 */
	public static function rules() {
		$rules = get_option( self::OPTION, array() );
		return is_array( $rules ) ? $rules : array();
	}

	/**
	 * Route event to matching rules.
	 */
	public static function on_event( $event, $ctx ) {
		foreach ( self::rules() as $rule ) {
			if ( empty( $rule['enabled'] ) || ( $rule['event'] ?? '' ) !== $event ) {
				continue;
			}
			if ( self::match_conditions( $rule['conditions'] ?? array(), $ctx, $event ) ) {
				foreach ( (array) ( $rule['actions'] ?? array() ) as $action ) {
					self::run_action( $action, $ctx, $event );
				}
			}
		}
	}

	/**
	 * Condition matching: simple equality/gt on context keys (amount, stage, …).
	 */
	private static function match_conditions( $conditions, $ctx, $event ) {
		foreach ( (array) $conditions as $key => $expected ) {
			if ( '' === $expected || null === $expected ) {
				continue;
			}
			$actual = isset( $ctx[ $key ] ) ? $ctx[ $key ] : null;
			if ( is_numeric( $expected ) && is_numeric( $actual ) ) {
				// Treat ">=N" style if prefixed.
				if ( is_string( $expected ) && preg_match( '/^(>=|<=|>|<|=)(\d+)$/', $expected, $m ) ) {
					$n = (float) $m[2];
					$a = (float) $actual;
					$ops = array( '>=' => $a >= $n, '<=' => $a <= $n, '>' => $a > $n, '<' => $a < $n, '=' => $a == $n );
					if ( ! $ops[ $m[1] ] ) {
						return false;
					}
					continue;
				}
				if ( (float) $expected != (float) $actual ) {
					return false;
				}
				continue;
			}
			if ( (string) $expected !== (string) $actual ) {
				return false;
			}
		}
		// First-run rules for follow-ups pass on their own event only.
		return true;
	}

	/**
	 * Execute one action.
	 */
	private static function run_action( $action, $ctx, $event ) {
		$type   = isset( $action['type'] ) ? sanitize_key( $action['type'] ) : '';
		$tpl    = isset( $action['template'] ) ? (string) $action['template'] : '';
		$user_id = isset( $ctx['user_id'] ) ? (int) $ctx['user_id'] : 0;
		$message = self::render_template( $tpl, $ctx, $event );

		switch ( $type ) {
			case 'send_sms':
				$phone = isset( $action['target'] ) && 'admin' === $action['target'] ? '' : \nmc_user_phone( $user_id );
				if ( isset( $action['target'] ) && 'admin' === $action['target'] ) {
					Notifier::send( 'admin', $message );
				} elseif ( $phone ) {
					Notifier::send( 'sms', $message, $phone );
				}
				break;

			case 'send_bale':
				if ( isset( $action['target'] ) && 'admin' === $action['target'] ) {
					Notifier::send( 'bale', $message );
				} else {
					$chat = get_user_meta( $user_id, 'nmc_bale_chat', true );
					if ( $chat ) {
						Notifier::send( 'bale', $message, $chat );
					}
				}
				break;

			case 'send_telegram':
				if ( isset( $action['target'] ) && 'admin' === $action['target'] ) {
					Notifier::send( 'telegram', $message );
				} else {
					$chat = get_user_meta( $user_id, 'nmc_telegram_chat', true );
					if ( $chat ) {
						Notifier::send( 'telegram', $message, $chat );
					}
				}
				break;

			case 'send_email':
				$user = get_userdata( $user_id );
				if ( $user && ! empty( $user->user_email ) && ! get_user_meta( $user_id, 'nmc_placeholder_email', true ) ) {
					Notifier::send( 'email', $message, $user->user_email );
				}
				break;

			case 'add_segment':
				$term_id = isset( $action['term_id'] ) ? (int) $action['term_id'] : 0;
				if ( $user_id && $term_id ) {
					$segments   = (array) get_user_meta( $user_id, 'nmc_segments', true );
					$segments[] = $term_id;
					update_user_meta( $user_id, 'nmc_segments', array_unique( array_filter( array_map( 'intval', $segments ) ) ) );
				}
				break;

			case 'add_points':
				if ( $user_id && ! empty( $action['points'] ) ) {
					\NeomorphCore\Loyalty\LoyaltyEngine::add_points( $user_id, (int) $action['points'], 'earn', 'automation_' . $event );
				}
				break;

			case 'create_task':
				// Create a follow-up case for the CRM manager.
				$case_id = wp_insert_post(
					array(
						'post_type'   => 'nmc_case',
						'post_status' => 'publish',
						'post_title'  => mb_substr( $message, 0, 80 ),
					)
				);
				if ( $case_id ) {
					\nmc_update_meta( $case_id, 'stage', 'new' );
					\nmc_update_meta( $case_id, 'user_id', $user_id );
					\nmc_update_meta( $case_id, 'next_followup', gmdate( 'Y-m-d', time() + 2 * DAY_IN_SECONDS ) );
				}
				break;
		}

		do_action( 'nmc_automation_action_done', $type, $action, $ctx );
	}

	/**
	 * Template tokens: {name} {phone} {amount} {invoice} {title} {balance} {site}.
	 */
	public static function render_template( $template, $ctx, $event = '' ) {
		$user = isset( $ctx['user_id'] ) ? get_userdata( (int) $ctx['user_id'] ) : null;
		$map  = array(
			'{name}'    => $user ? $user->display_name : '',
			'{phone}'   => isset( $ctx['user_id'] ) ? \nmc_user_phone( (int) $ctx['user_id'] ) : '',
			'{amount}'  => isset( $ctx['amount'] ) ? \nmc_price( $ctx['amount'] ) : '',
			'{invoice}' => isset( $ctx['post_id'] ) ? \nmc_get_meta( (int) $ctx['post_id'], 'number', '' ) : '',
			'{title}'   => isset( $ctx['post_id'] ) ? get_the_title( (int) $ctx['post_id'] ) : '',
			'{balance}' => isset( $ctx['balance'] ) ? number_format_i18n( (int) $ctx['balance'] ) : '',
			'{site}'    => get_bloginfo( 'name' ),
			'{event}'   => $event,
		);
		return str_replace( array_keys( $map ), array_values( $map ), $template );
	}

	/* ── Admin rules UI ──────────────────────────────────────────── */

	/**
	 * Save rules from admin form (repeatable rows).
	 */
	public static function handle_save() {
		if ( ! current_user_can( 'manage_nmc_crm' ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز.', 'neomorph-core' ) );
		}
		check_admin_referer( 'nmc_save_rules' );

		$events   = isset( $_POST['rule_event'] ) ? (array) wp_unslash( $_POST['rule_event'] ) : array();
		$types    = isset( $_POST['rule_action_type'] ) ? (array) wp_unslash( $_POST['rule_action_type'] ) : array();
		$targets  = isset( $_POST['rule_action_target'] ) ? (array) wp_unslash( $_POST['rule_action_target'] ) : array();
		$tpls     = isset( $_POST['rule_template'] ) ? (array) wp_unslash( $_POST['rule_template'] ) : array();
		$conds    = isset( $_POST['rule_condition'] ) ? (array) wp_unslash( $_POST['rule_condition'] ) : array();
		$enabled  = isset( $_POST['rule_enabled'] ) ? (array) wp_unslash( $_POST['rule_enabled'] ) : array();

		$rules = array();
		foreach ( $events as $i => $event ) {
			if ( '' === $event ) {
				continue;
			}
			$rules[] = array(
				'id'         => 'rule_' . $i . '_' . wp_generate_password( 4, false ),
				'event'      => sanitize_key( $event ),
				'conditions' => self::parse_condition( sanitize_text_field( $conds[ $i ] ?? '' ) ),
				'actions'    => array(
					array(
						'type'     => sanitize_key( $types[ $i ] ?? 'send_sms' ),
						'target'   => sanitize_key( $targets[ $i ] ?? 'user' ),
						'template' => sanitize_textarea_field( $tpls[ $i ] ?? '' ),
					),
				),
				'enabled'    => ! empty( $enabled[ $i ] ),
			);
		}
		update_option( self::OPTION, $rules );
		wp_safe_redirect( add_query_arg( array( 'page' => 'nmc-automation', 'updated' => 1 ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Parse "amount>=100" / "stage=won" text into condition map.
	 */
	private static function parse_condition( $text ) {
		$conditions = array();
		foreach ( preg_split( '/\s*,\s*/', (string) $text ) as $piece ) {
			if ( preg_match( '/^([a-z_]+)(>=|<=|>|<|=)(.+)$/i', $piece, $m ) ) {
				$conditions[ $m[1] ] = $m[2] . $m[3];
			}
		}
		return $conditions;
	}

	/**
	 * Rules admin page.
	 */
	public static function render_rules_page() {
		if ( ! current_user_can( 'manage_nmc_crm' ) ) {
			return;
		}
		$rules   = self::rules();
		$events  = self::known_events();
		$actions = self::known_actions();
		$rows    = $rules ? $rules : array( array( 'event' => '', 'actions' => array( array( 'type' => 'send_sms', 'target' => 'user', 'template' => '' ) ), 'conditions' => array(), 'enabled' => true ) );
		?>
		<div class="wrap nmc-admin">
			<h1>⚙️ <?php esc_html_e( 'قوانین اتوماسیون و CRM', 'neomorph-core' ); ?></h1>
			<p class="description">
				<?php esc_html_e( 'الگوی پیام: توکن‌های {name} {phone} {amount} {invoice} {title} {balance} {site}. شرط: مثال amount>=500000 یا stage=won (جدا شده با ویرگول).', 'neomorph-core' ); ?>
			</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'nmc_save_rules' ); ?>
				<input type="hidden" name="action" value="nmc_save_rules">
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'فعال', 'neomorph-core' ); ?></th>
							<th><?php esc_html_e( 'رویداد', 'neomorph-core' ); ?></th>
							<th><?php esc_html_e( 'شرط', 'neomorph-core' ); ?></th>
							<th><?php esc_html_e( 'اقدام', 'neomorph-core' ); ?></th>
							<th><?php esc_html_ex( 'گیرنده', 'neomorph-core' ); ?></th>
							<th><?php esc_html_e( 'متن پیام', 'neomorph-core' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $rows as $rule ) :
							$action = isset( $rule['actions'][0] ) ? $rule['actions'][0] : array( 'type' => 'send_sms', 'target' => 'user', 'template' => '' );
							$cond   = '';
							foreach ( (array) ( $rule['conditions'] ?? array() ) as $k => $v ) {
								$cond .= ( $cond ? ', ' : '' ) . $k . $v;
							}
							?>
							<tr>
								<td><input type="checkbox" name="rule_enabled[]" value="1" <?php checked( ! empty( $rule['enabled'] ) ); ?>></td>
								<td>
									<select name="rule_event[]">
										<option value="">—</option>
										<?php foreach ( $events as $slug => $label ) : ?>
											<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $rule['event'] ?? '', $slug ); ?>><?php echo esc_html( $label ); ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td><input type="text" name="rule_condition[]" value="<?php echo esc_attr( $cond ); ?>" placeholder="amount>=500000" class="regular-text"></td>
								<td>
									<select name="rule_action_type[]">
										<?php foreach ( $actions as $slug => $label ) : ?>
											<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $action['type'], $slug ); ?>><?php echo esc_html( $label ); ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<select name="rule_action_target[]">
										<option value="user" <?php selected( $action['target'] ?? 'user', 'user' ); ?>><?php esc_html_e( 'مشتری', 'neomorph-core' ); ?></option>
										<option value="admin" <?php selected( $action['target'] ?? '', 'admin' ); ?>><?php esc_html_e( 'ادمین', 'neomorph-core' ); ?></option>
									</select>
								</td>
								<td><textarea name="rule_template[]" rows="2" class="large-text"><?php echo esc_textarea( $action['template'] ); ?></textarea></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<p class="description"><?php esc_html_e( 'برای ردیف جدید، صفحه را دوباره ذخیره کنید (یک ردیف خالی اضافه می‌شود).', 'neomorph-core' ); ?></p>
				<?php submit_button( esc_html__( 'ذخیره قوانین', 'neomorph-core' ) ); ?>
			</form>

			<h2><?php esc_html_e( 'پیشنهادهای آماده', 'neomorph-core' ); ?></h2>
			<ul style="list-style:disc;padding-right:20px">
				<li><code>invoice_paid → send_sms(کاربر): «خرید شما با موفقیت انجام شد. متشکریم {name}»</code></li>
				<li><code>invoice_created → send_telegram(ادمین): «فاکتور {invoice} به مبلغ {amount} صادر شد»</code></li>
				<li><code>ticket_created → send_bale(ادمین): «تیکت جدید: {title}»</code></li>
				<li><code>case_stage_changed, condition stage=won → add_points(100)</code></li>
				<li><code>apply_received → send_telegram(ادمین): «درخواست استخدام جدید {title}»</code></li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Event catalog.
	 */
	public static function known_events() {
		return array(
			'user_registered_otp'   => esc_html__( 'ثبت‌نام/ورود OTP', 'neomorph-core' ),
			'contact_created'       => esc_html__( 'ایجاد سرنخ (مشتری)', 'neomorph-core' ),
			'invoice_created'       => esc_html__( 'صدور فاکتور', 'neomorph-core' ),
			'invoice_sent'          => esc_html__( 'ارسال فاکتور به مشتری', 'neomorph-core' ),
			'invoice_paid'          => esc_html__( 'پرداخت فاکتور', 'neomorph-core' ),
			'ticket_created'        => esc_html__( 'تیکت جدید', 'neomorph-core' ),
			'case_stage_changed'    => esc_html__( 'تغییر مرحله پرونده', 'neomorph-core' ),
			'apply_received'        => esc_html__( 'دریافت رزومه', 'neomorph-core' ),
			'points_changed'        => esc_html__( 'تغییر امتیاز باشگاه', 'neomorph-core' ),
			'consult_requested'     => esc_html__( 'درخواست مشاوره', 'neomorph-core' ),
		);
	}

	/**
	 * Action catalog.
	 */
	public static function known_actions() {
		return array(
			'send_sms'       => esc_html__( 'ارسال پیامک', 'neomorph-core' ),
			'send_bale'      => esc_html__( 'ارسال به بله', 'neomorph-core' ),
			'send_telegram'  => esc_html__( 'ارسال به تلگرام', 'neomorph-core' ),
			'send_email'     => esc_html__( 'ارسال ایمیل', 'neomorph-core' ),
			'add_segment'    => esc_html__( 'تغییر دسته مشتری', 'neomorph-core' ),
			'add_points'     => esc_html__( 'افزودن امتیاز', 'neomorph-core' ),
			'create_task'    => esc_html__( 'ساخت کار پیگیری CRM', 'neomorph-core' ),
		);
	}
}

AutomationEngine::init();
