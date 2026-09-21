<?php
/**
 * المان سوالات متداول (آکاردئون).
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
 * کلاس سوالات متداول.
 */
class FAQ extends Novin_AI_Widget_Base {

	/**
	 * نام المان.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'novin-faq';
	}

	/**
	 * عنوان المان.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'سوالات متداول', 'novin-ai' );
	}

	/**
	 * آیکون المان.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-accordion';
	}

	/**
	 * کلمات کلیدی.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords() {
		return array( 'faq', 'سوالات', 'متداول', 'accordion', 'آکاردئون' );
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
				'label' => esc_html__( 'سوالات', 'novin-ai' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'question',
			array(
				'label'   => esc_html__( 'سوال', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'زمان تحویل پروژه چقدر است؟', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'answer',
			array(
				'label'   => esc_html__( 'پاسخ', 'novin-ai' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'بسته به scope پروژه، بین ۴ تا ۱۲ هفته. پس از جلسه کشف نیازها زمان‌بندی دقیق اعلام می‌شود.', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'open',
			array(
				'label'        => esc_html__( 'باز به صورت پیش‌فرض', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => '',
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
						'question' => esc_html__( 'زمان تحویل پروژه چقدر است؟', 'novin-ai' ),
						'answer'   => esc_html__( 'بسته به محدوده پروژه، بین ۴ تا ۱۲ هفته. پس از جلسه کشف نیازها زمان‌بندی دقیق اعلام می‌شود.', 'novin-ai' ),
						'open'     => 'yes',
					),
					array(
						'question' => esc_html__( 'آیا خدمات پشتیبانی پس از تحویل هم دارید؟', 'novin-ai' ),
						'answer'   => esc_html__( 'بله. پکیج‌های پشتیبانی شامل نگهداری، بروزرسانی امنیتی و پاسخگویی بر اساس SLA است.', 'novin-ai' ),
					),
					array(
						'question' => esc_html__( 'هزینه پروژه چگونه محاسبه می‌شود؟', 'novin-ai' ),
						'answer'   => esc_html__( 'پس از تحلیل نیازها، برآورد بر اساس زمان‌بندی، تخصص موردنیاز و زیرساخت اعلام می‌شود.', 'novin-ai' ),
					),
					array(
						'question' => esc_html__( 'آیا امکان خرید لایسنس نرم‌افزار هم وجود دارد؟', 'novin-ai' ),
						'answer'   => esc_html__( 'بله. لایسنس محصولات از طریق فروشگاه آنلاین قابل خریداری و تمدید است.', 'novin-ai' ),
					),
				),
				'title_field' => '{{{ question }}}',
			)
		);

		$this->add_control(
			'allow_multiple',
			array(
				'label'        => esc_html__( 'باز ماندن چند پاسخ همزمان', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		$this->section_title_controls(
			array(
				'title_default' => esc_html__( 'سوالات پرتکرار مشتریان', 'novin-ai' ),
			)
		);

		$this->backdrop_controls();

		/* ---------------------------------- استایل ---------------------------------- */
		$this->start_controls_section(
			'section_faq_style',
			array(
				'label' => esc_html__( 'استایل', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'item_background',
				'label'    => esc_html__( 'پس‌زمینه آیتم', 'novin-ai' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .nv-accordion__item',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'label'    => esc_html__( 'حاشیه آیتم', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-accordion__item',
			)
		);

		$this->add_responsive_control(
			'item_radius',
			array(
				'label'      => esc_html__( 'انحنا', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-accordion__item' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => esc_html__( 'فاصله بین آیتم‌ها', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default'    => array( 'size' => 14 ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-accordion' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'question_typography',
				'label'    => esc_html__( 'تایپوگرافی سوال', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-accordion__header',
			)
		);

		$this->add_control(
			'question_color',
			array(
				'label'     => esc_html__( 'رنگ سوال', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-accordion__header' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'question_active_color',
			array(
				'label'     => esc_html__( 'رنگ سوال فعال', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-accordion__item.is-open .nv-accordion__header' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'answer_typography',
				'label'    => esc_html__( 'تایپوگرافی پاسخ', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-accordion__content',
			)
		);

		$this->add_control(
			'answer_color',
			array(
				'label'     => esc_html__( 'رنگ پاسخ', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-accordion__content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-accordion__icon' => 'color: {{VALUE}};',
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

		$this->add_render_attribute( 'wrapper', 'class', 'nv-widget nv-section nv-widget-faq' );
		$this->add_render_attribute( 'accordion', 'class', 'nv-accordion' );
		$this->add_render_attribute( 'accordion', 'data-nv-accordion', '1' );
		$this->add_render_attribute( 'accordion', 'data-nv-multiple', 'yes' === $settings['allow_multiple'] ? '1' : '0' );
		?>
		<section <?php $this->nv_attr( 'wrapper' ); ?>>
			<?php $this->nv_backdrop( $settings ); ?>

			<div class="nv-container">
				<?php $this->render_heading( $settings ); ?>

				<?php if ( ! empty( $settings['items'] ) ) : ?>
					<div <?php $this->nv_attr( 'accordion' ); ?>>
						<?php foreach ( $settings['items'] as $item ) : ?>
							<div class="nv-accordion__item<?php echo 'yes' === $item['open'] ? ' is-open' : ''; ?>">
								<button class="nv-accordion__header" type="button" aria-expanded="<?php echo 'yes' === $item['open'] ? 'true' : 'false'; ?>">
									<span class="nv-accordion__title"><?php echo esc_html( $item['question'] ); ?></span>
									<span class="nv-accordion__icon" aria-hidden="true"></span>
								</button>

								<div class="nv-accordion__panel">
									<div class="nv-accordion__content"><?php echo wp_kses_post( wpautop( $item['answer'] ) ); ?></div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="nv-widget__empty"><?php esc_html_e( 'سوالی تعریف نشده است.', 'novin-ai' ); ?></p>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
