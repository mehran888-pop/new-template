<?php
/**
 * المان اختصاصی محصولات (ووکامرس).
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
 * کلاس محصولات.
 */
class Products extends Novin_AI_Widget_Base {

	/**
	 * نام المان.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'novin-products';
	}

	/**
	 * عنوان المان.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'محصولات (ووکامرس)', 'novin-ai' );
	}

	/**
	 * آیکون المان.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-products';
	}

	/**
	 * کلمات کلیدی.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords() {
		return array( 'products', 'محصولات', 'فروشگاه', 'woocommerce', 'لایسنس' );
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
				'label' => esc_html__( 'کوئری محصولات', 'novin-ai' ),
			)
		);

		$this->add_control(
			'query_type',
			array(
				'label'   => esc_html__( 'نوع نمایش', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'recent',
				'options' => array(
					'recent'       => esc_html__( 'جدیدترین‌ها', 'novin-ai' ),
					'featured'     => esc_html__( 'ویژه‌ها', 'novin-ai' ),
					'sale'         => esc_html__( 'حراج‌دارها', 'novin-ai' ),
					'best_selling' => esc_html__( 'پرفروش‌ترین‌ها', 'novin-ai' ),
					'top_rated'    => esc_html__( 'برترین امتیازها', 'novin-ai' ),
				),
			)
		);

		$this->add_control(
			'product_categories',
			array(
				'label'       => esc_html__( 'دسته‌بندی محصولات', 'novin-ai' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => novin_ai_get_terms_choices( 'product_cat' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'تعداد محصول', 'novin-ai' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 1,
				'max'     => 36,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => esc_html__( 'مرتب‌سازی', 'novin-ai' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => array(
					'date'  => esc_html__( 'تاریخ', 'novin-ai' ),
					'price' => esc_html__( 'قیمت', 'novin-ai' ),
					'title' => esc_html__( 'عنوان', 'novin-ai' ),
					'rand'  => esc_html__( 'تصادفی', 'novin-ai' ),
				),
				'condition' => array(
					'query_type' => array( 'recent', 'sale' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'ترتیب', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => esc_html__( 'نزولی', 'novin-ai' ),
					'ASC'  => esc_html__( 'صعودی', 'novin-ai' ),
				),
			)
		);

		$this->add_control(
			'exclude_ids',
			array(
				'label'       => esc_html__( 'حذف محصولات (آیدی با کاما)', 'novin-ai' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '12,15',
			)
		);

		$this->end_controls_section();

		$this->section_title_controls(
			array(
				'title_default' => esc_html__( 'محصولات و لایسنس‌های نرم‌افزاری', 'novin-ai' ),
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
				'default'   => '1/1',
				'options'   => array(
					'1/1'  => '1:1',
					'4/3'  => '4:3',
					'3/4'  => '3:4',
					'16/9' => '16:9',
				),
				'selectors' => array(
					'{{WRAPPER}} .nv-product__media' => 'aspect-ratio: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'show_price',
			array(
				'label'        => esc_html__( 'نمایش قیمت', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
			'show_cart_button',
			array(
				'label'        => esc_html__( 'دکمه افزودن به سبد', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'cart_text',
			array(
				'label'     => esc_html__( 'متن دکمه', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'افزودن به سبد', 'novin-ai' ),
				'condition' => array( 'show_cart_button' => 'yes' ),
			)
		);

		$this->add_control(
			'show_sale_badge',
			array(
				'label'        => esc_html__( 'برچسب تخفیف', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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

		$this->card_style_controls( '{{WRAPPER}} .nv-product' );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'product_title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-product__title',
			)
		);

		$this->add_control(
			'product_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-product__title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_typography',
				'label'    => esc_html__( 'تایپوگرافی قیمت', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-product__price',
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => esc_html__( 'رنگ قیمت', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-product__price' => 'color: {{VALUE}};',
					'{{WRAPPER}} .nv-product__price bdi' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'sale_border',
				'label'    => esc_html__( 'حاشیه برچسب تخفیف', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-product__sale',
			)
		);

		$this->add_control(
			'sale_color',
			array(
				'label'     => esc_html__( 'رنگ برچسب تخفیف', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-product__sale' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->section_title_style_controls();
	}

	/**
	 * ساخت آرگومان‌های کوئری محصولات.
	 *
	 * @param array<string, mixed> $settings تنظیمات.
	 * @return array<string, mixed>
	 */
	protected function product_query_args( $settings ) {
		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => ! empty( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 6,
			'order'               => ! empty( $settings['order'] ) ? $settings['order'] : 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		switch ( $settings['query_type'] ) {
			case 'featured':
				$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
					array(
						'taxonomy' => 'product_visibility',
						'field'    => 'name',
						'terms'    => 'featured',
					),
				);
				$args['orderby']   = 'date';
				break;

			case 'sale':
				$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
				$args['orderby']  = ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date';

				if ( 'price' === $args['orderby'] ) {
					$args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery
					$args['orderby']  = 'meta_value_num';
				}
				break;

			case 'best_selling':
				$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery
				$args['orderby']  = 'meta_value_num';
				break;

			case 'top_rated':
				$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery
				$args['orderby']  = 'meta_value_num';
				break;

			default:
				$args['orderby'] = ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date';

				if ( 'price' === $args['orderby'] ) {
					$args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery
					$args['orderby']  = 'meta_value_num';
				}
				break;
		}

		if ( ! empty( $settings['product_categories'] ) && is_array( $settings['product_categories'] ) ) {
			$args['tax_query'] = isset( $args['tax_query'] ) ? $args['tax_query'] : array( 'relation' => 'AND' ); // phpcs:ignore WordPress.DB.SlowDBQuery
			$args['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => array_map( 'intval', $settings['product_categories'] ),
			);
		}

		if ( ! empty( $settings['exclude_ids'] ) ) {
			$ids                 = array_map( 'intval', array_filter( array_map( 'trim', explode( ',', (string) $settings['exclude_ids'] ) ) ) );
			$args['post__not_in'] = $ids;
		}

		return apply_filters( 'novin_ai_products_query_args', $args, $settings );
	}

	/**
	 * رندر خروجی.
	 *
	 * @return void
	 */
	protected function render() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<p class="nv-widget__empty">' . esc_html__( 'برای نمایش محصولات، افزونه ووکامرس را نصب و فعال کنید.', 'novin-ai' ) . '</p>';
			return;
		}

		$settings = $this->get_settings_for_display();
		$query    = new \WP_Query( $this->product_query_args( $settings ) );

		$this->add_render_attribute( 'wrapper', 'class', 'nv-widget nv-section nv-widget-products' );
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

							$product = wc_get_product( get_the_ID() );

							if ( ! $product ) {
								continue;
							}

							$classes = array( 'nv-card', 'nv-product', $this->nv_classes( $settings, array() ) );
							$image   = get_the_post_thumbnail_url( get_the_ID(), 'novin-ai-square' );
							?>
							<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
								<div class="nv-product__media nv-media">
									<a href="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
										<?php if ( $image ) : ?>
											<img class="nv-media__img" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
										<?php endif; ?>
									</a>

									<?php if ( 'yes' === $settings['show_sale_badge'] && $product->is_on_sale() ) : ?>
										<span class="nv-product__sale nv-badge nv-badge--sale">
											<?php
											if ( 'variable' === $product->get_type() ) {
												esc_html_e( 'حراج', 'novin-ai' );
											} else {
												$regular = (float) $product->get_regular_price();
												$sale    = (float) $product->get_sale_price();

												if ( $regular > 0 && $sale > 0 ) {
													/* translators: %d: درصد تخفیف. */
													printf( esc_html__( '٪%d تخفیف', 'novin-ai' ), (int) round( ( ( $regular - $sale ) / $regular ) * 100 ) );
												} else {
													esc_html_e( 'حراج', 'novin-ai' );
												}
											}
											?>
										</span>
									<?php endif; ?>
								</div>

								<div class="nv-product__body">
									<h3 class="nv-product__title">
										<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
									</h3>

									<?php if ( 'yes' === $settings['show_rating'] && $product->get_average_rating() > 0 ) : ?>
										<div class="nv-product__rating" aria-label="<?php echo esc_attr( sprintf( '%s از ۵', (string) $product->get_average_rating() ) ); ?>">
											<?php
											$rating = (int) round( $product->get_average_rating() );

											for ( $i = 1; $i <= 5; $i++ ) {
												echo '<span class="' . esc_attr( $i <= $rating ? 'is-filled' : '' ) . '">' . novin_ai_icon( 'star' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
											}
											?>
										</div>
									<?php endif; ?>

									<?php if ( 'yes' === $settings['show_price'] ) : ?>
										<div class="nv-product__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
									<?php endif; ?>

									<?php if ( 'yes' === $settings['show_cart_button'] ) : ?>
										<a class="nv-btn nv-btn--primary nv-btn--sm" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-nv-add-to-cart="<?php echo esc_attr( $product->get_id() ); ?>">
											<span><?php echo esc_html( $settings['cart_text'] ); ?></span>
											<?php echo novin_ai_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
										</a>
									<?php endif; ?>
								</div>
							</article>
							<?php
						}

						wp_reset_postdata();
					} else {
						echo '<p class="nv-widget__empty">' . esc_html__( 'محصولی برای نمایش وجود ندارد.', 'novin-ai' ) . '</p>';
					}
					?>
				</div>
			</div>
		</section>
		<?php
	}
}
