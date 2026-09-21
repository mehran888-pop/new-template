<?php
/**
 * المان هیرو سه‌بعدی و فراگیر.
 *
 * @package Novin_AI
 */

namespace Novin_AI\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

/**
 * کلاس هیرو.
 */
class Hero extends Novin_AI_Widget_Base {

	/**
	 * نام المان.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'novin-hero';
	}

	/**
	 * عنوان المان.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'هیرو سه‌بعدی', 'novin-ai' );
	}

	/**
	 * آیکون المان.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-banner';
	}

	/**
	 * کلمات کلیدی.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords() {
		return array( 'hero', 'هیرو', 'سربرگ', 'بنر', '3d', 'immersive' );
	}

	/**
	 * ثبت کنترل‌ها.
	 *
	 * @return void
	 */
	protected function register_controls() {

		/* ---------------------------------- محتوا ---------------------------------- */
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'محتوا', 'novin-ai' ),
			)
		);

		$this->add_control(
			'badge_text',
			array(
				'label'   => esc_html__( 'برچسب بالای عنوان', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'هوش مصنوعی + مهندسی نرم‌افزار', 'novin-ai' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان اصلی', 'novin-ai' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'آینده کسب‌وکار را با هوش مصنوعی بسازید', 'novin-ai' ),
				'rows'        => 2,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'highlight',
			array(
				'label'       => esc_html__( 'کلمات گرادیانتی', 'novin-ai' ),
				'description' => esc_html__( 'بخشی از عنوان که با رنگ گرادیانتی نمایش داده می‌شود.', 'novin-ai' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'هوش مصنوعی', 'novin-ai' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => esc_html__( 'تگ عنوان', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h1',
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'p'  => 'P',
				),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'       => esc_html__( 'توضیح', 'novin-ai' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => esc_html__( 'از مدل‌های زبانی اختصاصی تا اپلیکیشن‌های ابری و خدمات پشتیبانی ۲۴ ساعته؛ مسیر تحول دیجیتال شما را هموار می‌کنیم.', 'novin-ai' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'primary_button_text',
			array(
				'label'   => esc_html__( 'متن دکمه اصلی', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'شروع پروژه', 'novin-ai' ),
			)
		);

		$this->add_control(
			'primary_button_link',
			array(
				'label'       => esc_html__( 'لینک دکمه اصلی', 'novin-ai' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);

		$this->add_control(
			'secondary_button_text',
			array(
				'label'   => esc_html__( 'متن دکمه دوم', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مشاهده نمونه‌کارها', 'novin-ai' ),
			)
		);

		$this->add_control(
			'secondary_button_link',
			array(
				'label'       => esc_html__( 'لینک دکمه دوم', 'novin-ai' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'چیدمان', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'split',
				'options' => array(
					'split'  => esc_html__( 'متن و تصویر کنار هم', 'novin-ai' ),
					'center' => esc_html__( 'متن وسط‌چین', 'novin-ai' ),
				),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- آمار ---------------------------------- */
		$this->start_controls_section(
			'section_stats',
			array(
				'label' => esc_html__( 'آمار سریع', 'novin-ai' ),
			)
		);

		$this->add_control(
			'show_stats',
			array(
				'label'        => esc_html__( 'نمایش آمار', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$stats = new Repeater();

		$stats->add_control(
			'number',
			array(
				'label'   => esc_html__( 'عدد', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '120',
			)
		);

		$stats->add_control(
			'suffix',
			array(
				'label'   => esc_html__( 'پسوند', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '+',
			)
		);

		$stats->add_control(
			'label',
			array(
				'label'   => esc_html__( 'برچسب', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'پروژه موفق', 'novin-ai' ),
			)
		);

		$this->add_control(
			'stats',
			array(
				'label'       => esc_html__( 'آیتم‌ها', 'novin-ai' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $stats->get_controls(),
				'default'     => array(
					array(
						'number' => '120',
						'suffix' => '+',
						'label'  => esc_html__( 'پروژه موفق', 'novin-ai' ),
					),
					array(
						'number' => '98',
						'suffix' => '%',
						'label'  => esc_html__( 'رضایت مشتریان', 'novin-ai' ),
					),
					array(
						'number' => '24',
						'suffix' => '/7',
						'label'  => esc_html__( 'پشتیبانی', 'novin-ai' ),
					),
				),
				'title_field' => '{{{ label }}}',
				'condition'   => array( 'show_stats' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- تصویر و لایه‌ها ---------------------------------- */
		$this->start_controls_section(
			'section_visual',
			array(
				'label' => esc_html__( 'تصویر و لایه‌های سه‌بعدی', 'novin-ai' ),
			)
		);

		$this->add_control(
			'visual_type',
			array(
				'label'   => esc_html__( 'نوع تصویر', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => array(
					'image' => esc_html__( 'تصویر', 'novin-ai' ),
					'none'  => esc_html__( 'بدون تصویر', 'novin-ai' ),
				),
			)
		);

		$this->add_control(
			'visual_image',
			array(
				'label'     => esc_html__( 'تصویر اصلی', 'novin-ai' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'visual_type' => 'image' ),
			)
		);

		$this->add_control(
			'enable_tilt',
			array(
				'label'        => esc_html__( 'چرخش سه‌بعدی تصویر', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$floats = new Repeater();

		$floats->add_control(
			'title',
			array(
				'label'   => esc_html__( 'عنوان', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'پردازش زبان طبیعی', 'novin-ai' ),
			)
		);

		$floats->add_control(
			'value',
			array(
				'label'   => esc_html__( 'مقدار / توضیح', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'دقت ۹۶٪', 'novin-ai' ),
			)
		);

		$floats->add_control(
			'icon',
			array(
				'label' => esc_html__( 'آیکون', 'novin-ai' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'floats',
			array(
				'label'       => esc_html__( 'کارت‌های شناور', 'novin-ai' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $floats->get_controls(),
				'default'     => array(
					array(
						'title' => esc_html__( 'پردازش زبان طبیعی', 'novin-ai' ),
						'value' => esc_html__( 'دقت ۹۶٪', 'novin-ai' ),
					),
					array(
						'title' => esc_html__( 'زیرساخت ابری', 'novin-ai' ),
						'value' => esc_html__( 'آپتایم ۹۹.۹٪', 'novin-ai' ),
					),
				),
				'title_field' => '{{{ title }}}',
				'condition'   => array( 'visual_type' => 'image' ),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- مارکوئی ---------------------------------- */
		$this->start_controls_section(
			'section_marquee',
			array(
				'label' => esc_html__( 'اسکرول متنی', 'novin-ai' ),
			)
		);

		$this->add_control(
			'show_marquee',
			array(
				'label'        => esc_html__( 'نمایش اسکرول متنی', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'marquee_items',
			array(
				'label'       => esc_html__( 'عبارت‌ها (با کاما جدا کنید)', 'novin-ai' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'هوش مصنوعی, یادگیری ماشین, بینایی ماشین, پردازش ابری, امنیت سایبری, اتوماسیون', 'novin-ai' ),
				'label_block' => true,
				'condition'   => array( 'show_marquee' => 'yes' ),
			)
		);

		$this->add_control(
			'marquee_speed',
			array(
				'label'      => esc_html__( 'سرعت (ثانیه)', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array(
					's' => array(
						'min' => 5,
						'max' => 90,
					),
				),
				'default'    => array( 'size' => 32, 'unit' => 's' ),
				'condition'  => array( 'show_marquee' => 'yes' ),
			)
		);

		$this->add_control(
			'show_scroll_hint',
			array(
				'label'        => esc_html__( 'نشانگر اسکرول', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		$this->backdrop_controls();

		/* ---------------------------------- استایل‌ها ---------------------------------- */
		$this->start_controls_section(
			'section_hero_style',
			array(
				'label' => esc_html__( 'استایل بخش', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'hero_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .nv-hero',
			)
		);

		$this->add_responsive_control(
			'hero_min_height',
			array(
				'label'      => esc_html__( 'حداقل ارتفاع', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 300,
						'max' => 1400,
					),
					'vh' => array(
						'min' => 30,
						'max' => 150,
					),
				),
				'default'    => array( 'size' => 92, 'unit' => 'vh' ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-hero' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hero_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'novin-ai' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-hero' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- استایل متن ---------------------------------- */
		$this->start_controls_section(
			'section_text_style',
			array(
				'label' => esc_html__( 'استایل متن', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-hero__title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-hero__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'highlight_gradient_a',
			array(
				'label'     => esc_html__( 'رنگ اول گرادیان', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8b7cff',
				'selectors' => array(
					'{{WRAPPER}} .nv-hero__title .nv-gradient' => 'background-image: linear-gradient(120deg, {{VALUE}}, {{highlight_gradient_b.VALUE}});',
				),
			)
		);

		$this->add_control(
			'highlight_gradient_b',
			array(
				'label'     => esc_html__( 'رنگ دوم گرادیان', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#22d3ee',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'label'    => esc_html__( 'تایپوگرافی توضیح', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-hero__desc',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => esc_html__( 'رنگ توضیح', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-hero__desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'badge_typography',
				'label'    => esc_html__( 'تایپوگرافی برچسب', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-badge',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'badge_background',
				'label'    => esc_html__( 'پس‌زمینه برچسب', 'novin-ai' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .nv-badge',
			)
		);

		$this->add_control(
			'badge_color',
			array(
				'label'     => esc_html__( 'رنگ برچسب', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-badge' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'stat_number_typography',
				'label'    => esc_html__( 'تایپوگرافی عدد آمار', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-stat__number',
			)
		);

		$this->add_control(
			'stat_number_color',
			array(
				'label'     => esc_html__( 'رنگ عدد آمار', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-stat__number' => 'background-image: linear-gradient(120deg, {{VALUE}}, {{stat_label_color.VALUE}});',
				),
			)
		);

		$this->add_control(
			'stat_label_color',
			array(
				'label'     => esc_html__( 'رنگ برچسب آمار', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-stat__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- استایل تصویر ---------------------------------- */
		$this->start_controls_section(
			'section_visual_style',
			array(
				'label' => esc_html__( 'استایل تصویر', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'visual_radius',
			array(
				'label'      => esc_html__( 'انحنای تصویر', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'default'    => array( 'size' => 28, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-hero__visual img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'visual_shadow',
				'selector' => '{{WRAPPER}} .nv-hero__visual img',
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

		$classes = array( 'nv-hero', 'nv-hero--' . $settings['layout'] );

		if ( 'yes' === $settings['enable_tilt'] ) {
			$classes[] = 'nv-hero--tilt';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$title     = ! empty( $settings['title'] ) ? $settings['title'] : '';
		$highlight = ! empty( $settings['highlight'] ) ? $settings['highlight'] : '';
		$tag       = ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h1';
		$speed     = ! empty( $settings['marquee_speed']['size'] ) ? (int) $settings['marquee_speed']['size'] : 32;

		$this->add_render_attribute( 'marquee', 'class', 'nv-marquee__track' );
		$this->add_render_attribute( 'marquee', 'style', 'animation-duration:' . $speed . 's' );
		?>
		<section <?php $this->nv_attr( 'wrapper' ); ?>>
			<?php $this->nv_backdrop( $settings ); ?>

			<div class="nv-container nv-hero__inner">

				<div class="nv-hero__content nv-reveal">
					<?php if ( ! empty( $settings['badge_text'] ) ) : ?>
						<span class="nv-badge nv-badge--glow">
							<?php echo novin_ai_icon( 'spark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span><?php echo esc_html( $settings['badge_text'] ); ?></span>
						</span>
					<?php endif; ?>

					<?php if ( $title ) : ?>
						<?php
						echo '<' . esc_attr( $tag ) . ' class="nv-hero__title">';

						if ( $highlight && false !== strpos( $title, $highlight ) ) {
							echo esc_html( str_replace( $highlight, '', $title ) );
							echo '<span class="nv-gradient">' . esc_html( $highlight ) . '</span>';
						} else {
							echo esc_html( $title );
						}

						echo '</' . esc_attr( $tag ) . '>';
						?>
					<?php endif; ?>

					<?php if ( ! empty( $settings['description'] ) ) : ?>
						<div class="nv-hero__desc"><?php echo wp_kses_post( $settings['description'] ); ?></div>
					<?php endif; ?>

					<div class="nv-actions">
						<?php if ( ! empty( $settings['primary_button_text'] ) ) : ?>
							<?php $this->nv_link_open( $settings, 'primary_button_link', 'nv-btn nv-btn--primary nv-magnetic' ); ?>
							<span><?php echo esc_html( $settings['primary_button_text'] ); ?></span>
							<?php echo novin_ai_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php $this->nv_link_close( $settings, 'primary_button_link' ); ?>
						<?php endif; ?>

						<?php if ( ! empty( $settings['secondary_button_text'] ) ) : ?>
							<?php $this->nv_link_open( $settings, 'secondary_button_link', 'nv-btn nv-btn--outline nv-magnetic' ); ?>
							<span><?php echo esc_html( $settings['secondary_button_text'] ); ?></span>
							<?php $this->nv_link_close( $settings, 'secondary_button_link' ); ?>
						<?php endif; ?>
					</div>

					<?php if ( 'yes' === $settings['show_stats'] && ! empty( $settings['stats'] ) ) : ?>
						<div class="nv-stats nv-stats--inline">
							<?php foreach ( $settings['stats'] as $stat ) : ?>
								<div class="nv-stat">
									<span class="nv-stat__number">
										<span class="nv-counter" data-nv-count="<?php echo esc_attr( (int) preg_replace( '/\D/', '', (string) $stat['number'] ) ); ?>">0</span>
										<?php echo esc_html( $stat['suffix'] ); ?>
									</span>
									<span class="nv-stat__label"><?php echo esc_html( $stat['label'] ); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( 'image' === $settings['visual_type'] && ! empty( $settings['visual_image']['url'] ) ) : ?>
					<div class="nv-hero__visual">
						<div class="nv-hero__visual-glow" aria-hidden="true"></div>

						<div class="nv-3d-stage <?php echo 'yes' === $settings['enable_tilt'] ? 'nv-tilt' : ''; ?>">
							<img class="nv-hero__img" src="<?php echo esc_url( $settings['visual_image']['url'] ); ?>" alt="<?php echo esc_attr( ! empty( $settings['visual_image']['alt'] ) ? $settings['visual_image']['alt'] : get_bloginfo( 'name' ) ); ?>" loading="eager">
						</div>

						<?php if ( ! empty( $settings['floats'] ) ) : ?>
							<?php
							$positions = array( 'nv-float--1', 'nv-float--2', 'nv-float--3', 'nv-float--4' );
							foreach ( $settings['floats'] as $index => $float ) :
								$pos = isset( $positions[ $index ] ) ? $positions[ $index ] : 'nv-float--1';
								?>
								<div class="nv-float-card nv-glass <?php echo esc_attr( $pos ); ?>">
									<?php if ( ! empty( $float['icon']['value'] ) ) : ?>
										<span class="nv-float-card__icon"><?php $this->nv_icon( $float['icon'] ); ?></span>
									<?php endif; ?>

									<span class="nv-float-card__title"><?php echo esc_html( $float['title'] ); ?></span>
									<span class="nv-float-card__value"><?php echo esc_html( $float['value'] ); ?></span>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( 'yes' === $settings['show_marquee'] && ! empty( $settings['marquee_items'] ) ) : ?>
				<?php $items = array_filter( array_map( 'trim', explode( ',', (string) $settings['marquee_items'] ) ) ); ?>
				<div class="nv-marquee">
					<div <?php $this->nv_attr( 'marquee' ); ?>>
						<?php for ( $i = 0; $i < 2; $i++ ) : ?>
							<?php foreach ( $items as $item ) : ?>
								<span class="nv-marquee__item">
									<span class="nv-marquee__dot"></span>
									<?php echo esc_html( $item ); ?>
								</span>
							<?php endforeach; ?>
						<?php endfor; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['show_scroll_hint'] ) : ?>
				<div class="nv-scroll-hint" aria-hidden="true"><span></span></div>
			<?php endif; ?>
		</section>
		<?php
	}
}
