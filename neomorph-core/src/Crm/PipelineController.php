<?php
/**
 * CRM pipeline — پرونده‌ها (nmc_case) با مراحل قابل پیگیری خدمات انتخابی.
 *
 * Stages: new → contacted → proposal → won / lost (قابل فیلتر/تغییر).
 *
 * @package NeomorphCore\Crm
 */

namespace NeomorphCore\Crm;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PipelineController
 */
final class PipelineController {

	/**
	 * Default pipeline stages.
	 */
	public static function stages() {
		$stages = array(
			'new'      => esc_html__( 'سرنخ جدید', 'neomorph-core' ),
			'contacted'=> esc_html__( 'تماس گرفته شد', 'neomorph-core' ),
			'proposal' => esc_html__( 'پیشنهاد ارسال شد', 'neomorph-core' ),
			'followup' => esc_html__( 'در حال پیگیری', 'neomorph-core' ),
			'won'      => esc_html__( 'موفق', 'neomorph-core' ),
			'lost'     => esc_html__( 'از دست رفته', 'neomorph-core' ),
		);
		return apply_filters( 'nmc_crm_stages', $stages );
	}

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box' ) );
		add_action( 'save_post_nmc_case', array( __CLASS__, 'save_meta' ) );
		add_action( 'admin_post_nmc_pipeline_move', array( __CLASS__, 'handle_move' ) );
	}

	/**
	 * Meta box: stage, linked user, services, next follow-up date.
	 */
	public static function meta_box() {
		add_meta_box( 'nmc_case_box', esc_html__( 'پیگیری پرونده', 'neomorph-core' ), array( __CLASS__, 'render_meta_box' ), 'nmc_case', 'normal' );
	}

	/**
	 * Render.
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'nmc_case_meta', 'nmc_case_meta_nonce' );
		$stage  = \nmc_get_meta( $post->ID, 'stage', 'new' );
		$user   = (int) \nmc_get_meta( $post->ID, 'user_id' );
		$follow = \nmc_get_meta( $post->ID, 'next_followup' );
		$services = wp_get_object_terms( $post->ID, 'crm_service', array( 'fields' => 'ids' ) );
		$service_terms = get_terms( array( 'taxonomy' => 'crm_service', 'hide_empty' => false ) );
		?>
		<div class="nmc-pipeline">
			<p>
				<label><?php esc_html_e( 'مرحله', 'neomorph-core' ); ?>
					<select name="nmc_stage">
						<?php foreach ( self::stages() as $slug => $label ) : ?>
							<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $stage, $slug ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
			</p>
			<p>
				<label><?php esc_html_e( 'مشتری مرتبط', 'neomorph-core' ); ?>
					<select name="nmc_user_id">
						<option value="0">—</option>
						<?php
						foreach ( get_users( array( 'role__not_in' => array( 'administrator' ), 'number' => 200 ) ) as $u ) {
							printf( '<option value="%d" %s>%s (%s)</option>', (int) $u->ID, selected( $user, $u->ID, false ), esc_html( $u->display_name ), esc_html( \nmc_user_phone( $u->ID ) ) );
						}
						?>
					</select>
				</label>
			</p>
			<p>
				<label><?php esc_html_e( 'تاریخ پیگیری بعدی', 'neomorph-core' ); ?>
					<input type="date" name="nmc_next_followup" value="<?php echo esc_attr( $follow ); ?>">
				</label>
			</p>
			<h4><?php esc_html_e( 'خدمات انتخابی', 'neomorph-core' ); ?></h4>
			<?php foreach ( $service_terms as $srv ) : ?>
				<label style="display:block;margin-bottom:4px">
					<input type="checkbox" name="nmc_services[]" value="<?php echo esc_attr( $srv->term_id ); ?>" <?php checked( in_array( (int) $srv->term_id, array_map( 'intval', (array) $services ), true ) ); ?>>
					<?php echo esc_html( $srv->name ); ?>
				</label>
			<?php endforeach; ?>
			<p class="description"><?php esc_html_e( 'تغییر مرحله، رویداد case_stage_changed را فعال می‌کند تا در اتوماسیون پیامک/اعلان بفرستید.', 'neomorph-core' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Save.
	 */
	public static function save_meta( $post_id ) {
		if ( ! isset( $_POST['nmc_case_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nmc_case_meta_nonce'] ), 'nmc_case_meta' ) ) {
			return;
		}
		$old_stage = \nmc_get_meta( $post_id, 'stage', 'new' );
		if ( isset( $_POST['nmc_stage'] ) ) {
			$new_stage = sanitize_key( $_POST['nmc_stage'] );
			\nmc_update_meta( $post_id, 'stage', $new_stage );
			if ( $new_stage !== $old_stage ) {
				\nmc_do_event( 'case_stage_changed', array( 'post_id' => $post_id, 'stage' => $new_stage, 'old_stage' => $old_stage, 'user_id' => (int) \nmc_get_meta( $post_id, 'user_id' ) ) );
			}
		}
		if ( isset( $_POST['nmc_user_id'] ) ) {
			$uid = (int) $_POST['nmc_user_id'];
			\nmc_update_meta( $post_id, 'user_id', $uid );
			if ( $uid ) {
				$segments = (array) get_user_meta( $uid, 'nmc_segments', true );
				$seg_term = get_term_by( 'name', 'مشتری فعال', 'crm_segment' );
				if ( $seg_term && ! in_array( (int) $seg_term->term_id, array_map( 'intval', $segments ), true ) ) {
					$segments[] = (int) $seg_term->term_id;
					update_user_meta( $uid, 'nmc_segments', array_filter( $segments ) );
				}
			}
		}
		if ( isset( $_POST['nmc_next_followup'] ) ) {
			\nmc_update_meta( $post_id, 'next_followup', sanitize_text_field( wp_unslash( $_POST['nmc_next_followup'] ) ) );
		}
		if ( isset( $_POST['nmc_services'] ) ) {
			wp_set_object_terms( $post_id, array_map( 'intval', (array) wp_unslash( $_POST['nmc_services'] ) ), 'crm_service' );
		}
	}

	/**
	 * Kanban-lite board on the cases list screen (admin_footer hook on edit.php).
	 */
	public static function handle_move() {
		if ( ! current_user_can( 'manage_nmc_crm' ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز.', 'neomorph-core' ) );
		}
		$case_id = isset( $_GET['case'] ) ? (int) $_GET['case'] : 0;
		check_admin_referer( 'nmc_move_' . $case_id );
		$stage = isset( $_GET['stage'] ) ? sanitize_key( $_GET['stage'] ) : 'new';
		$old   = \nmc_get_meta( $case_id, 'stage', 'new' );
		\nmc_update_meta( $case_id, 'stage', $stage );
		\nmc_do_event( 'case_stage_changed', array( 'post_id' => $case_id, 'stage' => $stage, 'old_stage' => $old, 'user_id' => (int) \nmc_get_meta( $case_id, 'user_id' ) ) );
		wp_safe_redirect( admin_url( 'edit.php?post_type=nmc_case&moved=1' ) );
		exit;
	}
}

PipelineController::init();
