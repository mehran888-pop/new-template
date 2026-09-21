<?php
/**
 * Job positions + scoring config per job (criteria weights).
 *
 * Job meta (_nmc_*):
 *  job_location, job_salary, job_experience, job_deadline,
 *  criteria (serialized: [ [label, weight, keywords(csv)] ]) — auto scoring rules,
 *  stages (custom hiring stages)
 *
 * @package NeomorphCore\Recruitment
 */

namespace NeomorphCore\Recruitment;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class JobController
 */
final class JobController {

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box' ) );
		add_action( 'save_post_job', array( __CLASS__, 'save_meta' ) );
	}

	/**
	 * Meta line for lists (location · type · salary).
	 */
	public static function get_job_meta_line( $job_id ) {
		$parts = array();
		if ( $loc = \nmc_get_meta( $job_id, 'job_location' ) ) {
			$parts[] = '📍 ' . $loc;
		}
		$types = wp_get_object_terms( $job_id, 'job_type', array( 'fields' => 'names' ) );
		if ( $types && ! is_wp_error( $types ) ) {
			$parts[] = '🕒 ' . implode( '، ', $types );
		}
		if ( $exp = \nmc_get_meta( $job_id, 'job_experience' ) ) {
			$parts[] = '💼 ' . $exp;
		}
		if ( $salary = \nmc_get_meta( $job_id, 'job_salary' ) ) {
			$parts[] = '💰 ' . $salary;
		}
		if ( $deadline = \nmc_get_meta( $job_id, 'job_deadline' ) ) {
			$parts[] = '🗓 ' . $deadline;
		}
		return implode( ' · ', $parts );
	}

	/**
	 * Default hiring stages shown in the wizard/progress.
	 */
	public static function hiring_stages() {
		$stages = array(
			'submission'   => esc_html__( 'ارسال رزومه', 'neomorph-core' ),
			'screening'    => esc_html__( 'غربالگری و امتیازدهی خودکار', 'neomorph-core' ),
			'video'        => esc_html__( 'مصاحبه ویدیویی', 'neomorph-core' ),
			'interview'    => esc_html__( 'مصاحبه حضوری/آنلاین', 'neomorph-core' ),
			'offer'        => esc_html__( 'پیشنهاد همکاری', 'neomorph-core' ),
		);
		return apply_filters( 'nmc_hiring_stages', $stages );
	}

	/**
	 * Admin menu placeholder page (children attach to it).
	 */
	public static function render_menu_page() {
		if ( ! current_user_can( 'manage_nmc_jobs' ) ) {
			return;
		}
		?>
		<div class="wrap nmc-admin">
			<h1>💼 <?php esc_html_e( 'سیستم استخدام', 'neomorph-core' ); ?></h1>
			<p><?php esc_html_e( 'از منوهای «موقعیت‌های شغلی» و «درخواست‌های استخدام» برای مدیریت آگهی‌ها و رزومه‌ها استفاده کنید. هر موقعیت، معیارهای امتیازدهی خودکار دارد.', 'neomorph-core' ); ?></p>
			<ol class="nmc-stages-list">
				<?php foreach ( self::hiring_stages() as $label ) : ?>
					<li class="neo-card"><?php echo esc_html( $label ); ?></li>
				<?php endforeach; ?>
			</ol>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=job' ) ); ?>"><?php esc_html_e( 'موقعیت‌های شغلی', 'neomorph-core' ); ?></a>
				<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=nmc_application' ) ); ?>"><?php esc_html_e( 'درخواست‌های استخدام', 'neomorph-core' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Meta box: job details + scoring criteria repeater.
	 */
	public static function meta_box() {
		add_meta_box( 'nmc_job_box', esc_html__( 'جزئیات شغل و معیارهای امتیازدهی', 'neomorph-core' ), array( __CLASS__, 'render_meta_box' ), 'job', 'normal' );
	}

	/**
	 * Render meta box.
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'nmc_job_meta', 'nmc_job_meta_nonce' );
		$criteria = (array) \nmc_get_meta( $post->ID, 'criteria', array() );
		if ( ! $criteria ) {
			$criteria = array(
				array( 'label' => esc_html__( 'سابقه کاری مرتبط', 'neomorph-core' ), 'weight' => 30, 'keywords' => 'سابقه,تجربه,سال' ),
				array( 'label' => esc_html__( 'مهارت‌های فنی', 'neomorph-core' ), 'weight' => 40, 'keywords' => 'PHP,WordPress,Elementor,REST' ),
				array( 'label' => esc_html__( 'تحصیلات', 'neomorph-core' ), 'weight' => 15, 'keywords' => 'کارشناسی,ارشد,دکترا' ),
				array( 'label' => esc_html__( 'نمونه‌کار/پروژه', 'neomorph-core' ), 'weight' => 15, 'keywords' => 'پروژه,نمونه‌کار,GitHub' ),
			);
		}
		?>
		<div class="nmc-job-meta">
			<p>
				<label><?php esc_html_e( 'موقعیت مکانی', 'neomorph-core' ); ?>
					<input type="text" name="nmc_job_location" value="<?php echo esc_attr( \nmc_get_meta( $post->ID, 'job_location' ) ); ?>" class="regular-text"></label>
				<label><?php esc_html_e( 'حقوق (متن آزاد)', 'neomorph-core' ); ?>
					<input type="text" name="nmc_job_salary" value="<?php echo esc_attr( \nmc_get_meta( $post->ID, 'job_salary' ) ); ?>" class="regular-text"></label>
				<label><?php esc_html_e( 'سابقه مورد نیاز', 'neomorph-core' ); ?>
					<input type="text" name="nmc_job_experience" value="<?php echo esc_attr( \nmc_get_meta( $post->ID, 'job_experience' ) ); ?>" class="regular-text"></label>
				<label><?php esc_html_e( 'مهلت ارسال', 'neomorph-core' ); ?>
					<input type="date" name="nmc_job_deadline" value="<?php echo esc_attr( \nmc_get_meta( $post->ID, 'job_deadline' ) ); ?>"></label>
			</p>

			<h3><?php esc_html_e( 'معیارهای امتیازدهی خودکار', 'neomorph-core' ); ?></h3>
			<p class="description"><?php esc_html_e( 'امتیاز هر رزومه بر اساس تطابق کلیدواژه‌های متن رزومه/پاسخ‌ها با هر معیار و وزن آن محاسبه می‌شود (حداکثر ۱۰۰).', 'neomorph-core' ); ?></p>
			<table class="widefat">
				<thead><tr><th><?php esc_html_e( 'معیار', 'neomorph-core' ); ?></th><th><?php esc_html_e( 'وزن (از ۱۰۰)', 'neomorph-core' ); ?></th><th><?php esc_html_e( 'کلیدواژه‌ها (جدا با کاما)', 'neomorph-core' ); ?></th></tr></thead>
				<tbody>
					<?php foreach ( $criteria as $c ) : ?>
						<tr>
							<td><input type="text" name="crit_label[]" value="<?php echo esc_attr( $c['label'] ); ?>" class="regular-text"></td>
							<td><input type="number" name="crit_weight[]" value="<?php echo esc_attr( $c['weight'] ); ?>" class="small-text"></td>
							<td><input type="text" name="crit_keywords[]" value="<?php echo esc_attr( $c['keywords'] ); ?>" class="large-text"></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Save.
	 */
	public static function save_meta( $post_id ) {
		if ( ! isset( $_POST['nmc_job_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nmc_job_meta_nonce'] ), 'nmc_job_meta' ) ) {
			return;
		}
		foreach ( array( 'nmc_job_location' => 'job_location', 'nmc_job_salary' => 'job_salary', 'nmc_job_experience' => 'job_experience', 'nmc_job_deadline' => 'job_deadline' ) as $field => $key ) {
			if ( isset( $_POST[ $field ] ) ) {
				\nmc_update_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
			}
		}
		if ( isset( $_POST['crit_label'] ) ) {
			$labels   = (array) wp_unslash( $_POST['crit_label'] );
			$weights  = (array) wp_unslash( $_POST['crit_weight'] );
			$keywords = (array) wp_unslash( $_POST['crit_keywords'] );
			$criteria = array();
			foreach ( $labels as $i => $label ) {
				if ( '' === trim( $label ) ) {
					continue;
				}
				$criteria[] = array(
					'label'    => sanitize_text_field( $label ),
					'weight'   => (int) ( $weights[ $i ] ?? 0 ),
					'keywords' => sanitize_text_field( $keywords[ $i ] ?? '' ),
				);
			}
			\nmc_update_meta( $post_id, 'criteria', $criteria );
		}
	}
}

JobController::init();
