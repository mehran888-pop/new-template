<?php
/**
 * المان نظرات مشتریان.
 *
 * @package Novin_AI
 */

namespace Novin_AI\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

/**
 * کلاس نظرات مشتریان.
 */
class Testimonials extends Novin_AI_Widget_Base {

	/**
	 * نام المان.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'novin-testimonials';
	}

	/**
	 * عنوان المان.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'نظرات مشتریان', 'novin-ai' );
	}

	/**
	 * آیکون المان.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-testimonial';
	}

	/**
	 * کلمات کلیدی.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords() {
		return array( 'testimonial', 'نظرات', 'مشتری', 'تعریف', 'review' );
	}

	/**
	 * ثبت کنترل‌ها.
	 *
	 * @return void
	 */
	protected function register_controls() {

		/* ---------------------------------- منبع ---------------------------------- */
		$this->start_controls_section(
			'section_source',
			array(
				'label' => esc_html__( 'منبع محتوا', 'novin-ai' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'منبع', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => array(
					'manual' => esc_html__( 'ورود دستی', 'novin-ai' ),
					'cpt'    => esc_html__( 'نوع نوشته «نظرات مشتریان»', 'novin-ai' ),
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			array(
				'label' => esc_html__( 'تصویر', 'novin-ai' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'   => esc_html__( 'نام', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مریم حسینی', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'role',
			array(
				'label'   => esc_html__( 'سمت / شرکت', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مدیر فناوری، داده‌پردازان نوین', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'content',
			array(
				'label' => esc_html__( 'متن نظر', 'novin-ai' ),
				'type'  => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'تیم نوین در کمتر از سه ماه مدل توصیه‌گر ما را بازطراحی کرد؛ دقت پیش‌بینی ۳۴٪ بهتر شد.', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'rating',
			array(
				'label'   => esc_html__( 'امتیاز', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '5',
				'options' => array(
					'0' => esc_html__( 'بدون امتیاز', 'novin-ai' ),
					'1' => '۱',
					'2' => '۲',
					'3' => '۳',
					'4' => '۴',
					'5' => '۵',
				),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'نظرات', 'novin-ai' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'name'    => esc_html__( 'مریم حسینی', 'novin-ai' ),
						'role'    => esc_html__( 'مدیر فناوری', 'novin-ai' ),
						'content' => esc_html__( 'تیم نوین در کمتر از سه ماه مدل توصیه‌گر ما را بازطراحی کرد و دقت پیش‌بینی ۳۴٪ بهتر شد.', 'novin-ai' ),
					),
					array(
						'name'    => esc_html__( 'رضا یوسفی', 'novin-ai' ),
						'role'    => esc_html__( 'مدیرعامل', 'novin-ai' ),
						'content' => esc_html__( 'پشتیبانی ۲۴ ساعته و پاسخگویی سریع، مهم‌ترین دلیل ادامه همکاری ما با این تیم است.', 'novin-ai' ),
					),
					array(
						'name'    => esc_html__( 'زهرا کریمی', 'novin-ai' ),
						'role'    => esc_html__( 'مدیر محصول', 'novin-ai' ),
						'content' => esc_html__( 'طراحی وب‌سایت جدید باعث شد نرخ تبدیل فروشگاه ما بیش از دو برابر شود.', 'novin-ai' ),
					),
				),
				'title_field' => '{{{ name }}}',
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'     => esc_html__( 'تعداد نظر', 'novin-ai' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 6,
				'min'       => 1,
				'max'       => 20,
				'condition' => array( 'source' => 'cpt' ),
			)
		);

		$this->end_controls_section();

		$this->section_title_controls(
			array(
				'title_default' => esc_html__( 'اعتماد مشتریان، سرمایه ماست', 'novin-ai' ),
			)
		);

		/* ---------------------------------- چیدمان ---------------------------------- */
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
				'default' => 'slider',
				'options' => array(
					'slider' => esc_html__( 'اسلایدر', 'novin-ai' ),
					'grid'   => esc_html__( 'گرید', 'novin-ai' ),
				),
			)
		);

		$this->add_control(
			'card_style',
			array(
				'label'   => esc_html__( 'استایل کارت', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'glass',
				'options' => array(
					'glass'   => esc_html__( 'شیشه‌ای', 'novin-ai' ),
					'solid'   => esc_html__( 'ساده', 'novin-ai' ),
					'outline' => esc_html__( 'خطی', 'novin-ai' ),
					'neon'    => esc_html__( 'نئونی', 'novin-ai' ),
				),
			)
		);

		$this->layout_controls( array( 'default' => 3 ) );

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'پخش خودکار', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'layout' => 'slider' ),
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'      => esc_html__( 'زمان توقف (ثانیه)', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array(
					's' => array(
						'min' => 2,
						'max' => 20,
					),
				),
				'default'    => array( 'size' => 6, 'unit' => 's' ),
				'condition'  => array(
					'layout'   => 'slider',
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'        => esc_html__( 'فلش‌های ناوبری', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'layout' => 'slider' ),
			)
		);

		$this->add_control(
			'show_dots',
			array(
				'label'        => esc_html__( 'نقاط ناوبری', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'layout' => 'slider' ),
			)
		);

		$this->add_control(
			'show_rating',
			array(
				'label'        => esc_html__( 'نمایش امتیاز', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_quote',
			array(
				'label'        => esc_html__( 'آیکون نقل‌قول', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

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
			'section_card_style',
			array(
				'label' => esc_html__( 'استایل', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->card_style_controls( '{{WRAPPER}} .nv-testimonial' );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'testimonial_typography',
				'label'    => esc_html__( 'تایپوگرافی متن نظر', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-testimonial__content',
			)
		);

		$this->add_control(
			'testimonial_color',
			array(
				'label'     => esc_html__( 'رنگ متن نظر', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-testimonial__content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'label'    => esc_html__( 'تایپوگرافی نام', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-testimonial__name',
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => esc_html__( 'رنگ نام', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-testimonial__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'role_typography',
				'label'    => esc_html__( 'تایپوگرافی سمت', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-testimonial__role',
			)
		);

		$this->add_control(
			'role_color',
			array(
				'label'     => esc_html__( 'رنگ سمت', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-testimonial__role' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'rating_color',
			array(
				'label'     => esc_html__( 'رنگ ستاره‌ها', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffc44d',
				'selectors' => array(
					'{{WRAPPER}} .nv-testimonial__rating span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'quote_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون نقل‌قول', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-testimonial__quote' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'avatar_border',
				'label'    => esc_html__( 'حاشیه تصویر', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-testimonial__avatar img',
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

		$this->add_render_attribute( 'wrapper', 'class', 'nv-widget nv-section nv-widget-testimonials' );

		$testimonials = array();

		if ( 'cpt' === $settings['source'] ) {
			$settings['post_type'] = 'novin_testimonial';
			$query                 = $this->nv_query( $settings, 'novin_testimonial' );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					$post_id = get_the_ID();

					$testimonials[] = array(
						'name'    => get_the_title(),
						'role'    => novin_ai_get_meta( $post_id, 'role' ),
						'content' => get_the_content(),
						'image'   => get_the_post_thumbnail_url( $post_id, 'thumbnail' ),
						'rating'  => (string) novin_ai_get_meta( $post_id, 'rating', 5 ),
					);
				}

				wp_reset_postdata();
			}
		} elseif ( ! empty( $settings['items'] ) ) {
			foreach ( $settings['items'] as $item ) {
				$testimonials[] = array(
					'name'    => $item['name'],
					'role'    => $item['role'],
					'content' => $item['content'],
					'image'   => ! empty( $item['image']['url'] ) ? $item['image']['url'] : '',
					'rating'  => (string) $item['rating'],
				);
			}
		}

		$slider_data = array(
			'autoplay' => ( 'slider' === $settings['layout'] && 'yes' === $settings['autoplay'] ) ? 1 : 0,
			'speed'    => ! empty( $settings['autoplay_speed']['size'] ) ? (int) $settings['autoplay_speed']['size'] * 1000 : 6000,
		);
		?>
		<section <?php $this->nv_attr( 'wrapper' ); ?>>
			<?php $this->nv_backdrop( $settings ); ?>

			<div class="nv-container">
				<?php $this->render_heading( $settings ); ?>

				<?php if ( 'slider' === $settings['layout'] ) : ?>
					<?php
					$cols = ! empty( $settings['columns'] ) ? (int) $settings['columns'] : 3;

					$this->add_render_attribute( 'slider', 'class', 'nv-slider' );
					$this->add_render_attribute( 'slider', 'style', '--nv-slides:' . max( 1, $cols ) );
					?>
					<div <?php $this->nv_attr( 'slider' ); ?> data-nv-slider="<?php echo esc_attr( wp_json_encode( $slider_data ) ); ?>">
						<div class="nv-slider__track">
							<?php foreach ( $testimonials as $item ) : ?>
								<div class="nv-slide">
									<?php $this->render_testimonial( $item, $settings ); ?>
								</div>
							<?php endforeach; ?>
						</div>

						<?php if ( 'yes' === $settings['show_arrows'] ) : ?>
							<button class="nv-slider__arrow nv-slider__arrow--prev" type="button" data-nv-slide-prev aria-label="<?php esc_attr_e( 'قبلی', 'novin-ai' ); ?>">
								<?php echo novin_ai_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</button>
							<button class="nv-slider__arrow nv-slider__arrow--next" type="button" data-nv-slide-next aria-label="<?php esc_attr_e( 'بعدی', 'novin-ai' ); ?>">
								<?php echo novin_ai_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</button>
						<?php endif; ?>

						<?php if ( 'yes' === $settings['show_dots'] ) : ?>
							<div class="nv-slider__dots" data-nv-slide-dots></div>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<div class="nv-grid">
						<?php foreach ( $testimonials as $item ) : ?>
							<?php $this->render_testimonial( $item, $settings ); ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( empty( $testimonials ) ) : ?>
					<p class="nv-widget__empty"><?php esc_html_e( 'نظری ثبت نشده است.', 'novin-ai' ); ?></p>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	/**
	 * رندر یک کارت نظر.
	 *
	 * @param array<string, mixed> $item     داده‌ها.
	 * @param array<string, mixed> $settings تنظیمات.
	 * @return void
	 */
	protected function render_testimonial( $item, $settings ) {
		$classes = array( 'nv-card', 'nv-testimonial', $this->nv_classes( $settings, array() ) );
		$rating  = (int) $item['rating'];
		?>
		<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php if ( 'yes' === $settings['show_quote'] ) : ?>
				<span class="nv-testimonial__quote" aria-hidden="true"><?php echo novin_ai_icon( 'quote' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['show_rating'] && $rating > 0 ) : ?>
				<div class="nv-testimonial__rating" aria-label="<?php echo esc_attr( sprintf( '%d از ۵', $rating ) ); ?>">
					<?php
					for ( $i = 1; $i <= 5; $i++ ) {
						echo '<span class="' . esc_attr( $i <= $rating ? 'is-filled' : '' ) . '">' . novin_ai_icon( 'star' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
					}
					?>
				</div>
			<?php endif; ?>

			<div class="nv-testimonial__content"><?php echo wp_kses_post( wpautop( $item['content'] ) ); ?></div>

			<div class="nv-testimonial__author">
				<?php if ( ! empty( $item['image'] ) ) : ?>
					<span class="nv-testimonial__avatar">
						<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['name'] ); ?>" loading="lazy">
					</span>
				<?php endif; ?>

				<span class="nv-testimonial__info">
					<span class="nv-testimonial__name"><?php echo esc_html( $item['name'] ); ?></span>

					<?php if ( ! empty( $item['role'] ) ) : ?>
						<span class="nv-testimonial__role"><?php echo esc_html( $item['role'] ); ?></span>
					<?php endif; ?>
				</span>
			</div>
		</article>
		<?php
	}
}
