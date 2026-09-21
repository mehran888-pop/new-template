<?php
/**
 * المان فراخوان به اقدام (CTA).
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
 * کلاس CTA.
 */
class CTA extends Novin_AI_Widget_Base {

	/**
	 * نام المان.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'novin-cta';
	}

	/**
	 * عنوان المان.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'فراخوان به اقدام', 'novin-ai' );
	}

	/**
	 * آیکون المان.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-call-to-action';
	}

	/**
	 * کلمات کلیدی.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords() {
		return array( 'cta', 'تماس', 'فراخوان', 'مشاوره', 'اقدام' );
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
				'label' => esc_html__( 'محتوا', 'novin-ai' ),
			)
		);

		$this->add_control(
			'badge_text',
			array(
				'label'   => esc_html__( 'برچسب', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مشاوره رایگان', 'novin-ai' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'novin-ai' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'آماده‌اید کسب‌وکارتان را هوشمند کنید؟', 'novin-ai' ),
				'rows'        => 2,
				'label_block' => true,
			)
		);

		$this->add_control(
			'highlight',
			array(
				'label'   => esc_html__( 'کلمات گرادیانتی', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'هوشمند', 'novin-ai' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => esc_html__( 'توضیح', 'novin-ai' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'یک جلسه ۳۰ دقیقه‌ای با تیم فنی ما کافی است تا مسیر تحول دیجیتال شما ترسیم شود.', 'novin-ai' ),
			)
		);

		$this->add_control(
			'primary_text',
			array(
				'label'   => esc_html__( 'دکمه اصلی', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'دریافت مشاوره رایگان', 'novin-ai' ),
			)
		);

		$this->add_control(
			'primary_link',
			array(
				'label'       => esc_html__( 'لینک دکمه اصلی', 'novin-ai' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);

		$this->add_control(
			'secondary_text',
			array(
				'label'   => esc_html__( 'دکمه دوم', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'تماس با ما', 'novin-ai' ),
			)
		);

		$this->add_control(
			'secondary_link',
			array(
				'label'       => esc_html__( 'لینک دکمه دوم', 'novin-ai' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);

		$this->add_control(
			'note',
			array(
				'label'   => esc_html__( 'یادداشت زیر دکمه‌ها', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'پاسخگویی در کمتر از یک روز کاری', 'novin-ai' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'   => esc_html__( 'متن', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'تحلیل رایگان وضعیت فعلی', 'novin-ai' ),
			)
		);

		$this->add_control(
			'benefits',
			array(
				'label'       => esc_html__( 'مزایا', 'novin-ai' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'text' => esc_html__( 'تحلیل رایگان وضعیت فعلی', 'novin-ai' ) ),
					array( 'text' => esc_html__( 'پیشنهاد معماری اختصاصی', 'novin-ai' ) ),
					array( 'text' => esc_html__( 'برآورد شفاف هزینه', 'novin-ai' ) ),
				),
				'title_field' => '{{{ text }}}',
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => esc_html__( 'تراز', 'novin-ai' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'راست', 'novin-ai' ),
						'icon'  => 'eicon-text-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'وسط', 'novin-ai' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'چپ', 'novin-ai' ),
						'icon'  => 'eicon-text-align-left',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .nv-cta' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->backdrop_controls();

		/* ---------------------------------- استایل ---------------------------------- */
		$this->start_controls_section(
			'section_cta_style',
			array(
				'label' => esc_html__( 'استایل', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'cta_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .nv-cta',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_border',
				'selector' => '{{WRAPPER}} .nv-cta',
			)
		);

		$this->add_responsive_control(
			'cta_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'novin-ai' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-cta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cta_radius',
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
					'{{WRAPPER}} .nv-cta' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cta_title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-cta__title',
			)
		);

		$this->add_control(
			'cta_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-cta__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'highlight_a',
			array(
				'label'     => esc_html__( 'رنگ اول گرادیان', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff7ad9',
				'selectors' => array(
					'{{WRAPPER}} .nv-cta__title .nv-gradient' => 'background-image: linear-gradient(120deg, {{VALUE}}, {{highlight_b.VALUE}});',
				),
			)
		);

		$this->add_control(
			'highlight_b',
			array(
				'label'   => esc_html__( 'رنگ دوم گرادیان', 'novin-ai' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#8b7cff',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cta_desc_typography',
				'label'    => esc_html__( 'تایپوگرافی توضیح', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-cta__desc',
			)
		);

		$this->add_control(
			'cta_desc_color',
			array(
				'label'     => esc_html__( 'رنگ توضیح', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-cta__desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'benefits_color',
			array(
				'label'     => esc_html__( 'رنگ مزایا', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-cta__benefits li' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * رندر خروجی.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'nv-widget nv-section nv-widget-cta' );
		$this->add_render_attribute( 'inner', 'class', 'nv-cta nv-glass nv-reveal' );

		$title     = ! empty( $settings['title'] ) ? $settings['title'] : '';
		$highlight = ! empty( $settings['highlight'] ) ? $settings['highlight'] : '';
		?>
		<section <?php $this->nv_attr( 'wrapper' ); ?>>
			<div class="nv-container">
				<div <?php $this->nv_attr( 'inner' ); ?>>
					<?php $this->nv_backdrop( $settings ); ?>

					<?php if ( ! empty( $settings['badge_text'] ) ) : ?>
						<span class="nv-badge nv-badge--glow">
							<?php echo novin_ai_icon( 'spark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span><?php echo esc_html( $settings['badge_text'] ); ?></span>
						</span>
					<?php endif; ?>

					<?php if ( $title ) : ?>
						<h2 class="nv-cta__title">
							<?php
							if ( $highlight && false !== strpos( $title, $highlight ) ) {
								echo esc_html( str_replace( $highlight, '', $title ) );
								echo '<span class="nv-gradient">' . esc_html( $highlight ) . '</span>';
							} else {
								echo esc_html( $title );
							}
							?>
						</h2>
					<?php endif; ?>

					<?php if ( ! empty( $settings['description'] ) ) : ?>
						<div class="nv-cta__desc"><?php echo wp_kses_post( $settings['description'] ); ?></div>
					<?php endif; ?>

					<?php if ( ! empty( $settings['benefits'] ) ) : ?>
						<ul class="nv-cta__benefits">
							<?php foreach ( $settings['benefits'] as $benefit ) : ?>
								<li>
									<?php echo novin_ai_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<span><?php echo esc_html( $benefit['text'] ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<div class="nv-actions">
						<?php if ( ! empty( $settings['primary_text'] ) ) : ?>
							<?php $this->nv_link_open( $settings, 'primary_link', 'nv-btn nv-btn--primary nv-magnetic' ); ?>
							<span><?php echo esc_html( $settings['primary_text'] ); ?></span>
							<?php echo novin_ai_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php $this->nv_link_close( $settings, 'primary_link' ); ?>
						<?php endif; ?>

						<?php if ( ! empty( $settings['secondary_text'] ) ) : ?>
							<?php $this->nv_link_open( $settings, 'secondary_link', 'nv-btn nv-btn--outline nv-magnetic' ); ?>
							<span><?php echo esc_html( $settings['secondary_text'] ); ?></span>
							<?php $this->nv_link_close( $settings, 'secondary_link' ); ?>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $settings['note'] ) ) : ?>
						<p class="nv-cta__note"><?php echo esc_html( $settings['note'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
