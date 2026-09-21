<?php
/**
 * Support tickets (تیکت): CPT nmc_ticket with threaded messages in meta.
 *
 * @package NeomorphCore\Tickets
 */

namespace NeomorphCore\Tickets;

use NeomorphCore\Integrations\Notifier;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TicketController
 */
final class TicketController {

	public static function init() {
		add_action( 'admin_post_nmc_ticket_create', array( __CLASS__, 'handle_create' ) );
		add_action( 'admin_post_nopriv_nmc_ticket_create', array( __CLASS__, 'handle_create' ) );
		add_action( 'wp_ajax_nmc_ticket_reply', array( __CLASS__, 'ajax_reply' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box' ) );
		add_action( 'save_post_nmc_ticket', array( __CLASS__, 'save_meta' ) );
	}

	/**
	 * Create ticket (from panel form).
	 */
	public static function handle_create() {
		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'ابتدا وارد شوید.', 'neomorph-core' ) );
		}
		check_admin_referer( 'nmc_ticket_create' );

		$user_id = get_current_user_id();
		$title   = isset( $_POST['ticket_title'] ) ? sanitize_text_field( wp_unslash( $_POST['ticket_title'] ) ) : '';
		$message = isset( $_POST['ticket_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ticket_message'] ) ) : '';
		$dept    = isset( $_POST['ticket_dept'] ) ? (int) $_POST['ticket_dept'] : 0;
		$priority = isset( $_POST['ticket_priority'] ) ? sanitize_key( $_POST['ticket_priority'] ) : 'normal';

		if ( ! $title || ! $message ) {
			wp_die( esc_html__( 'عنوان و پیام الزامی است.', 'neomorph-core' ) );
		}

		$ticket_id = wp_insert_post(
			array(
				'post_type'   => 'nmc_ticket',
				'post_status' => 'publish',
				'post_title'  => $title,
				'post_author' => $user_id,
			)
		);
		if ( is_wp_error( $ticket_id ) || ! $ticket_id ) {
			wp_die( esc_html__( 'خطا در ثبت تیکت.', 'neomorph-core' ) );
		}

		$number = 'TCK-' . ( 10000 + $ticket_id );
		\nmc_update_meta( $ticket_id, 'number', $number );
		\nmc_update_meta( $ticket_id, 'user_id', $user_id );
		\nmc_update_meta( $ticket_id, 'status', 'open' );
		\nmc_update_meta( $ticket_id, 'priority', $priority );
		\nmc_update_meta( $ticket_id, 'messages', array( self::message_row( 'user', $message, $user_id ) ) );
		if ( $dept ) {
			wp_set_object_terms( $ticket_id, array( $dept ), 'ticket_dept' );
		}

		Notifier::send( 'admin', sprintf( "🎫 تیکت جدید %s\n%s\nاز: %s", $number, $title, wp_get_current_user()->display_name ) );
		\nmc_do_event( 'ticket_created', array( 'post_id' => $ticket_id, 'user_id' => $user_id ) );

		$panel = (int) nmc_setting( 'panel_page', 0 );
		wp_safe_redirect( add_query_arg( array( 'neo-tab' => 'tickets', 'ticket' => $ticket_id, 'created' => 1 ), $panel ? get_permalink( $panel ) : home_url( '/' ) ) );
		exit;
	}

	/**
	 * AJAX reply (customer side or admin side).
	 */
	public static function ajax_reply() {
		check_ajax_referer( 'nmc_panel', 'nonce' );
		$ticket_id = isset( $_POST['ticket_id'] ) ? (int) $_POST['ticket_id'] : 0;
		$message   = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
		$user_id   = get_current_user_id();
		if ( ! $user_id || ! $ticket_id || ! $message ) {
			wp_send_json_error( esc_html__( 'داده ناقص است.', 'neomorph-core' ) );
		}
		$owner = (int) \nmc_get_meta( $ticket_id, 'user_id' );
		if ( $owner !== $user_id && ! current_user_can( 'manage_nmc_tickets' ) ) {
			wp_send_json_error( esc_html__( 'دسترسی غیرمجاز.', 'neomorph-core' ) );
		}

		$role = ( $owner === $user_id ) ? 'user' : 'staff';
		self::append_message( $ticket_id, $role, $message, $user_id );
		if ( 'staff' === $role ) {
			\nmc_update_meta( $ticket_id, 'status', 'answered' );
			Notifier::send_to_user( $owner, sprintf( '📩 پاسخ تیکت %s ثبت شد.', \nmc_get_meta( $ticket_id, 'number' ) ) );
		} else {
			\nmc_update_meta( $ticket_id, 'status', 'open' );
			Notifier::send( 'admin', sprintf( '💬 پاسخ مشتری روی تیکت %s', \nmc_get_meta( $ticket_id, 'number' ) ) );
		}

		wp_send_json_success( array( 'message' => esc_html__( 'پیام ثبت شد.', 'neomorph-core' ) ) );
	}

	/**
	 * Build one message row.
	 */
	public static function message_row( $role, $text, $author ) {
		return array(
			'role'   => $role,
			'text'   => $text,
			'author' => (int) $author,
			'time'   => current_time( 'mysql' ),
		);
	}

	/**
	 * Append message to thread.
	 */
	public static function append_message( $ticket_id, $role, $text, $author ) {
		$messages   = (array) \nmc_get_meta( $ticket_id, 'messages', array() );
		$messages[] = self::message_row( $role, $text, $author );
		\nmc_update_meta( $ticket_id, 'messages', $messages );
	}

	/**
	 * Admin meta box: status + thread.
	 */
	public static function meta_box() {
		add_meta_box( 'nmc_ticket_box', esc_html__( 'گفتگوی تیکت', 'neomorph-core' ), array( __CLASS__, 'render_meta_box' ), 'nmc_ticket', 'normal' );
	}

	/**
	 * Render thread + quick reply + status.
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'nmc_ticket_meta', 'nmc_ticket_meta_nonce' );
		$messages = (array) \nmc_get_meta( $post->ID, 'messages', array() );
		$status   = \nmc_get_meta( $post->ID, 'status', 'open' );
		?>
		<div class="nmc-thread">
			<?php foreach ( $messages as $msg ) : ?>
				<div class="nmc-msg nmc-msg--<?php echo esc_attr( $msg['role'] ); ?>">
					<strong><?php echo esc_html( 'user' === $msg['role'] ? esc_html__( 'مشتری', 'neomorph-core' ) : esc_html__( 'پشتیبانی', 'neomorph-core' ) ); ?></strong>
					<span class="nmc-msg__time"><?php echo esc_html( $msg['time'] ); ?></span>
					<p><?php echo nl2br( esc_html( $msg['text'] ) ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<p>
			<label><?php esc_html_e( 'وضعیت', 'neomorph-core' ); ?>
				<select name="nmc_ticket_status">
					<?php
					foreach ( array( 'open' => 'باز', 'answered' => 'پاسخ داده شد', 'closed' => 'بسته شد' ) as $val => $label ) {
						printf( '<option value="%s" %s>%s</option>', esc_attr( $val ), selected( $status, $val, false ), esc_html( $label ) );
					}
					?>
				</select>
			</label>
		</p>
		<p>
			<textarea name="nmc_reply" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'پاسخ پشتیبانی…', 'neomorph-core' ); ?>"></textarea>
		</p>
		<?php
	}

	/**
	 * Save meta box + optional reply.
	 */
	public static function save_meta( $post_id ) {
		if ( ! isset( $_POST['nmc_ticket_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nmc_ticket_meta_nonce'] ), 'nmc_ticket_meta' ) ) {
			return;
		}
		if ( isset( $_POST['nmc_ticket_status'] ) ) {
			\nmc_update_meta( $post_id, 'status', sanitize_key( $_POST['nmc_ticket_status'] ) );
		}
		if ( ! empty( $_POST['nmc_reply'] ) ) {
			$reply = sanitize_textarea_field( wp_unslash( $_POST['nmc_reply'] ) );
			self::append_message( $post_id, 'staff', $reply, get_current_user_id() );
			$owner = (int) \nmc_get_meta( $post_id, 'user_id' );
			Notifier::send_to_user( $owner, sprintf( '📩 پاسخ تیکت %s: %s', \nmc_get_meta( $post_id, 'number' ), mb_substr( $reply, 0, 80 ) ) );
		}
	}
}

TicketController::init();
