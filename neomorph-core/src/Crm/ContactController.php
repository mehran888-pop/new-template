<?php
/**
 * CRM contacts — WP users as customers + segments/services + admin list page.
 *
 * @package NeomorphCore\Crm
 */

namespace NeomorphCore\Crm;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ContactController
 */
final class ContactController {

	public static function init() {
		add_action( 'user_register', array( __CLASS__, 'sync_user' ) );
		add_action( 'profile_update', array( __CLASS__, 'sync_user' ) );
		add_action( 'admin_post_nmc_save_contact', array( __CLASS__, 'handle_save' ) );
	}

	/**
	 * Mirror user into CRM meta (segment, services, lifetime value, last activity).
	 */
	public static function sync_user( $user_id ) {
		$user = get_userdata( $user_id );
		if ( ! $user || in_array( 'administrator', (array) $user->roles, true ) ) {
			return;
		}
		if ( ! get_user_meta( $user_id, 'nmc_crm_added', true ) ) {
			update_user_meta( $user_id, 'nmc_crm_added', current_time( 'mysql' ) );
			// Default segment: مشتری تازه.
			$term = get_term_by( 'name', 'مشتری تازه', 'crm_segment' );
			if ( $term ) {
				$segments   = (array) get_user_meta( $user_id, 'nmc_segments', true );
				$segments[] = (int) $term->term_id;
				update_user_meta( $user_id, 'nmc_segments', array_unique( array_filter( $segments ) ) );
			}
			\nmc_do_event( 'contact_created', array( 'user_id' => $user_id ) );
		}
		update_user_meta( $user_id, 'nmc_last_activity', current_time( 'mysql' ) );
	}

	/**
	 * Lifetime value (sum of paid invoices).
	 */
	public static function lifetime_value( $user_id ) {
		$invoices = get_posts(
			array(
				'post_type'   => 'nmc_invoice',
				'post_status' => 'publish',
				'meta_key'    => '_nmc_user_id', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'  => (int) $user_id, // phpcs:ignore WordPress.DB.SlowDBQuery
				'numberposts' => -1,
				'fields'      => 'ids',
			)
		);
		$sum = 0;
		foreach ( $invoices as $inv ) {
			if ( 'paid' === \nmc_get_meta( $inv, 'status' ) ) {
				$sum += (float) \nmc_get_meta( $inv, 'total', 0 );
			}
		}
		return $sum;
	}

	/**
	 * Save contact profile fields (admin quick edit).
	 */
	public static function handle_save() {
		if ( ! current_user_can( 'manage_nmc_crm' ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز.', 'neomorph-core' ) );
		}
		check_admin_referer( 'nmc_save_contact' );
		$user_id = isset( $_POST['user_id'] ) ? (int) $_POST['user_id'] : 0;
		if ( ! $user_id ) {
			wp_die( 'user required' );
		}
		if ( isset( $_POST['segments'] ) ) {
			$segments = array_map( 'intval', (array) wp_unslash( $_POST['segments'] ) );
			update_user_meta( $user_id, 'nmc_segments', array_filter( $segments ) );
		}
		if ( isset( $_POST['services'] ) ) {
			$services = array_map( 'intval', (array) wp_unslash( $_POST['services'] ) );
			update_user_meta( $user_id, 'nmc_services', array_filter( $services ) );
		}
		if ( isset( $_POST['crm_note'] ) ) {
			update_user_meta( $user_id, 'nmc_crm_note', sanitize_textarea_field( wp_unslash( $_POST['crm_note'] ) ) );
		}
		\nmc_do_event( 'contact_updated', array( 'user_id' => $user_id ) );
		wp_safe_redirect( add_query_arg( array( 'page' => 'nmc-crm', 'user' => $user_id, 'updated' => 1 ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Admin CRM page: contacts table + filter by segment + detail side form.
	 */
	public static function render_admin_page() {
		if ( ! current_user_can( 'manage_nmc_crm' ) ) {
			return;
		}
		$segment_filter = isset( $_GET['segment'] ) ? (int) $_GET['segment'] : 0; // phpcs:ignore WordPress.Security.NonceVerification
		$detail_user    = isset( $_GET['user'] ) ? (int) $_GET['user'] : 0; // phpcs:ignore WordPress.Security.NonceVerification

		$customers = get_users(
			array(
				'role__not_in' => array( 'administrator' ),
				'number'       => 100,
				'orderby'      => 'registered',
				'order'        => 'DESC',
			)
		);
		if ( $segment_filter ) {
			$customers = array_filter(
				$customers,
				function ( $user ) use ( $segment_filter ) {
					$segments = (array) get_user_meta( $user->ID, 'nmc_segments', true );
					return in_array( $segment_filter, array_map( 'intval', $segments ), true );
				}
			);
		}

		$segments        = get_terms( array( 'taxonomy' => 'crm_segment', 'hide_empty' => false ) );
		$service_terms   = get_terms( array( 'taxonomy' => 'crm_service', 'hide_empty' => false ) );
		?>
		<div class="wrap nmc-admin">
			<h1>🗂️ <?php esc_html_e( 'CRM — مشتریان و خدمات', 'neomorph-core' ); ?></h1>

			<ul class="subsubsub">
				<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=nmc-crm' ) ); ?>" class="<?php echo $segment_filter ? '' : 'current'; ?>"><?php esc_html_e( 'همه', 'neomorph-core' ); ?></a> |</li>
				<?php foreach ( $segments as $seg ) : ?>
					<li>
						<a href="<?php echo esc_url( add_query_arg( 'segment', $seg->term_id, admin_url( 'admin.php?page=nmc-crm' ) ) ); ?>" class="<?php echo $segment_filter === (int) $seg->term_id ? 'current' : ''; ?>"><?php echo esc_html( $seg->name ); ?></a> |
					</li>
				<?php endforeach; ?>
			</ul>

			<div class="nmc-crm-layout">
				<table class="widefat striped nmc-crm-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'نام', 'neomorph-core' ); ?></th>
							<th><?php esc_html_e( 'موبایل', 'neomorph-core' ); ?></th>
							<th><?php esc_html_e( 'دسته‌بندی', 'neomorph-core' ); ?></th>
							<th><?php esc_html_e( 'خدمات انتخابی', 'neomorph-core' ); ?></th>
							<th><?php esc_html_e( 'ارزش مادام‌العمر', 'neomorph-core' ); ?></th>
							<th><?php esc_html_e( 'آخرین فعالیت', 'neomorph-core' ); ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $customers as $user ) : ?>
							<?php
							$user_segments = (array) get_user_meta( $user->ID, 'nmc_segments', true );
							$user_services = (array) get_user_meta( $user->ID, 'nmc_services', true );
							?>
							<tr>
								<td><strong><?php echo esc_html( $user->display_name ); ?></strong></td>
								<td><code><?php echo esc_html( \nmc_user_phone( $user->ID ) ?: '—' ); ?></code></td>
								<td><?php echo esc_html( self::terms_line( $user_segments ) ); ?></td>
								<td><?php echo esc_html( self::terms_line( $user_services ) ); ?></td>
								<td><?php echo esc_html( \nmc_price( self::lifetime_value( $user->ID ) ) ); ?></td>
								<td><?php echo esc_html( get_user_meta( $user->ID, 'nmc_last_activity', true ) ?: '—' ); ?></td>
								<td><a class="button button-small" href="<?php echo esc_url( add_query_arg( 'user', $user->ID, admin_url( 'admin.php?page=nmc-crm' ) ) ); ?>"><?php esc_html_e( 'مدیریت', 'neomorph-core' ); ?></a></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<?php if ( $detail_user ) : ?>
					<?php $user = get_userdata( $detail_user ); ?>
					<div class="nmc-card nmc-crm-detail">
						<h2><?php echo esc_html( $user ? $user->display_name : '' ); ?></h2>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<?php wp_nonce_field( 'nmc_save_contact' ); ?>
							<input type="hidden" name="action" value="nmc_save_contact">
							<input type="hidden" name="user_id" value="<?php echo esc_attr( $detail_user ); ?>">

							<h3><?php esc_html_e( 'دسته‌بندی مشتری', 'neomorph-core' ); ?></h3>
							<?php foreach ( $segments as $seg ) : ?>
								<label style="display:block;margin-bottom:4px">
									<input type="checkbox" name="segments[]" value="<?php echo esc_attr( $seg->term_id ); ?>" <?php checked( in_array( (int) $seg->term_id, array_map( 'intval', (array) get_user_meta( $detail_user, 'nmc_segments', true ) ), true ) ); ?>>
									<?php echo esc_html( $seg->name ); ?>
								</label>
							<?php endforeach; ?>

							<h3><?php esc_html_e( 'خدمات انتخابی', 'neomorph-core' ); ?></h3>
							<?php foreach ( $service_terms as $srv ) : ?>
								<label style="display:block;margin-bottom:4px">
									<input type="checkbox" name="services[]" value="<?php echo esc_attr( $srv->term_id ); ?>" <?php checked( in_array( (int) $srv->term_id, array_map( 'intval', (array) get_user_meta( $detail_user, 'nmc_services', true ) ), true ) ); ?>>
									<?php echo esc_html( $srv->name ); ?>
								</label>
							<?php endforeach; ?>

							<h3><?php esc_html_e( 'یادداشت CRM', 'neomorph-core' ); ?></h3>
							<textarea name="crm_note" rows="4" class="large-text"><?php echo esc_textarea( get_user_meta( $detail_user, 'nmc_crm_note', true ) ); ?></textarea>

							<p>
								<button class="button button-primary"><?php esc_html_e( 'ذخیره', 'neomorph-core' ); ?></button>
								<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=nmc-crm' ) ); ?>"><?php esc_html_e( 'بستن', 'neomorph-core' ); ?></a>
							</p>
						</form>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Term IDs → names line.
	 */
	public static function terms_line( $term_ids ) {
		$names = array();
		foreach ( array_filter( array_map( 'intval', (array) $term_ids ) ) as $tid ) {
			$term = get_term( $tid );
			if ( $term && ! is_wp_error( $term ) ) {
				$names[] = $term->name;
			}
		}
		return $names ? implode( '، ', $names ) : '—';
	}
}

ContactController::init();
