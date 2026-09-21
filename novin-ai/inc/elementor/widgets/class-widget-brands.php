<?php
/**
 * المان مشتریان و برندها.
 *
 * @package Novin_AI
 */

namespace Novin_AI\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;

/**
 * کلاس برندها.
 */
class Brands extends Novin_AI_Widget_Base {

	/**
	 * نام المان.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'novin-brands';
	}

	/**
	 * عنوان المان.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'مشتریان و برندها', 'novin-ai' );
	}

	/**
	 * آیکون المان.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-carousel';
	}

	/**
	 * کلمات کلیدی.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords() {
		return array( 'brands', 'برند', 'مشتریان', 'لوگو', 'marquee' );
	}

	/**
	 * ثبت کنترل‌ها.
	 *
	 * @return void
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'لوگوها', 'novin-ai' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			array(
				'label' => esc_html__( 'لوگو', 'novin-ai' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'   => esc_html__( 'نام', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'شرکت فناوری', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'url',
			array(
				'label'       => esc_html__( 'لینک', 'novin-ai' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'آیتم‌ها', 'novin-ai' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'name' => 'TechNova' ),
					array( 'name' => 'DadePaydar' ),
					array( 'name' => 'CloudX' ),
					array( 'name' => 'SmartLabs' ),
					array( 'name' => 'AbrPaye' ),
					array( 'name' => 'NeuralNet' ),
				),
				'title_field' => '{{{ name }}}',
			)
		);

		$this->end_controls_section();

		$this->section_title_controls(
			array(
				'title_default' => esc_html__( 'برندهایی که به ما اعتماد کرده‌اند', 'novin-ai' ),
				'desc'          => false,
			)
		);

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'چیدمان', 'novin-ai' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'نوع نمایش', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'marquee',
				'options' => array(
					'marquee' => esc_html__( 'اسکرول بی‌پایان', 'novin-ai' ),
					'grid'    => esc_html__( 'گرید ثابت', 'novin-ai' ),
				),
			)
		);

		$this->add_responsive_control(
			'grid_columns',
			array(
				'label'          => esc_html__( 'تعداد ستون', 'novin-ai' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '5',
				'tablet_default' => '3',
				'mobile_default' => '2',
				'options'        => array(
					'2' => '۲',
					'3' => '۳',
					'4' => '۴',
					'5' => '۵',
					'6' => '۶',
				),
				'condition'      => array( 'layout' => 'grid' ),
				'selectors'      => array(
					'{{WRAPPER}} .nv-brands--grid' => '--nv-cols: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'      => esc_html__( 'سرعت اسکرول (ثانیه)', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array(
					's' => array(
						'min' => 5,
						'max' => 90,
					),
				),
				'default'    => array( 'size' => 30, 'unit' => 's' ),
				'condition'  => array( 'layout' => 'marquee' ),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => esc_html__( 'توقف هنگام هاور', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'layout' => 'marquee' ),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- استایل ---------------------------------- */
		$this->start_controls_section(
			'section_brand_style',
			array(
				'label' => esc_html__( 'استایل', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'logo_height',
			array(
				'label'      => esc_html__( 'ارتفاع لوگو', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 200,
					),
				),
				'default'    => array( 'size' => 42 ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-brand img' => 'max-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-brand__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'logo_opacity',
			array(
				'label'      => esc_html__( 'شفافیت', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '' ),
				'range'      => array(
					'' => array(
						'min'  => 0.1,
						'max'  => 1,
						'step' => 0.05,
					),
				),
				'default'    => array( 'size' => 0.6, 'unit' => '' ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-brand' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'logo_opacity_hover',
			array(
				'label'      => esc_html__( 'شفافیت در هاور', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '' ),
				'range'      => array(
					'' => array(
						'min'  => 0.1,
						'max'  => 1,
						'step' => 0.05,
					),
				),
				'default'    => array( 'size' => 1, 'unit' => '' ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-brand:hover' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'logo_filter',
				'label'    => esc_html__( 'فیلتر تصویر', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-brand img',
			)
		);

		$this->end_controls_section();

		$this->section_title_style_controls();
	}

	/**
	 * رندر یک آیتم برند.
	 *
	 * @param array<string, mixed> $item تنظیمات آیتم.
	 * @return void
	 */
	protected function render_brand( $item ) {
		$image = ! empty( $item['image']['url'] ) ? $item['image']['url'] : '';
		$name  = ! empty( $item['name'] ) ? $item['name'] : '';
		$attrs = $this->nv_link_attrs( $item, 'url' );

		$tag = ! empty( $attrs ) ? 'a' : 'div';

		echo '<' . esc_attr( $tag ) . ' class="nv-brand"';

		foreach ( $attrs as $attr => $value ) {
			echo ' ' . esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
		}

		echo '>';

		if ( $image ) {
			echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $name ) . '" loading="lazy">';
		} elseif ( $name ) {
			echo '<span class="nv-brand__name">' . esc_html( $name ) . '</span>';
		}

		echo '</' . esc_attr( $tag ) . '>';
	}

	/**
	 * رندر خروجی.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'nv-widget nv-section nv-widget-brands' );

		$speed = ! empty( $settings['speed']['size'] ) ? (int) $settings['speed']['size'] : 30;
		?>
		<section <?php $this->nv_attr( 'wrapper' ); ?>>
			<div class="nv-container">
				<?php $this->render_heading( $settings ); ?>

				<?php if ( empty( $settings['items'] ) ) : ?>
					<p class="nv-widget__empty"><?php esc_html_e( 'لوگویی تعریف نشده است.', 'novin-ai' ); ?></p>
				<?php elseif ( 'marquee' === $settings['layout'] ) : ?>
					<div class="nv-marquee"<?php echo 'yes' === $settings['pause_on_hover'] ? ' data-nv-pause' : ''; ?>>
						<div class="nv-marquee__track nv-brands" style="animation-duration: <?php echo (int) $speed; ?>s">
							<?php
							for ( $i = 0; $i < 2; $i++ ) {
								foreach ( $settings['items'] as $item ) {
									$this->render_brand( $item );
								}
							}
							?>
						</div>
					</div>
				<?php else : ?>
					<div class="nv-grid nv-brands nv-brands--grid">
						<?php
						foreach ( $settings['items'] as $item ) {
							$this->render_brand( $item );
						}
						?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
