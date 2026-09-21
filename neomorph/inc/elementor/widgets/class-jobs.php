<?php
/**
 * Widget: Neo Jobs (استخدام و موقعیت‌های شغلی).
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Jobs
 */
class Widget_Jobs extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-jobs';
	}

	public function get_title() {
		return esc_html__( 'استخدام و موقعیت شغلی', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-briefcase';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان بخش', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'موقعیت‌های شغلی', 'neomorph' ),
		) );
		$this->add_control( 'subtitle', array(
			'label' => esc_html__( 'زیرعنوان', 'neomorph' ),
			'type'  => \Elementor\Controls_Manager::TEXTAREA,
		) );
		$this->add_control( 'layout', array(
			'label'   => esc_html__( 'چیدمان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'cards',
			'options' => array(
				'cards'      => esc_html__( 'کارت‌ها', 'neomorph' ),
				'accordion'  => esc_html__( 'آکاردئونی', 'neomorph' ),
				'timeline'   => esc_html__( 'روند استخدام مرحله‌ای', 'neomorph' ),
				'table'      => esc_html__( 'جدول', 'neomorph' ),
			),
		) );
		$this->add_control( 'count', array(
			'label'   => esc_html__( 'تعداد', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 6,
		) );
		$this->add_control( 'show_apply', array(
			'label'   => esc_html__( 'دکمه درخواست', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'show_steps', array(
			'label'     => esc_html__( 'نمایش مراحل استخدام (روند)', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => array( 'layout' => 'timeline' ),
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'job_style', array(
			'label' => esc_html__( 'کارت شغل', 'neomorph' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'job_title_color', array(
			'label'     => esc_html__( 'رنگ عنوان شغل', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .neo-job-card__title a' => 'color: {{VALUE}};' ),
		) );
		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل کلی', 'neomorph' ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$query = new \WP_Query(
			array(
				'post_type'      => 'job',
				'posts_per_page' => max( 1, (int) $s['count'] ),
				'post_status'    => 'publish',
			)
		);
		?>
		<section class="neo-widget neo-jobs neo-jobs--<?php echo esc_attr( $s['layout'] ); ?> <?php echo esc_attr( $this->neo_widget_class() ); ?>">
			<header class="neo-section__head">
				<h2 class="neo-section__title"><?php echo esc_html( $s['title'] ); ?></h2>
				<?php if ( $s['subtitle'] ) : ?><p class="neo-section__subtitle"><?php echo esc_html( $s['subtitle'] ); ?></p><?php endif; ?>
			</header>

			<?php if ( 'timeline' === $s['layout'] && 'yes' === $s['show_steps'] ) : ?>
				<ol class="neo-steps">
					<?php
					$steps = array(
						esc_html__( 'ارسال رزومه', 'neomorph' ),
						esc_html__( 'غربالگری و امتیازدهی خودکار', 'neomorph' ),
						esc_html__( 'مصاحبه ویدیویی', 'neomorph' ),
						esc_html__( 'مصاحبه حضوری', 'neomorph' ),
						esc_html__( 'پیشنهاد همکاری', 'neomorph' ),
					);
					foreach ( $steps as $i => $step ) :
						?>
						<li class="neo-steps__item neo-surface"><span class="neo-steps__num"><?php echo esc_html( (string) ( $i + 1 ) ); ?></span><?php echo esc_html( $step ); ?></li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>

			<?php if ( $query->have_posts() ) : ?>
				<div class="neo-grid neo-grid--2">
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						$meta = class_exists( '\NeomorphCore\Recruitment\JobController' ) ? \NeomorphCore\Recruitment\JobController::get_job_meta_line( get_the_ID() ) : '';
						?>
						<article <?php post_class( 'neo-card neo-job-card' ); ?>>
							<h3 class="neo-job-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<?php if ( $meta ) : ?><div class="neo-meta"><span class="neo-meta__item"><?php echo esc_html( $meta ); ?></span></div><?php endif; ?>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
							<?php if ( 'yes' === $s['show_apply'] ) : ?>
								<a class="neo-btn neo-btn--primary neo-btn--sm" href="<?php the_permalink(); ?>"><?php esc_html_e( 'درخواست همکاری', 'neomorph' ); ?></a>
							<?php endif; ?>
						</article>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			<?php else : ?>
				<p class="neo-empty"><?php esc_html_e( 'فعلاً موقعیت شغلی بازی ثبت نشده است.', 'neomorph' ); ?></p>
			<?php endif; ?>
		</section>
		<?php
	}
}
