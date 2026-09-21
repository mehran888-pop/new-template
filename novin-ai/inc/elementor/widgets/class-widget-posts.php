<?php
/**
 * المان اختصاصی مقالات و اخبار.
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

/**
 * کلاس مقالات.
 */
class Posts extends Novin_AI_Widget_Base {

	/**
	 * نام المان.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'novin-posts';
	}

	/**
	 * عنوان المان.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'مقالات و اخبار', 'novin-ai' );
	}

	/**
	 * آیکون المان.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-post-list';
	}

	/**
	 * کلمات کلیدی.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords() {
		return array( 'posts', 'blog', 'مقالات', 'وبلاگ', 'اخبار', 'نوشته' );
	}

	/**
	 * ثبت کنترل‌ها.
	 *
	 * @return void
	 */
	protected function register_controls() {

		/* ---------------------------------- کوئری ---------------------------------- */
		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'منبع و کوئری', 'novin-ai' ),
			)
		);

		$this->query_controls( array( 'post', 'novin_project', 'novin_service' ) );

		$this->add_control(
			'only_with_thumb',
			array(
				'label'        => esc_html__( 'فقط مطالب دارای تصویر', 'novin-ai' ),
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
				'title_default' => esc_html__( 'آخرین مقالات و اخبار فناوری', 'novin-ai' ),
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
				'default' => 'grid',
				'options' => array(
					'grid'    => esc_html__( 'شبکه‌ای', 'novin-ai' ),
					'list'    => esc_html__( 'لیستی', 'novin-ai' ),
					'minimal' => esc_html__( 'مینیمال', 'novin-ai' ),
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
			'image_ratio',
			array(
				'label'     => esc_html__( 'نسبت تصویر', 'novin-ai' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '16/9',
				'options'   => array(
					'1/1'  => '1:1',
					'16/9' => '16:9',
					'4/3'  => '4:3',
					'3/2'  => '3:2',
				),
				'selectors' => array(
					'{{WRAPPER}} .nv-post__media' => 'aspect-ratio: {{VALUE}};',
				),
				'condition' => array( 'layout!' => 'minimal' ),
			)
		);

		$this->add_control(
			'show_date',
			array(
				'label'        => esc_html__( 'نمایش تاریخ', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_category',
			array(
				'label'        => esc_html__( 'نمایش دسته‌بندی', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_author',
			array(
				'label'        => esc_html__( 'نمایش نویسنده', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_reading_time',
			array(
				'label'        => esc_html__( 'نمایش زمان مطالعه', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_excerpt',
			array(
				'label'        => esc_html__( 'نمایش خلاصه', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label'     => esc_html__( 'تعداد کلمات خلاصه', 'novin-ai' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 20,
				'min'       => 5,
				'max'       => 60,
				'condition' => array( 'show_excerpt' => 'yes' ),
			)
		);

		$this->add_control(
			'show_more',
			array(
				'label'        => esc_html__( 'نمایش دکمه ادامه مطلب', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'more_text',
			array(
				'label'     => esc_html__( 'متن دکمه', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'ادامه مطلب', 'novin-ai' ),
				'condition' => array( 'show_more' => 'yes' ),
			)
		);

		$this->add_control(
			'enable_tilt',
			array(
				'label'        => esc_html__( 'چرخش سه‌بعدی', 'novin-ai' ),
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
				'label' => esc_html__( 'استایل کارت', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->card_style_controls( '{{WRAPPER}} .nv-post' );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'post_title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-post__title',
			)
		);

		$this->add_control(
			'post_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-post__title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'post_title_hover',
			array(
				'label'     => esc_html__( 'رنگ عنوان در هاور', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-post__title a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'post_excerpt_typography',
				'label'    => esc_html__( 'تایپوگرافی خلاصه', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-post__excerpt',
			)
		);

		$this->add_control(
			'post_excerpt_color',
			array(
				'label'     => esc_html__( 'رنگ خلاصه', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-post__excerpt' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'post_meta_typography',
				'label'    => esc_html__( 'تایپوگرافی متادیتا', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-post__meta',
			)
		);

		$this->add_control(
			'post_meta_color',
			array(
				'label'     => esc_html__( 'رنگ متادیتا', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-post__meta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'badge_border',
				'label'    => esc_html__( 'حاشیه برچسب دسته', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-post__badge',
			)
		);

		$this->add_control(
			'badge_color',
			array(
				'label'     => esc_html__( 'رنگ برچسب دسته', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-post__badge' => 'color: {{VALUE}};',
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
		$query    = $this->nv_query( $settings, 'post' );

		$this->add_render_attribute(
			'wrapper',
			'class',
			'nv-widget nv-section nv-widget-posts nv-posts nv-posts--' . sanitize_html_class( $settings['layout'] )
		);
		?>
		<section <?php $this->nv_attr( 'wrapper' ); ?>>
			<?php $this->nv_backdrop( $settings ); ?>

			<div class="nv-container">
				<?php $this->render_heading( $settings ); ?>

				<div class="nv-grid">
					<?php
					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();

							$post_id    = get_the_ID();
							$categories = get_the_category();
							$classes    = array( 'nv-card', 'nv-post', $this->nv_classes( $settings, array() ) );
							$thumb      = get_the_post_thumbnail_url( $post_id, 'novin-ai-card' );
							?>
							<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
								<?php if ( 'minimal' !== $settings['layout'] ) : ?>
									<div class="nv-post__media nv-media">
										<a href="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
											<?php if ( $thumb ) : ?>
												<img class="nv-media__img" src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
											<?php else : ?>
												<span class="nv-media__placeholder" aria-hidden="true"><?php echo novin_ai_icon( 'spark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
											<?php endif; ?>
										</a>

										<?php if ( 'yes' === $settings['show_category'] && ! empty( $categories ) ) : ?>
											<a class="nv-post__badge nv-badge" href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>">
												<?php echo esc_html( $categories[0]->name ); ?>
											</a>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<div class="nv-post__body">
									<div class="nv-post__meta">
										<?php if ( 'yes' === $settings['show_date'] ) : ?>
											<span class="nv-post__date"><?php echo esc_html( get_the_date() ); ?></span>
										<?php endif; ?>

										<?php if ( 'yes' === $settings['show_author'] ) : ?>
											<span class="nv-post__author"><?php echo esc_html( get_the_author() ); ?></span>
										<?php endif; ?>

										<?php if ( 'yes' === $settings['show_reading_time'] ) : ?>
											<span class="nv-post__read"><?php echo esc_html( novin_ai_reading_time() . ' ' . esc_html__( 'دقیقه', 'novin-ai' ) ); ?></span>
										<?php endif; ?>
									</div>

									<h3 class="nv-post__title">
										<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
									</h3>

									<?php if ( 'yes' === $settings['show_excerpt'] ) : ?>
										<p class="nv-post__excerpt"><?php echo esc_html( novin_ai_excerpt( get_the_excerpt(), (int) $settings['excerpt_length'] ) ); ?></p>
									<?php endif; ?>

									<?php if ( 'yes' === $settings['show_more'] ) : ?>
										<a class="nv-link-more" href="<?php echo esc_url( get_permalink() ); ?>">
											<span><?php echo esc_html( $settings['more_text'] ); ?></span>
											<?php echo novin_ai_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
										</a>
									<?php endif; ?>
								</div>
							</article>
							<?php
						}

						wp_reset_postdata();
					} else {
						echo '<p class="nv-widget__empty">' . esc_html__( 'مقاله‌ای برای نمایش وجود ندارد.', 'novin-ai' ) . '</p>';
					}
					?>
				</div>
			</div>
		</section>
		<?php
	}
}
