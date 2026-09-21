<?php
/**
 * المان آمار و دستاوردها.
 *
 * @package Novin_AI
 */

namespace Novin_AI\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

/**
 * کلاس آمار.
 */
class Stats extends Novin_AI_Widget_Base {

	/**
	 * نام المان.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'novin-stats';
	}

	/**
	 * عنوان المان.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'آمار و دستاوردها', 'novin-ai' );
	}

	/**
	 * آیکون المان.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-counter';
	}

	/**
	 * کلمات کلیدی.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords() {
		return array( 'stats', 'آمار', 'counter', 'شمارنده', 'دستاورد' );
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
				'label' => esc_html__( 'آیتم‌ها', 'novin-ai' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon',
			array(
				'label' => esc_html__( 'آیکون', 'novin-ai' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$repeater->add_control(
			'number',
			array(
				'label'   => esc_html__( 'عدد', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '250',
			)
		);

		$repeater->add_control(
			'prefix',
			array(
				'label' => esc_html__( 'پیشوند', 'novin-ai' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'suffix',
			array(
				'label'   => esc_html__( 'پسوند', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '+',
			)
		);

		$repeater->add_control(
			'label',
			array(
				'label'   => esc_html__( 'برچسب', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'پروژه تحویل شده', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'desc',
			array(
				'label' => esc_html__( 'توضیح کوتاه', 'novin-ai' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'آیتم‌ها', 'novin-ai' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'number' => '250',
						'suffix' => '+',
						'label'  => esc_html__( 'پروژه تحویل شده', 'novin-ai' ),
					),
					array(
						'number' => '98',
						'suffix' => '%',
						'label'  => esc_html__( 'رضایت مشتریان', 'novin-ai' ),
					),
					array(
						'number' => '42',
						'suffix' => '',
						'label'  => esc_html__( 'متخصص فنی', 'novin-ai' ),
					),
					array(
						'number' => '15',
						'suffix' => esc_html__( 'سال', 'novin-ai' ),
						'label'  => esc_html__( 'تجربه در صنعت', 'novin-ai' ),
					),
				),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();

		$this->section_title_controls(
			array(
				'title_default' => esc_html__( 'اعدادی که مسیر ما را نشان می‌دهند', 'novin-ai' ),
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
				'default' => 'cards',
				'options' => array(
					'cards' => esc_html__( 'کارت‌دار', 'novin-ai' ),
					'plain' => esc_html__( 'ساده', 'novin-ai' ),
				),
			)
		);

		$this->add_control(
			'card_style',
			array(
				'label'     => esc_html__( 'استایل کارت', 'novin-ai' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'glass',
				'options'   => array(
					'glass'   => esc_html__( 'شیشه‌ای', 'novin-ai' ),
					'solid'   => esc_html__( 'ساده', 'novin-ai' ),
					'outline' => esc_html__( 'خطی', 'novin-ai' ),
					'neon'    => esc_html__( 'نئونی', 'novin-ai' ),
				),
				'condition' => array( 'layout' => 'cards' ),
			)
		);

		$this->layout_controls( array( 'default' => 4 ) );

		$this->add_control(
			'enable_reveal',
			array(
				'label'        => esc_html__( 'ظهور تدریجی', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->backdrop_controls();

		/* ---------------------------------- استایل ---------------------------------- */
		$this->start_controls_section(
			'section_stat_style',
			array(
				'label' => esc_html__( 'استایل', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'stat_background',
				'label'    => esc_html__( 'پس‌زمینه آیتم', 'novin-ai' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .nv-stat',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'stat_border',
				'label'    => esc_html__( 'حاشیه آیتم', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-stat',
			)
		);

		$this->add_responsive_control(
			'stat_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'novin-ai' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-stat' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'stat_radius',
			array(
				'label'      => esc_html__( 'انحنا', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-stat' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'number_typography',
				'label'    => esc_html__( 'تایپوگرافی عدد', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-stat__number',
			)
		);

		$this->add_control(
			'number_color',
			array(
				'label'     => esc_html__( 'رنگ اول عدد', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8b7cff',
				'selectors' => array(
					'{{WRAPPER}} .nv-stat__number' => 'background-image: linear-gradient(120deg, {{VALUE}}, {{number_color_b.VALUE}});',
				),
			)
		);

		$this->add_control(
			'number_color_b',
			array(
				'label'   => esc_html__( 'رنگ دوم عدد', 'novin-ai' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#22d3ee',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'stat_label_typography',
				'label'    => esc_html__( 'تایپوگرافی برچسب', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-stat__label',
			)
		);

		$this->add_control(
			'stat_label_color',
			array(
				'label'     => esc_html__( 'رنگ برچسب', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-stat__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'stat_icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-stat__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->section_title_style_controls();
	}

	/**
	 * رندر خروجی.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'nv-widget nv-section nv-widget-stats nv-stats nv-stats--' . sanitize_html_class( $settings['layout'] ) );
		?>
		<section <?php $this->nv_attr( 'wrapper' ); ?>>
			<?php $this->nv_backdrop( $settings ); ?>

			<div class="nv-container">
				<?php $this->render_heading( $settings ); ?>

				<div class="nv-grid">
					<?php if ( ! empty( $settings['items'] ) ) : ?>
						<?php foreach ( $settings['items'] as $item ) : ?>
							<?php
							$classes = array( 'nv-stat', 'nv-stat--' . sanitize_html_class( $settings['layout'] ) );

							if ( 'cards' === $settings['layout'] ) {
								$classes[] = 'nv-card';
								$classes[] = $this->nv_classes( $settings, array() );
							} elseif ( 'yes' === $settings['enable_reveal'] ) {
								$classes[] = 'nv-reveal';
							}
							?>
							<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
								<?php if ( ! empty( $item['icon']['value'] ) ) : ?>
									<span class="nv-stat__icon"><?php $this->nv_icon( $item['icon'] ); ?></span>
								<?php endif; ?>

								<span class="nv-stat__number">
									<?php if ( ! empty( $item['prefix'] ) ) : ?>
										<span class="nv-stat__prefix"><?php echo esc_html( $item['prefix'] ); ?></span>
									<?php endif; ?>

									<span class="nv-counter" data-nv-count="<?php echo esc_attr( (int) preg_replace( '/\D/', '', (string) $item['number'] ) ); ?>">0</span>

									<?php if ( ! empty( $item['suffix'] ) ) : ?>
										<span class="nv-stat__suffix"><?php echo esc_html( $item['suffix'] ); ?></span>
									<?php endif; ?>
								</span>

								<span class="nv-stat__label"><?php echo esc_html( $item['label'] ); ?></span>

								<?php if ( ! empty( $item['desc'] ) ) : ?>
									<span class="nv-stat__desc"><?php echo esc_html( $item['desc'] ); ?></span>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<p class="nv-widget__empty"><?php esc_html_e( 'آیتمی تعریف نشده است.', 'novin-ai' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
