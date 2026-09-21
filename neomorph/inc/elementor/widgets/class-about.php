<?php
/**
 * Widget: Neo About (درباره ما).
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_About
 */
class Widget_About extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-about';
	}

	public function get_title() {
		return esc_html__( 'درباره ما', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-info-box-o';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'layout', array(
			'label'   => esc_html__( 'چیدمان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'image-side',
			'options' => array(
				'image-side'  => esc_html__( 'تصویر کنار متن', 'neomorph' ),
				'overlap'     => esc_html__( 'تصویر روی هم (Overlapping)', 'neomorph' ),
				'stats-focus' => esc_html__( 'تمرکز روی آمار', 'neomorph' ),
			),
		) );

		$this->add_control( 'eyebrow', array(
			'label' => esc_html__( 'متن کوچک بالا', 'neomorph' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		) );
		$this->add_control( 'title', array(
			'label'   => esc_html__( 'تیتر', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'درباره ما', 'neomorph' ),
		) );
		$this->add_control( 'text', array(
			'label'   => esc_html__( 'متن', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::WYSIWYG,
			'default' => esc_html__( 'ما با تمرکز بر طراحی نرم و تجربه کاربری دلپذیر، محصولاتی می‌سازیم که هم زیبا هستند و هم کاربردی.', 'neomorph' ),
		) );
		$this->add_control( 'image', array(
			'label' => esc_html__( 'تصویر', 'neomorph' ),
			'type'  => \Elementor\Controls_Manager::MEDIA,
		) );
		$this->add_control( 'btn_text', array(
			'label'   => esc_html__( 'متن دکمه', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
		) );
		$this->add_control( 'btn_link', array(
			'label' => esc_html__( 'لینک دکمه', 'neomorph' ),
			'type'  => \Elementor\Controls_Manager::URL,
		) );

		$stats = new \Elementor\Repeater();
		$stats->add_control( 'number', array(
			'label'   => esc_html__( 'عدد', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '۱۰۰+',
		) );
		$stats->add_control( 'label', array(
			'label'   => esc_html__( 'برچسب', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'مشتری راضی', 'neomorph' ),
		) );
		$this->add_control( 'stats', array(
			'label'  => esc_html__( 'آمار', 'neomorph' ),
			'type'   => \Elementor\Controls_Manager::REPEATER,
			'fields' => $stats->get_controls(),
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'stats_style', array(
			'label' => esc_html__( 'آمار', 'neomorph' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'stat_number_color', array(
			'label'     => esc_html__( 'رنگ اعداد', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#6c5ce7',
			'selectors' => array( '{{WRAPPER}} .neo-stat__number' => 'color: {{VALUE}};' ),
		) );
		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل کلی', 'neomorph' ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="neo-widget neo-about neo-about--<?php echo esc_attr( $s['layout'] ); ?> <?php echo esc_attr( $this->neo_widget_class() ); ?>">
			<div class="neo-about__grid">
				<div class="neo-about__content">
					<?php if ( $s['eyebrow'] ) : ?><span class="neo-badge"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
					<?php if ( $s['title'] ) : ?><h2 class="neo-section__title"><?php echo esc_html( $s['title'] ); ?></h2><?php endif; ?>
					<div class="neo-about__text"><?php echo wp_kses_post( $s['text'] ); ?></div>
					<?php if ( $s['btn_text'] ) : ?>
						<a class="neo-btn neo-btn--primary" href="<?php echo esc_url( isset( $s['btn_link']['url'] ) ? $s['btn_link']['url'] : '#' ); ?>"><?php echo esc_html( $s['btn_text'] ); ?></a>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $s['image']['url'] ) ) : ?>
					<div class="neo-about__media neo-media neo-inset">
						<img src="<?php echo esc_url( $s['image']['url'] ); ?>" alt="<?php echo esc_attr( $s['title'] ); ?>">
					</div>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( $s['stats'] ) ) : ?>
				<div class="neo-about__stats neo-grid neo-grid--<?php echo esc_attr( min( 4, max( 2, count( $s['stats'] ) ) ) ); ?>">
					<?php foreach ( $s['stats'] as $stat ) : ?>
						<div class="neo-stat neo-inset">
							<span class="neo-stat__number"><?php echo esc_html( $stat['number'] ); ?></span>
							<span class="neo-stat__label"><?php echo esc_html( $stat['label'] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</section>
		<?php
	}
}
