<?php
/**
 * Invoice admin UI + public shortcode [neomorph_invoice] (view + pay + print).
 *
 * @package NeomorphCore\Finance
 */

namespace NeomorphCore\Finance;

use NeomorphCore\Finance\Gateways\Manager;
use NeomorphCore\Integrations\Notifier;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class InvoiceController
 */
final class InvoiceController {

	public static function init() {
		add_shortcode( 'neomorph_invoice', array( __CLASS__, 'shortcode' ) );
		add_action( 'admin_post_nmc_create_invoice', array( __CLASS__, 'handle_create' ) );
		add_action( 'admin_post_nmc_send_invoice', array( __CLASS__, 'handle_send' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box' ) );
		add_action( 'save_post_nmc_invoice', array( __CLASS__, 'save_meta_box' ) );
		add_action( 'init', array( __CLASS__, 'maybe_handle_gateway_return' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	/**
	 * Front assets for invoice page.
	 */
	public static function assets() {
		if ( ! is_page() ) {
			return;
		}
		wp_enqueue_style( 'nmc-panel', NEOMORPH_CORE_URL . 'assets/css/panel.css', array(), NEOMORPH_CORE_VERSION );
	}

	/**
	 * Public invoice page via ?nmc-invoice=token or [neomorph_invoice id=""].
	 */
	public static function shortcode( $atts ) {
		$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'neomorph_invoice' );
		wp_enqueue_style( 'nmc-panel', NEOMORPH_CORE_URL . 'assets/css/panel.css', array(), NEOMORPH_CORE_VERSION );

		$invoice_id = (int) $atts['id'];
		if ( ! $invoice_id && isset( $_GET['nmc-invoice'] ) ) {
			$invoice_id = Invoice::id_by_token( sanitize_text_field( wp_unslash( $_GET['nmc-invoice'] ) ) );
		}
		if ( ! $invoice_id ) {
			return '<div class="neo-surface neo-empty"><p>' . esc_html__( 'فاکتوری یافت نشد.', 'neomorph-core' ) . '</p></div>';
		}

		$status   = \nmc_get_meta( $invoice_id, 'status' );
		$items    = (array) \nmc_get_meta( $invoice_id, 'items', array() );
		$subtotal = (float) \nmc_get_meta( $invoice_id, 'subtotal', 0 );
		$discount = (float) \nmc_get_meta( $invoice_id, 'discount', 0 );
		$tax      = (float) \nmc_get_meta( $invoice_id, 'tax', 0 );
		$total    = (float) \nmc_get_meta( $invoice_id, 'total', 0 );
		$number   = \nmc_get_meta( $invoice_id, 'number', get_the_title( $invoice_id ) );
		$user_id  = (int) \nmc_get_meta( $invoice_id, 'user_id' );

		ob_start();
		?>
		<div class="neo-surface neo-invoice" id="nmc-invoice">
			<div class="neo-invoice__head">
				<div>
					<h1 class="entry-title"><?php echo esc_html( sprintf( 'فاکتور %s', $number ) ); ?></h1>
					<p class="neo-meta">
						<span><?php echo esc_html( \nmc_get_meta( $invoice_id, 'created_at' ) ); ?></span>
						<?php
						$badge_map = array(
							'paid'      => array( 'success', 'پرداخت شده' ),
							'unpaid'    => array( 'info', 'در انتظار پرداخت' ),
							'cancelled' => array( 'danger', 'لغو شده' ),
							'expired'   => array( 'danger', 'منقضی' ),
						);
						$badge = isset( $badge_map[ $status ] ) ? $badge_map[ $status ] : array( 'info', $status );
						printf( '<span class="neo-badge neo-badge--%s">%s</span>', esc_attr( $badge[0] ), esc_html( $badge[1] ) );
						?>
					</p>
				</div>
				<div class="neo-invoice__logo"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></div>
			</div>

			<table class="neo-table">
				<thead><tr><th><?php esc_html_e( 'شرح', 'neomorph-core' ); ?></th><th><?php esc_html_e( 'تعداد', 'neomorph-core' ); ?></th><th><?php esc_html_e( 'قیمت واحد', 'neomorph-core' ); ?></th><th><?php esc_html_e( 'جمع', 'neomorph-core' ); ?></th></tr></thead>
				<tbody>
					<?php foreach ( $items as $item ) : ?>
						<tr>
							<td><?php echo esc_html( $item['title'] ); ?></td>
							<td><?php echo esc_html( (string) $item['qty'] ); ?></td>
							<td><?php echo esc_html( \nmc_price( $item['price'] ) ); ?></td>
							<td><?php echo esc_html( \nmc_price( $item['total'] ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<div class="neo-invoice__totals">
				<p><?php esc_html_e( 'جمع کل:', 'neomorph-core' ); ?> <?php echo esc_html( \nmc_price( $subtotal ) ); ?></p>
				<?php if ( $discount ) : ?><p><?php esc_html_e( 'تخفیف:', 'neomorph-core' ); ?> -<?php echo esc_html( \nmc_price( $discount ) ); ?></p><?php endif; ?>
				<?php if ( $tax ) : ?><p><?php esc_html_e( 'مالیات:', 'neomorph-core' ); ?> <?php echo esc_html( \nmc_price( $tax ) ); ?></p><?php endif; ?>
				<p><strong><?php esc_html_e( 'مبلغ قابل پرداخت:', 'neomorph-core' ); ?> <span class="neo-accent"><?php echo esc_html( \nmc_price( $total ) ); ?></span></strong></p>
			</div>

			<div class="neo-invoice__actions">
				<?php if ( 'unpaid' === $status ) : ?>
					<?php echo Manager::render_pay_button( $invoice_id ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<button type="button" class="neo-btn" onclick="navigator.clipboard.writeText(location.href);this.textContent='<?php echo esc_js( 'لینک کپی شد' ); ?>'">
						<?php esc_html_e( 'کپی لینک پرداخت', 'neomorph-core' ); ?>
					</button>
				<?php endif; ?>
				<button type="button" class="neo-btn" onclick="window.print()"><?php esc_html_e( 'چاپ / PDF', 'neomorph-core' ); ?></button>
				<?php
				// Send by SMS link (customer can forward).
				if ( 'unpaid' === $status && current_user_can( 'manage_nmc_invoices' ) ) {
					printf(
						'<a class="neo-btn neo-btn--primary" href="%s">%s</a>',
						esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=nmc_send_invoice&invoice=' . $invoice_id ), 'nmc_send_' . $invoice_id ) ),
						esc_html__( 'ارسال لینک پیامکی', 'neomorph-core' )
					);
				}
				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Gateway return/callback (success/fail query args).
	 */
	public static function maybe_handle_gateway_return() {
		if ( isset( $_GET['nmc-gw'] ) && isset( $_GET['invoice'] ) ) {
			$invoice_id = (int) $_GET['invoice'];
			$ok         = Manager::verify_return( $invoice_id );
			$status     = $ok ? 'paid' : 'failed';
			wp_safe_redirect( add_query_arg( 'pay', $status, Invoice::payment_url( $invoice_id ) ) );
			exit;
		}
	}

	/**
	 * Admin: create invoice from form (admin-post).
	 */
	public static function handle_create() {
		if ( ! current_user_can( 'manage_nmc_invoices' ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز.', 'neomorph-core' ) );
		}
		check_admin_referer( 'nmc_create_invoice' );

		$user_id = isset( $_POST['user_id'] ) ? (int) $_POST['user_id'] : 0;
		$titles  = isset( $_POST['item_title'] ) ? (array) wp_unslash( $_POST['item_title'] ) : array();
		$qtys    = isset( $_POST['item_qty'] ) ? (array) wp_unslash( $_POST['item_qty'] ) : array();
		$prices  = isset( $_POST['item_price'] ) ? (array) wp_unslash( $_POST['item_price'] ) : array();

		$items = array();
		foreach ( $titles as $i => $title ) {
			if ( '' === trim( $title ) ) {
				continue;
			}
			$items[] = array(
				'title' => sanitize_text_field( $title ),
				'qty'   => isset( $qtys[ $i ] ) ? max( 1, (int) $qtys[ $i ] ) : 1,
				'price' => isset( $prices[ $i ] ) ? (float) $prices[ $i ] : 0,
			);
		}
		if ( ! $items ) {
			wp_die( esc_html__( 'حداقل یک قلم فاکتور لازم است.', 'neomorph-core' ) );
		}

		$invoice_id = Invoice::create(
			$user_id,
			$items,
			array(
				'discount'    => isset( $_POST['discount'] ) ? (float) $_POST['discount'] : 0,
				'tax_percent' => isset( $_POST['tax_percent'] ) ? (float) $_POST['tax_percent'] : 0,
				'description' => isset( $_POST['description'] ) ? wp_kses_post( wp_unslash( $_POST['description'] ) ) : '',
				'due_date'    => isset( $_POST['due_date'] ) ? sanitize_text_field( wp_unslash( $_POST['due_date'] ) ) : '',
			)
		);

		if ( ! empty( $_POST['send_now'] ) && $invoice_id ) {
			self::send_invoice_to_customer( $invoice_id );
		}

		wp_safe_redirect( admin_url( 'post.php?post=' . $invoice_id . '&action=edit' ) );
		exit;
	}

	/**
	 * Admin: send invoice link by SMS + admin channels.
	 */
	public static function handle_send() {
		if ( ! current_user_can( 'manage_nmc_invoices' ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز.', 'neomorph-core' ) );
		}
		$invoice_id = isset( $_GET['invoice'] ) ? (int) $_GET['invoice'] : 0;
		check_admin_referer( 'nmc_send_' . $invoice_id );
		self::send_invoice_to_customer( $invoice_id );
		wp_safe_redirect( add_query_arg( 'nmc-sent', 1, Invoice::payment_url( $invoice_id ) ) );
		exit;
	}

	/**
	 * Send invoice + payment link to customer over SMS and Bale/Telegram copy to admin.
	 */
	public static function send_invoice_to_customer( $invoice_id ) {
		$user_id = (int) \nmc_get_meta( $invoice_id, 'user_id' );
		$phone   = \nmc_user_phone( $user_id );
		$url     = Invoice::payment_url( $invoice_id );
		$message = sprintf(
			/* translators: 1: invoice number 2: amount 3: url */
			esc_html__( 'فاکتور %1$s به مبلغ %2$s صادر شد. لینک پرداخت: %3$s', 'neomorph-core' ),
			\nmc_get_meta( $invoice_id, 'number' ),
			\nmc_price( \nmc_get_meta( $invoice_id, 'total', 0 ) ),
			$url
		);

		if ( $phone ) {
			Notifier::send( 'sms', $message, $phone );
		}
		// Optional copy to customer Bale/Telegram if they linked chat id.
		$bale_chat = get_user_meta( $user_id, 'nmc_bale_chat', true );
		if ( $bale_chat ) {
			Notifier::send( 'bale', $message, $bale_chat );
		}
		$tg_chat = get_user_meta( $user_id, 'nmc_telegram_chat', true );
		if ( $tg_chat ) {
			Notifier::send( 'telegram', $message, $tg_chat );
		}

		\nmc_do_event( 'invoice_sent', array( 'post_id' => $invoice_id, 'user_id' => $user_id ) );
	}

	/**
	 * Meta box: quick status + items summary on invoice edit screen.
	 */
	public static function meta_box() {
		add_meta_box( 'nmc_invoice_details', esc_html__( 'جزئیات فاکتور', 'neomorph-core' ), array( __CLASS__, 'render_meta_box' ), 'nmc_invoice', 'side' );
	}

	/**
	 * Render meta box.
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'nmc_invoice_meta', 'nmc_invoice_meta_nonce' );
		$status = \nmc_get_meta( $post->ID, 'status', 'unpaid' );
		?>
		<label>
			<?php esc_html_e( 'وضعیت', 'neomorph-core' ); ?>
			<select name="nmc_status">
				<?php
				foreach ( array( 'draft' => 'پیش‌نویس', 'unpaid' => 'در انتظار پرداخت', 'paid' => 'پرداخت شده', 'cancelled' => 'لغو شده', 'expired' => 'منقضی' ) as $val => $label ) {
					printf( '<option value="%s" %s>%s</option>', esc_attr( $val ), selected( $status, $val, false ), esc_html( $label ) );
				}
				?>
			</select>
		</label>
		<p>
			<strong><?php esc_html_e( 'لینک پرداخت:', 'neomorph-core' ); ?></strong><br>
			<input type="text" readonly value="<?php echo esc_attr( Invoice::payment_url( $post->ID ) ); ?>" onfocus="this.select()" style="width:100%">
		</p>
		<?php
		if ( 'unpaid' === $status ) {
			printf(
				'<a class="button button-primary" href="%s">%s</a>',
				esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=nmc_send_invoice&invoice=' . $post->ID ), 'nmc_send_' . $post->ID ) ),
				esc_html__( 'ارسال به مشتری', 'neomorph-core' )
			);
		}
	}

	/**
	 * Save meta box.
	 */
	public static function save_meta_box( $post_id ) {
		if ( ! isset( $_POST['nmc_invoice_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nmc_invoice_meta_nonce'] ), 'nmc_invoice_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( isset( $_POST['nmc_status'] ) ) {
			$old = \nmc_get_meta( $post_id, 'status' );
			$new = sanitize_key( $_POST['nmc_status'] );
			\nmc_update_meta( $post_id, 'status', $new );
			if ( 'paid' === $new && 'paid' !== $old ) {
				Invoice::mark_paid( $post_id, 'manual', '' );
			}
		}
	}
}

InvoiceController::init();
