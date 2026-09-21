<?php
/**
 * Widget: Neo Hero (هیرو) — layouts: split / centered / compact.
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Hero
 */
class Widget_Hero extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-hero';
	}

	public function get_title() {
		return esc_html__( 'هیرو نئومورف', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-slideshow';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array( 'label' => esc_html__( 'محتوا', 'neomorph' ) )
		);

		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'چیدمان', 'neomorph' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'split',
				'options' => array(
					'split'    => esc_html__( 'دو ستونه (متن + تصویر)', 'neomorph' ),
					'centered' => esc_html__( 'وسط‌چین', 'neomorph' ),
					'compact'  => esc_html__( 'جمع‌وجور', 'neomorph' ),
				),
			)
		);

		$this->add_control( 'eyebrow', array(
			'label' => esc_html__( 'متن بالای تیتر', 'neomorph' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		) );

		$this->add_control( 'title', array(
			'label'       => esc_html__( 'تیتر', 'neomorph' ),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'default'     => esc_html__( 'کسب‌وکار شما، با ظاهری نرم و مدرن', 'neomorph' ),
			'description' => 'با <span> می‌توانید بخشی از تیتر را رنگی کنید.',
		) );

		$this->add_control( 'subtitle', array(
			'label'   => esc_html__( 'زیرتیتر', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'قالب نئومورف — سازگار با المنتور، ووکامرس و گراویتی فرم', 'neomorph' ),
		) );

		$this->add_control( 'image', array(
			'label' => esc_html__( 'تصویر', 'neomorph' ),
			'type'  => \Elementor\Controls_Manager::MEDIA,
		) );

		$this->add_control( 'btn1_text', array(
			'label'   => esc_html__( 'دکمه اول — متن', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'شروع کنید', 'neomorph' ),
		) );
		$this->add_control( 'btn1_link', array(
			'label'   => esc_html__( 'دکمه اول — لینک', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::URL,
			'dynamic' => array( 'active' => true ),
		) );
		$this->add_control( 'btn2_text', array(
			'label'   => esc_html__( 'دکمه دوم — متن', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'مشاهده خدمات', 'neomorph' ),
		) );
		$this->add_control( 'btn2_link', array(
			'label' => esc_html__( 'دکمه دوم — لینک', 'neomorph' ),
			'type'  => \Elementor\Controls_Manager::URL,
		) );

		$this->add_control( 'shape', array(
			'label'   => esc_html__( 'شکل هندسی پس‌زمینه', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'blobs',
			'options' => array(
				'blobs' => esc_html__( 'حباب‌های نرم', 'neomorph' ),
				'grid'  => esc_html__( 'گرید نقطه‌ای', 'neomorph' ),
				'none'  => esc_html__( 'بدون', 'neomorph' ),
			),
		) );

		$this->end_controls_section();

		// Style: title.
		$this->start_controls_section( 'title_style', array(
			'label' => esc_html__( 'تیتر و متن', 'neomorph' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'title_color', array(
			'label'     => esc_html__( 'رنگ تیتر', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .neo-hero__title' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'title_accent', array(
			'label'     => esc_html__( 'رنگ تیتر (بخش رنگی)', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#6c5ce7',
			'selectors' => array( '{{WRAPPER}} .neo-hero__title span' => 'color: {{VALUE}};' ),
		) );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'title_typo',
			'selector' => '{{WRAPPER}} .neo-hero__title',
		) );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'sub_typo',
			'selector' => '{{WRAPPER}} .neo-hero__subtitle',
		) );
		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل هیرو', 'neomorph' ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="neo-widget neo-hero neo-hero--<?php echo esc_attr( $s['layout'] ); ?> <?php echo esc_attr( $this->neo_widget_class() ); ?> neo-shape--<?php echo esc_attr( $s['shape'] ); ?>">
			<div class="neo-hero__content">
				<?php if ( $s['eyebrow'] ) : ?>
					<span class="neo-hero__eyebrow neo-badge"><?php echo wp_kses_post( $s['eyebrow'] ); ?></span>
				<?php endif; ?>
				<h1 class="neo-hero__title"><?php echo wp_kses_post( str_replace( array( '<span>', '</span>' ), array( '<span class="neo-accent">', '</span>' ), $s['title'] ) ); ?></h1>
				<?php if ( $s['subtitle'] ) : ?>
					<p class="neo-hero__subtitle"><?php echo wp_kses_post( $s['subtitle'] ); ?></p>
				<?php endif; ?>
				<div class="neo-hero__actions">
					<?php if ( $s['btn1_text'] ) : ?>
						<a class="neo-btn neo-btn--primary"
							href="<?php echo esc_url( isset( $s['btn1_link']['url'] ) ? $s['btn1_link']['url'] : '#' ); ?>"
							<?php echo ! empty( $s['btn1_link']['is_external'] ) ? 'target="_blank" rel="noopener"' : ''; ?>>
							<?php echo esc_html( $s['btn1_text'] ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $s['btn2_text'] ) : ?>
						<a class="neo-btn" href="<?php echo esc_url( isset( $s['btn2_link']['url'] ) ? $s['btn2_link']['url'] : '#' ); ?>"
							<?php echo ! empty( $s['btn2_link']['is_external'] ) ? 'target="_blank" rel="noopener"' : ''; ?>>
							<?php echo esc_html( $s['btn2_text'] ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
			<?php if ( 'split' === $s['layout'] && ! empty( $s['image']['url'] ) ) : ?>
				<div class="neo-hero__media neo-media neo-inset">
					<img src="<?php echo esc_url( $s['image']['url'] ); ?>" alt="<?php echo esc_attr( $s['eyebrow'] ? wp_strip_all_tags( $s['eyebrow'] ) : 'hero' ); ?>">
				</div>
			<?php endif; ?>
		</section>
		<?php
	}
}
