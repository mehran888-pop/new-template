<?php
/**
 * Widget: Neo Posts (مقالات / نمایش مقالات).
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Posts
 */
class Widget_Posts extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-posts';
	}

	public function get_title() {
		return esc_html__( 'نمایش مقالات', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان بخش', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'آخرین مقالات', 'neomorph' ),
		) );

		$this->add_control( 'layout', array(
			'label'   => esc_html__( 'چیدمان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'cards',
			'options' => array(
				'cards'    => esc_html__( 'کارت‌ها', 'neomorph' ),
				'list'     => esc_html__( 'لیست', 'neomorph' ),
				'featured' => esc_html__( 'یک برجسته + کناری', 'neomorph' ),
				'masonry'  => esc_html__( 'ماسونری نرم', 'neomorph' ),
			),
		) );

		$this->add_control( 'count', array(
			'label'   => esc_html__( 'تعداد', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 3,
		) );
		$this->add_control( 'columns', array(
			'label'   => esc_html__( 'ستون‌ها', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '3',
			'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
		) );
		$this->add_control( 'category', array(
			'label'       => esc_html__( 'دسته (اختیاری)', 'neomorph' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'description' => esc_html__( 'نامک دسته‌بندی — مثلاً blog', 'neomorph' ),
		) );
		$this->add_control( 'show_excerpt', array(
			'label'   => esc_html__( 'نمایش خلاصه', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'show_meta', array(
			'label'   => esc_html__( 'نمایش تاریخ و دسته', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'btn_text', array(
			'label'   => esc_html__( 'متن دکمه هر کارت', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'ادامه مطلب', 'neomorph' ),
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'card_style', array(
			'label' => esc_html__( 'کارت', 'neomorph' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'title_color', array(
			'label'     => esc_html__( 'رنگ عنوان', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .neo-post-card__title a' => 'color: {{VALUE}};' ),
		) );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'title_typo',
			'selector' => '{{WRAPPER}} .neo-post-card__title',
		) );
		$this->add_control( 'media_radius', array(
			'label'     => esc_html__( 'گردی تصویر', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::SLIDER,
			'range'     => array( 'px' => array( 'min' => 0, 'max' => 48 ) ),
			'selectors' => array( '{{WRAPPER}} .neo-post-card__thumb' => 'border-radius: {{SIZE}}px;' ),
		) );
		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل کلی', 'neomorph' ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$args = array(
			'posts_per_page'      => max( 1, (int) $s['count'] ),
			'ignore_sticky_posts' => true,
		);
		if ( $s['category'] ) {
			$args['category_name'] = sanitize_title( $s['category'] );
		}
		$query = new \WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return;
		}
		?>
		<section class="neo-widget neo-posts neo-posts--<?php echo esc_attr( $s['layout'] ); ?> <?php echo esc_attr( $this->neo_widget_class() ); ?>">
			<?php if ( $s['title'] ) : ?>
				<h2 class="neo-section__title"><?php echo esc_html( $s['title'] ); ?></h2>
			<?php endif; ?>
			<div class="neo-grid neo-grid--<?php echo esc_attr( $s['columns'] ); ?>">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					?>
					<article <?php post_class( 'neo-card neo-post-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="neo-post-card__thumb neo-media" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'neomorph-card' ); ?></a>
						<?php endif; ?>
						<div class="neo-post-card__body">
							<?php if ( 'yes' === $s['show_meta'] ) : ?>
								<div class="neo-meta">
									<span class="neo-meta__item"><?php echo esc_html( get_the_date() ); ?></span>
									<?php $cats = get_the_category(); if ( $cats ) : ?><span class="neo-meta__item"><?php echo esc_html( $cats[0]->name ); ?></span><?php endif; ?>
								</div>
							<?php endif; ?>
							<h3 class="neo-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<?php if ( 'yes' === $s['show_excerpt'] ) : ?>
								<p class="neo-post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), (int) neomorph_option( 'blog_excerpt_length', 22 ) ) ); ?></p>
							<?php endif; ?>
							<a class="neo-btn neo-btn--ghost neo-btn--sm" href="<?php the_permalink(); ?>"><?php echo esc_html( $s['btn_text'] ); ?></a>
						</div>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
		<?php
	}
}
