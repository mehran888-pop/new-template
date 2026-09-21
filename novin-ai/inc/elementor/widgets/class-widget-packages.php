<?php
/**
 * المان اختصاصی پکیج‌های خدماتی / جدول قیمت‌گذاری.
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
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

/**
 * کلاس پکیج‌ها.
 */
class Packages extends Novin_AI_Widget_Base {

	/**
	 * نام المان.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'novin-packages';
	}

	/**
	 * عنوان المان.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'پکیج‌های خدماتی', 'novin-ai' );
	}

	/**
	 * آیکون المان.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-price-table';
	}

	/**
	 * کلمات کلیدی.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords() {
		return array( 'pricing', 'packages', 'پکیج', 'تعرفه', 'قیمت', 'اشتراک' );
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
					'cpt'    => esc_html__( 'نوع نوشته «پکیج‌ها»', 'novin-ai' ),
				),
			)
		);

		$this->add_control(
			'show_toggle',
			array(
				'label'        => esc_html__( 'تغییر ماهانه / سالانه', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'monthly_text',
			array(
				'label'     => esc_html__( 'متن حالت ماهانه', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'ماهانه', 'novin-ai' ),
				'condition' => array( 'show_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'yearly_text',
			array(
				'label'     => esc_html__( 'متن حالت سالانه', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'سالیانه (۲ ماه رایگان)', 'novin-ai' ),
				'condition' => array( 'show_toggle' => 'yes' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			array(
				'label'   => esc_html__( 'عنوان پکیج', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'پکیج پایه', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'desc',
			array(
				'label' => esc_html__( 'توضیح کوتاه', 'novin-ai' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);

		$repeater->add_control(
			'currency',
			array(
				'label'   => esc_html__( 'واحد پول', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'تومان', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'price',
			array(
				'label'   => esc_html__( 'قیمت ماهانه', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '4,500,000',
			)
		);

		$repeater->add_control(
			'price_yearly',
			array(
				'label'   => esc_html__( 'قیمت سالانه', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '45,000,000',
			)
		);

		$repeater->add_control(
			'period',
			array(
				'label'   => esc_html__( 'دوره', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '/ ماهانه',
			)
		);

		$repeater->add_control(
			'features',
			array(
				'label'       => esc_html__( 'امکانات (هر خط یک مورد)', 'novin-ai' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => "دسترسی به پنل مانیتورینگ\nپشتیبانی ایمیلی\n۳۰ روز گارانتی",
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'badge',
			array(
				'label' => esc_html__( 'برچسب', 'novin-ai' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'متن دکمه', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'سفارش پکیج', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'button_link',
			array(
				'label'       => esc_html__( 'لینک دکمه', 'novin-ai' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);

		$repeater->add_control(
			'featured',
			array(
				'label'        => esc_html__( 'پکیج ویژه', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label' => esc_html__( 'آیکون', 'novin-ai' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'پکیج‌ها', 'novin-ai' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'title'    => esc_html__( 'پشتیبانی پایه', 'novin-ai' ),
						'desc'     => esc_html__( 'مناسب استارتاپ‌ها و تیم‌های کوچک', 'novin-ai' ),
						'price'    => '4,500,000',
						'features' => "پاسخگویی در ساعات اداری\nمانیتورینگ روزانه\nگزارش ماهانه",
						'button_text' => esc_html__( 'سفارش پکیج', 'novin-ai' ),
					),
					array(
						'title'    => esc_html__( 'پشتیبانی حرفه‌ای', 'novin-ai' ),
						'desc'     => esc_html__( 'مناسب کسب‌وکارهای در حال رشد', 'novin-ai' ),
						'price'    => '9,800,000',
						'features' => "پشتیبانی ۲۴ ساعته\nSLA دو ساعته\nبروزرسانی امنیتی\nمشاوره ماهانه",
						'badge'    => esc_html__( 'پیشنهاد ویژه', 'novin-ai' ),
						'featured' => 'yes',
						'button_text' => esc_html__( 'سفارش پکیج', 'novin-ai' ),
					),
					array(
						'title'    => esc_html__( 'پشتیبانی سازمانی', 'novin-ai' ),
						'desc'     => esc_html__( 'مناسب سازمان‌ها و زیرساخت حساس', 'novin-ai' ),
						'price'    => '22,000,000',
						'features' => "تیم اختصاصی\nSLA سی دقیقه‌ای\nامنیت و ممیزی مستمر\nگزارش‌دهی سفارشی",
						'button_text' => esc_html__( 'تماس با ما', 'novin-ai' ),
					),
				),
				'title_field' => '{{{ title }}}',
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'     => esc_html__( 'تعداد پکیج', 'novin-ai' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3,
				'min'       => 1,
				'max'       => 6,
				'condition' => array( 'source' => 'cpt' ),
			)
		);

		$this->end_controls_section();

		$this->section_title_controls(
			array(
				'title_default' => esc_html__( 'پکیج‌های خدماتی متناسب با نیاز شما', 'novin-ai' ),
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
			'featured_scale',
			array(
				'label'      => esc_html__( 'بزرگ‌نمایی پکیج ویژه', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '' ),
				'range'      => array(
					'' => array(
						'min' => 1,
						'max' => 1.3,
						'step' => 0.01,
					),
				),
				'default'    => array( 'size' => 1.05, 'unit' => '' ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-package--featured' => 'transform: scale({{SIZE}}); z-index: 2;',
				),
			)
		);

		$this->add_control(
			'show_features',
			array(
				'label'        => esc_html__( 'نمایش امکانات', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_icon',
			array(
				'label'        => esc_html__( 'نمایش آیکون', 'novin-ai' ),
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
				'default'      => '',
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

		$this->card_style_controls( '{{WRAPPER}} .nv-package' );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'featured_background',
				'label'    => esc_html__( 'پس‌زمینه پکیج ویژه', 'novin-ai' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .nv-package--featured',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'featured_border',
				'label'    => esc_html__( 'حاشیه پکیج ویژه', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-package--featured',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'featured_shadow',
				'label'    => esc_html__( 'سایه پکیج ویژه', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-package--featured',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_price_style',
			array(
				'label' => esc_html__( 'استایل قیمت و متن', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'package_title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-package__title',
			)
		);

		$this->add_control(
			'package_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-package__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_typography',
				'label'    => esc_html__( 'تایپوگرافی قیمت', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-package__amount',
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => esc_html__( 'رنگ قیمت', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-package__amount' => 'background-image: linear-gradient(120deg, {{VALUE}}, {{price_gradient.VALUE}});',
				),
			)
		);

		$this->add_control(
			'price_gradient',
			array(
				'label'   => esc_html__( 'رنگ دوم قیمت', 'novin-ai' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#22d3ee',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'features_typography',
				'label'    => esc_html__( 'تایپوگرافی امکانات', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-package__features li',
			)
		);

		$this->add_control(
			'features_color',
			array(
				'label'     => esc_html__( 'رنگ امکانات', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-package__features li' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'check_color',
			array(
				'label'     => esc_html__( 'رنگ تیک', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-package__features li svg' => 'color: {{VALUE}};',
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

		$this->add_render_attribute( 'wrapper', 'class', 'nv-widget nv-section nv-widget-packages' );

		$packages = array();

		if ( 'cpt' === $settings['source'] ) {
			$settings['post_type'] = 'novin_package';
			$query                 = $this->nv_query( $settings, 'novin_package' );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					$post_id = get_the_ID();

					$packages[] = array(
						'title'        => get_the_title(),
						'desc'         => novin_ai_excerpt( get_the_excerpt() ? get_the_excerpt() : get_the_content(), 18 ),
						'currency'     => novin_ai_get_meta( $post_id, 'currency', esc_html__( 'تومان', 'novin-ai' ) ),
						'price'        => novin_ai_get_meta( $post_id, 'price' ),
						'price_yearly' => novin_ai_get_meta( $post_id, 'price_yearly' ),
						'period'       => novin_ai_get_meta( $post_id, 'period', '/ ماهانه' ),
						'features'     => novin_ai_meta_lines( novin_ai_get_meta( $post_id, 'features' ) ),
						'badge'        => novin_ai_get_meta( $post_id, 'badge' ),
						'button_text'  => novin_ai_get_meta( $post_id, 'button_text', esc_html__( 'سفارش پکیج', 'novin-ai' ) ),
						'button_link'  => array( 'url' => novin_ai_get_meta( $post_id, 'button_url' ) ? novin_ai_get_meta( $post_id, 'button_url' ) : get_permalink() ),
						'featured'     => novin_ai_get_meta( $post_id, 'featured' ) ? 'yes' : '',
						'icon'         => array(),
					);
				}

				wp_reset_postdata();
			}
		} elseif ( ! empty( $settings['items'] ) ) {
			foreach ( $settings['items'] as $item ) {
				$packages[] = array(
					'title'        => $item['title'],
					'desc'         => $item['desc'],
					'currency'     => $item['currency'],
					'price'        => $item['price'],
					'price_yearly' => $item['price_yearly'],
					'period'       => $item['period'],
					'features'     => novin_ai_meta_lines( $item['features'] ),
					'badge'        => $item['badge'],
					'button_text'  => $item['button_text'],
					'button_link'  => $item['button_link'],
					'featured'     => $item['featured'],
					'icon'         => $item['icon'],
				);
			}
		}
		?>
		<section <?php $this->nv_attr( 'wrapper' ); ?>>
			<?php $this->nv_backdrop( $settings ); ?>

			<div class="nv-container">
				<?php $this->render_heading( $settings ); ?>

				<?php if ( 'yes' === $settings['show_toggle'] ) : ?>
					<div class="nv-pricing-toggle" data-nv-pricing-toggle>
						<button class="nv-pricing-toggle__btn is-active" type="button" data-nv-pricing="monthly"><?php echo esc_html( $settings['monthly_text'] ); ?></button>
						<button class="nv-pricing-toggle__btn" type="button" data-nv-pricing="yearly"><?php echo esc_html( $settings['yearly_text'] ); ?></button>
					</div>
				<?php endif; ?>

				<div class="nv-grid">
					<?php if ( ! empty( $packages ) ) : ?>
						<?php foreach ( $packages as $package ) : ?>
							<?php
							$classes = array( 'nv-card', 'nv-package', $this->nv_classes( $settings, array() ) );

							if ( 'yes' === $package['featured'] ) {
								$classes[] = 'nv-package--featured';
							}
							?>
							<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-nv-package>
								<?php if ( ! empty( $package['badge'] ) ) : ?>
									<span class="nv-package__badge nv-badge nv-badge--glow"><?php echo esc_html( $package['badge'] ); ?></span>
								<?php endif; ?>

								<?php if ( 'yes' === $settings['show_icon'] && ! empty( $package['icon']['value'] ) ) : ?>
									<div class="nv-package__icon"><?php $this->nv_icon( $package['icon'] ); ?></div>
								<?php endif; ?>

								<h3 class="nv-package__title"><?php echo esc_html( $package['title'] ); ?></h3>

								<?php if ( ! empty( $package['desc'] ) ) : ?>
									<p class="nv-package__desc"><?php echo esc_html( $package['desc'] ); ?></p>
								<?php endif; ?>

								<div class="nv-package__price">
									<span class="nv-package__amount nv-gradient" data-nv-price-monthly="<?php echo esc_attr( $package['price'] ); ?>" data-nv-price-yearly="<?php echo esc_attr( $package['price_yearly'] ); ?>">
										<?php echo esc_html( $package['price'] ); ?>
									</span>
									<span class="nv-package__currency"><?php echo esc_html( $package['currency'] ); ?></span>
									<span class="nv-package__period"><?php echo esc_html( $package['period'] ); ?></span>
								</div>

								<?php if ( 'yes' === $settings['show_features'] && ! empty( $package['features'] ) ) : ?>
									<ul class="nv-package__features">
										<?php foreach ( $package['features'] as $feature ) : ?>
											<li>
												<?php echo novin_ai_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
												<span><?php echo esc_html( $feature ); ?></span>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>

								<?php if ( ! empty( $package['button_text'] ) ) : ?>
									<?php $this->nv_link_open( $package, 'button_link', 'nv-btn ' . ( 'yes' === $package['featured'] ? 'nv-btn--primary' : 'nv-btn--outline' ) ); ?>
									<span><?php echo esc_html( $package['button_text'] ); ?></span>
									<?php echo novin_ai_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<?php $this->nv_link_close( $package, 'button_link' ); ?>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					<?php else : ?>
						<p class="nv-widget__empty"><?php esc_html_e( 'پکیجی تعریف نشده است.', 'novin-ai' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
