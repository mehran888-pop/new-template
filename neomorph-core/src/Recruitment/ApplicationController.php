<?php
/**
 * Multi-step job application wizard + automatic scoring engine.
 *
 * [neomorph_apply job="123"] renders the wizard (steps configurable via filter).
 * On submit: creates nmc_application, scores resume text against job criteria,
 * notifies admin (Telegram/Bale/SMS), fires apply_received event.
 *
 * Application meta (_nmc_*):
 *  job_id, user_phone, name, email, resume_text, portfolio_url, video_url,
 *  answers (per-step JSON), score, score_details, stage, scored_at
 *
 * @package NeomorphCore\Recruitment
 */

namespace NeomorphCore\Recruitment;

use NeomorphCore\Integrations\Notifier;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ApplicationController
 */
final class ApplicationController {

	public static function init() {
		add_shortcode( 'neomorph_apply', array( __CLASS__, 'shortcode' ) );
		add_action( 'admin_post_nmc_apply_submit', array( __CLASS__, 'handle_submit' ) );
		add_action( 'admin_post_nopriv_nmc_apply_submit', array( __CLASS__, 'handle_submit' ) );
		add_filter( 'manage_nmc_application_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_nmc_application_posts_custom_column', array( __CLASS__, 'column_content' ), 10, 2 );
	}

	/**
	 * Wizard steps.
	 */
	public static function steps() {
		$steps = array(
			'personal'   => array(
				'title'   => esc_html__( 'اطلاعات هویتی', 'neomorph-core' ),
				'fields'  => array( 'name', 'phone', 'email' ),
			),
			'experience' => array(
				'title'  => esc_html__( 'سوابق و مهارت‌ها', 'neomorph-core' ),
				'fields' => array( 'resume_text', 'portfolio_url' ),
			),
			'questions'  => array(
				'title'  => esc_html__( 'سؤالات تکمیلی', 'neomorph-core' ),
				'fields' => array( 'q_motivation', 'q_availability' ),
			),
			'video'      => array(
				'title'  => esc_html__( 'معرفی ویدیویی', 'neomorph-core' ),
				'fields' => array( 'video_url' ),
			),
			'review'     => array(
				'title'  => esc_html__( 'بازبینی و ارسال', 'neomorph-core' ),
				'fields' => array(),
			),
		);
		return apply_filters( 'nmc_apply_steps', $steps );
	}

	/**
	 * [neomorph_apply job="ID" style="wizard|vertical|cards"]
	 */
	public static function shortcode( $atts ) {
		$atts = shortcode_atts( array( 'job' => 0, 'style' => 'wizard' ), $atts, 'neomorph_apply' );
		$job_id = (int) $atts['job'];
		if ( ! $job_id ) {
			$jobs = get_posts( array( 'post_type' => 'job', 'numberposts' => 1, 'fields' => 'ids' ) );
			$job_id = $jobs ? (int) $jobs[0] : 0;
		}
		if ( ! $job_id ) {
			return '<div class="neo-surface neo-empty"><p>' . esc_html__( 'موقعیت شغلی یافت نشد.', 'neomorph-core' ) . '</p></div>';
		}

		wp_enqueue_style( 'nmc-panel', NEOMORPH_CORE_URL . 'assets/css/panel.css', array(), NEOMORPH_CORE_VERSION );
		wp_enqueue_script( 'nmc-apply', NEOMORPH_CORE_URL . 'assets/js/apply.js', array(), NEOMORPH_CORE_VERSION, true );

		$steps = self::steps();
		$title = function_exists( 'neomorph_option' ) ? neomorph_option( 'careers_form_title', esc_html__( 'فرم درخواست همکاری', 'neomorph-core' ) ) : esc_html__( 'فرم درخواست همکاری', 'neomorph-core' );
		$style = sanitize_key( $atts['style'] );
		if ( function_exists( 'neomorph_option' ) ) {
			$style = neomorph_option( 'careers_style', 'wizard' );
		}

		ob_start();
		?>
		<form class="neo-widget neo-surface neo-apply neo-apply--<?php echo esc_attr( $style ); ?>" data-neo-wizard method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'nmc_apply_submit' ); ?>
			<input type="hidden" name="action" value="nmc_apply_submit">
			<input type="hidden" name="job_id" value="<?php echo esc_attr( $job_id ); ?>">

			<h2 class="neo-section__title"><?php echo esc_html( $title ); ?></h2>
			<p class="neo-muted"><?php echo esc_html( get_the_title( $job_id ) ); ?></p>

			<div class="neo-progress"><div class="neo-progress__bar" style="width:20%"></div></div>
			<ol class="neo-steps">
				<?php $i = 1; foreach ( $steps as $slug => $step ) : ?>
					<li class="neo-steps__item neo-surface" data-neo-step-dot><span class="neo-steps__num"><?php echo esc_html( (string) $i ); ?></span><?php echo esc_html( $step['title'] ); ?></li>
					<?php $i++; endforeach; ?>
			</ol>

			<div class="neo-apply__steps">
				<?php $i = 0; foreach ( $steps as $slug => $step ) : $i++; ?>
					<fieldset class="neo-apply__step" data-neo-step <?php echo 0 === ( $i - 1 ) ? '' : 'hidden'; ?>>
						<legend class="neo-apply__legend"><?php echo esc_html( $step['title'] ); ?></legend>

						<?php if ( 'personal' === $slug ) : ?>
							<label class="neo-label"><?php esc_html_e( 'نام و نام خانوادگی', 'neomorph-core' ); ?> *<input class="neo-input" type="text" name="app_name" required></label>
							<label class="neo-label"><?php esc_html_e( 'موبایل', 'neomorph-core' ); ?> *<input class="neo-input" type="tel" name="app_phone" required pattern="09[0-9]{9}"></label>
							<label class="neo-label"><?php esc_html_e( 'ایمیل', 'neomorph-core' ); ?><input class="neo-input" type="email" name="app_email"></label>

						<?php elseif ( 'experience' === $slug ) : ?>
							<label class="neo-label"><?php esc_html_e( 'سوابق و مهارت‌ها (متن رزومه)', 'neomorph-core' ); ?> *
								<textarea class="neo-input neo-textarea" name="app_resume" rows="7" required placeholder="<?php esc_attr_e( 'سوابق کاری، مهارت‌ها، پروژه‌ها… (در امتیازدهی خودکار لحاظ می‌شود)', 'neomorph-core' ); ?>"></textarea>
							</label>
							<label class="neo-label"><?php esc_html_e( 'لینک نمونه‌کار / گیت‌هاب', 'neomorph-core' ); ?><input class="neo-input" type="url" name="app_portfolio"></label>

						<?php elseif ( 'questions' === $slug ) : ?>
							<label class="neo-label"><?php esc_html_e( 'چرا می‌خواهید با ما همکاری کنید؟', 'neomorph-core' ); ?>
								<textarea class="neo-input neo-textarea" name="app_motivation" rows="4"></textarea>
							</label>
							<label class="neo-label"><?php esc_html_e( 'از چه زمانی امکان شروع دارید؟', 'neomorph-core' ); ?>
								<input class="neo-input" type="text" name="app_availability">
							</label>

						<?php elseif ( 'video' === $slug ) : ?>
							<label class="neo-label"><?php esc_html_e( 'لینک معرفی ویدیویی (آپارات/آپلودشده)', 'neomorph-core' ); ?>
								<input class="neo-input" type="url" name="app_video" placeholder="https://...">
							</label>
							<p class="neo-muted"><?php esc_html_e( 'یک ویدیوی کوتاه ۲ دقیقه‌ای از خودتان بسازید و لینک آن را اینجا قرار دهید. این مرحله در «مصاحبه ویدیویی» بررسی می‌شود.', 'neomorph-core' ); ?></p>

						<?php else : ?>
							<p class="neo-inset"><?php esc_html_e( 'پس از ارسال، رزومه شما به‌صورت خودکار امتیازدهی می‌شود و نتیجه مرحله غربالگری از طریق پیامک اطلاع‌رسانی می‌گردد.', 'neomorph-core' ); ?></p>
						<?php endif; ?>

						<div class="neo-apply__nav">
							<button type="button" class="neo-btn" data-neo-prev><?php esc_html_e( 'قبلی', 'neomorph-core' ); ?></button>
							<?php if ( 'review' === $slug ) : ?>
								<button type="submit" class="neo-btn neo-btn--primary"><?php esc_html_e( 'ارسال درخواست', 'neomorph-core' ); ?></button>
							<?php else : ?>
								<button type="button" class="neo-btn neo-btn--primary" data-neo-next><?php esc_html_e( 'بعدی', 'neomorph-core' ); ?></button>
							<?php endif; ?>
						</div>
					</fieldset>
				<?php endforeach; ?>
			</div>
		</form>
		<?php
		return ob_get_clean();
	}

	/**
	 * Handle submission: create application + score + notify.
	 */
	public static function handle_submit() {
		check_admin_referer( 'nmc_apply_submit' );

		$job_id = isset( $_POST['job_id'] ) ? (int) $_POST['job_id'] : 0;
		$name   = isset( $_POST['app_name'] ) ? sanitize_text_field( wp_unslash( $_POST['app_name'] ) ) : '';
		$phone  = isset( $_POST['app_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['app_phone'] ) ) : '';
		$email  = isset( $_POST['app_email'] ) ? sanitize_email( wp_unslash( $_POST['app_email'] ) ) : '';
		$resume = isset( $_POST['app_resume'] ) ? sanitize_textarea_field( wp_unslash( $_POST['app_resume'] ) ) : '';
		$portfolio = isset( $_POST['app_portfolio'] ) ? esc_url_raw( wp_unslash( $_POST['app_portfolio'] ) ) : '';
		$motivation = isset( $_POST['app_motivation'] ) ? sanitize_textarea_field( wp_unslash( $_POST['app_motivation'] ) ) : '';
		$availability = isset( $_POST['app_availability'] ) ? sanitize_text_field( wp_unslash( $_POST['app_availability'] ) ) : '';
		$video  = isset( $_POST['app_video'] ) ? esc_url_raw( wp_unslash( $_POST['app_video'] ) ) : '';

		if ( ! $job_id || ! $name || ! \nmc_is_valid_phone( $phone ) || ! $resume ) {
			wp_die( esc_html__( 'فیلدهای الزامی را تکمیل کنید.', 'neomorph-core' ) );
		}

		$app_id = wp_insert_post(
			array(
				'post_type'   => 'nmc_application',
				'post_status' => 'publish',
				'post_title'  => $name . ' — ' . get_the_title( $job_id ),
			)
		);
		if ( is_wp_error( $app_id ) || ! $app_id ) {
			wp_die( esc_html__( 'خطا در ثبت درخواست.', 'neomorph-core' ) );
		}

		\nmc_update_meta( $app_id, 'job_id', $job_id );
		\nmc_update_meta( $app_id, 'name', $name );
		\nmc_update_meta( $app_id, 'user_phone', \nmc_normalize_phone( $phone ) );
		\nmc_update_meta( $app_id, 'email', $email );
		\nmc_update_meta( $app_id, 'resume_text', $resume );
		\nmc_update_meta( $app_id, 'portfolio_url', $portfolio );
		\nmc_update_meta( $app_id, 'video_url', $video );
		\nmc_update_meta( $app_id, 'answers', array( 'motivation' => $motivation, 'availability' => $availability ) );
		\nmc_update_meta( $app_id, 'stage', 'screening' );
		\nmc_update_meta( $app_id, 'created_at', current_time( 'mysql' ) );

		// ── Automatic scoring ──────────────────────────────────────
		$score_result = self::score( $job_id, $resume . ' ' . $motivation );
		\nmc_update_meta( $app_id, 'score', $score_result['score'] );
		\nmc_update_meta( $app_id, 'score_details', $score_result['details'] );
		\nmc_update_meta( $app_id, 'scored_at', current_time( 'mysql' ) );

		// High scores jump straight to video stage.
		if ( $score_result['score'] >= (int) apply_filters( 'nmc_auto_video_threshold', 70 ) ) {
			\nmc_update_meta( $app_id, 'stage', 'video' );
			Notifier::send_to_user_by_phone(
				\nmc_normalize_phone( $phone ),
				sprintf( '✅ درخواست شما برای «%s» با امتیاز %d پذیرفته مرحله مصاحبه ویدیویی شد.', get_the_title( $job_id ), $score_result['score'] )
			);
		} else {
			Notifier::send_to_user_by_phone(
				\nmc_normalize_phone( $phone ),
				sprintf( 'درخواست شما برای «%s» دریافت شد و در مرحله غربالگری است.', get_the_title( $job_id ) )
			);
		}

		Notifier::send(
			'admin',
			sprintf(
				"📥 رزومه جدید\n%s\nموقعیت: %s\nامتیاز خودکار: %d/100",
				$name,
				get_the_title( $job_id ),
				$score_result['score']
			)
		);

		\nmc_do_event( 'apply_received', array( 'post_id' => $app_id, 'score' => $score_result['score'], 'job_id' => $job_id ) );

		$done_url = add_query_arg( 'applied', 1, get_permalink( $job_id ) );
		wp_safe_redirect( $done_url );
		exit;
	}

	/**
	 * Auto scoring: for each criterion, count keyword hits in text; weight proportionally.
	 *
	 * @return array{score:int, details:array}
	 */
	public static function score( $job_id, $text ) {
		$criteria = (array) \nmc_get_meta( $job_id, 'criteria', array() );
		$text     = mb_strtolower( (string) $text );
		$details  = array();
		$total    = 0;
		$max      = 0;

		foreach ( $criteria as $c ) {
			$weight   = max( 0, (int) ( $c['weight'] ?? 0 ) );
			$keywords = array_filter( array_map( 'trim', explode( ',', (string) ( $c['keywords'] ?? '' ) ) ) );
			$hits     = 0;
			foreach ( $keywords as $kw ) {
				$kw = mb_strtolower( $kw );
				if ( '' !== $kw && false !== mb_strpos( $text, $kw ) ) {
					$hits++;
				}
			}
			$ratio   = $keywords ? ( $hits / count( $keywords ) ) : 0;
			$points  = (int) round( $weight * min( 1, $ratio ) );
			$total  += $points;
			$max    += $weight;
			$details[] = array(
				'label'  => $c['label'] ?? '',
				'hits'   => $hits,
				'total'  => count( $keywords ),
				'points' => $points,
				'weight' => $weight,
			);
		}

		$score = $max > 0 ? (int) round( $total / $max * 100 ) : 0;
		return array( 'score' => $score, 'details' => $details );
	}

	/* ── Admin list columns ──────────────────────────────────────── */

	/**
	 * Columns.
	 */
	public static function columns( $columns ) {
		return array(
			'cb'       => isset( $columns['cb'] ) ? $columns['cb'] : '',
			'title'    => esc_html__( 'درخواست‌دهنده', 'neomorph-core' ),
			'job'      => esc_html__( 'موقعیت', 'neomorph-core' ),
			'phone'    => esc_html__( 'موبایل', 'neomorph-core' ),
			'score'    => esc_html__( 'امتیاز', 'neomorph-core' ),
			'stage'    => esc_html__( 'مرحله', 'neomorph-core' ),
			'date'     => esc_html__( 'تاریخ', 'neomorph-core' ),
		);
	}

	/**
	 * Column content.
	 */
	public static function column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'job':
				echo esc_html( get_the_title( (int) \nmc_get_meta( $post_id, 'job_id' ) ) );
				break;
			case 'phone':
				echo esc_html( \nmc_get_meta( $post_id, 'user_phone' ) );
				break;
			case 'score':
				$score = (int) \nmc_get_meta( $post_id, 'score' );
				printf( '<strong style="color:%s">%d</strong>', $score >= 70 ? '#00b894' : ( $score >= 40 ? '#fdcb6e' : '#e17055' ), $score );
				break;
			case 'stage':
				$stages = JobController::hiring_stages();
				$stage  = \nmc_get_meta( $post_id, 'stage', 'screening' );
				echo esc_html( isset( $stages[ $stage ] ) ? $stages[ $stage ] : $stage );
				break;
		}
	}
}

ApplicationController::init();
