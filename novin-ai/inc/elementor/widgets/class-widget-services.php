<?php
/**
 * المان اختصاصی بخش خدمات.
 *
 * @package Novin_AI
 */

namespace Novin_AI\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

/**
 * کلاس خدمات.
 */
class Services extends Novin_AI_Widget_Base {

	/**
	 * نام المان.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'novin-services';
	}

	/**
	 * عنوان المان.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'بخش خدمات', 'novin-ai' );
	}

	/**
	 * آیکون المان.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-info-box';
	}

	/**
	 * کلمات کلیدی.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords() {
		return array( 'services', 'خدمات', 'service', 'سرویس' );
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
				'default' => 'cpt',
				'options' => array(
					'cpt'    => esc_html__( 'نوع نوشته «خدمات» (پیشنهادی)', 'novin-ai' ),
					'manual' => esc_html__( 'ورود دستی', 'novin-ai' ),
				),
			)
		);

		$this->add_control(
			'post_type',
			array(
				'label'     => esc_html__( 'منبع محتوا', 'novin-ai' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'novin_service',
				'options'   => array(
					'novin_service' => esc_html__( 'خدمات', 'novin-ai' ),
					'novin_project' => esc_html__( 'پروژه‌ها', 'novin-ai' ),
					'page'          => esc_html__( 'برگه‌ها', 'novin-ai' ),
				),
				'condition' => array( 'source' => 'cpt' ),
			)
		);

		$this->add_control(
			'taxonomy_filter',
			array(
				'label'     => esc_html__( 'فیلتر دسته‌بندی', 'novin-ai' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''                  => esc_html__( 'همه', 'novin-ai' ),
					'novin_service_cat' => esc_html__( 'دسته خدمات', 'novin-ai' ),
					'novin_project_cat' => esc_html__( 'دسته پروژه‌ها', 'novin-ai' ),
				),
				'condition' => array( 'source' => 'cpt' ),
			)
		);

		$this->add_control(
			'terms',
			array(
				'label'       => esc_html__( 'ترم‌ها', 'novin-ai' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->nv_all_terms(),
				'label_block' => true,
				'condition'   => array(
					'source'          => 'cpt',
					'taxonomy_filter!' => '',
				),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'     => esc_html__( 'تعداد آیتم', 'novin-ai' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 6,
				'min'       => 1,
				'max'       => 36,
				'condition' => array( 'source' => 'cpt' ),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => esc_html__( 'مرتب‌سازی', 'novin-ai' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'menu_order',
				'options'   => array(
					'menu_order' => esc_html__( 'ترتیب دستی', 'novin-ai' ),
					'date'       => esc_html__( 'تاریخ', 'novin-ai' ),
					'title'      => esc_html__( 'عنوان', 'novin-ai' ),
					'rand'       => esc_html__( 'تصادفی', 'novin-ai' ),
				),
				'condition' => array( 'source' => 'cpt' ),
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
			'image',
			array(
				'label' => esc_html__( 'یا تصویر', 'novin-ai' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'   => esc_html__( 'عنوان', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'توسعه مدل‌های هوش مصنوعی', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'desc',
			array(
				'label' => esc_html__( 'توضیح', 'novin-ai' ),
				'type'  => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'طراحی و آموزش مدل‌های اختصاصی متناسب با داده‌های سازمان شما.', 'novin-ai' ),
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
			'features',
			array(
				'label'       => esc_html__( 'ویژگی‌ها (هر خط یک مورد)', 'novin-ai' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => "تحلیل داده\nیادگیری ماشین\nاستقرار ابری",
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'price',
			array(
				'label' => esc_html__( 'قیمت / تعرفه', 'novin-ai' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'link',
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
					array(
						'title' => esc_html__( 'هوش مصنوعی و یادگیری ماشین', 'novin-ai' ),
						'desc'  => esc_html__( 'طراحی، آموزش و استقرار مدل‌های اختصاصی برای فرآیندهای سازمانی.', 'novin-ai' ),
					),
					array(
						'title' => esc_html__( 'توسعه نرم‌افزار و وب', 'novin-ai' ),
						'desc'  => esc_html__( 'ساخت وب‌اپلیکیشن‌های مقیاس‌پذیر با معماری مدرن و امن.', 'novin-ai' ),
					),
					array(
						'title' => esc_html__( 'زیرساخت و امنیت شبکه', 'novin-ai' ),
						'desc'  => esc_html__( 'مانیتورینگ، سخت‌سازی و پشتیبانی ۲۴ ساعته زیرساخت شما.', 'novin-ai' ),
					),
					array(
						'title' => esc_html__( 'خدمات پشتیبانی و SLA', 'novin-ai' ),
						'desc'  => esc_html__( 'پشتیبانی سطح‌بندی‌شده، قرارداد سطح خدمات و پاسخگویی سریع.', 'novin-ai' ),
					),
					array(
						'title' => esc_html__( 'فروش نرم‌افزار و لایسنس', 'novin-ai' ),
						'desc'  => esc_html__( 'ارائه لایسنس معتبر، نصب، آموزش و بروزرسانی مستمر.', 'novin-ai' ),
					),
					array(
						'title' => esc_html__( 'مشاوره تحول دیجیتال', 'novin-ai' ),
						'desc'  => esc_html__( 'نقشه‌راه فناوری، انتخاب معماری و بهینه‌سازی هزینه‌ها.', 'novin-ai' ),
					),
				),
				'title_field' => '{{{ title }}}',
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$this->end_controls_section();

		$this->section_title_controls(
			array(
				'title_default' => esc_html__( 'خدماتی که کسب‌وکار شما را متحول می‌کند', 'novin-ai' ),
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

		$this->add_control(
			'show_icon',
			array(
				'label'        => esc_html__( 'نمایش آیکون / تصویر', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_desc',
			array(
				'label'        => esc_html__( 'نمایش توضیح', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_features',
			array(
				'label'        => esc_html__( 'نمایش فهرست ویژگی‌ها', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_link',
			array(
				'label'        => esc_html__( 'نمایش لینک جزئیات', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'link_text',
			array(
				'label'     => esc_html__( 'متن لینک', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'جزئیات خدمت', 'novin-ai' ),
				'condition' => array( 'show_link' => 'yes' ),
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label'   => esc_html__( 'تعداد کلمات خلاصه', 'novin-ai' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 24,
				'min'     => 5,
				'max'     => 80,
			)
		);

		$this->end_controls_section();

		$this->backdrop_controls();

		/* ---------------------------------- استایل‌ها ---------------------------------- */
		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => esc_html__( 'استایل کارت', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->card_style_controls( '{{WRAPPER}} .nv-service' );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'icon_border',
				'label'          => esc_html__( 'حاشیه آیکون', 'novin-ai' ),
				'selector'       => '{{WRAPPER}} .nv-service__icon',
				'fields_options' => array(
					'border' => array( 'default' => 'solid' ),
				),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 16,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-service__icon' => 'font-size: {{SIZE}}{{UNIT}}; width: calc({{SIZE}}{{UNIT}} * 2); height: calc({{SIZE}}{{UNIT}} * 2);',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-service__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

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
				'name'     => 'item_title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-service__title',
			)
		);

		$this->add_control(
			'item_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-service__title, {{WRAPPER}} .nv-service__title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_desc_typography',
				'label'    => esc_html__( 'تایپوگرافی توضیح', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-service__desc',
			)
		);

		$this->add_control(
			'item_desc_color',
			array(
				'label'     => esc_html__( 'رنگ توضیح', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-service__desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'features_typography',
				'label'    => esc_html__( 'تایپوگرافی ویژگی‌ها', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-features li',
			)
		);

		$this->add_control(
			'features_color',
			array(
				'label'     => esc_html__( 'رنگ ویژگی‌ها', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-features li' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'badge_shadow',
				'label'    => esc_html__( 'سایه برچسب', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-service__badge',
			)
		);

		$this->end_controls_section();

		$this->section_title_style_controls();
	}

	/**
	 * رندر یک کارت خدمت.
	 *
	 * @param array<string, mixed> $data     داده‌های کارت.
	 * @param array<string, mixed> $settings تنظیمات ویجت.
	 * @return void
	 */
	protected function render_card( $data, $settings ) {
		$classes = array( 'nv-card', 'nv-service', $this->nv_classes( $settings, array() ) );
		?>
		<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php if ( ! empty( $data['badge'] ) ) : ?>
				<span class="nv-service__badge nv-badge"><?php echo esc_html( $data['badge'] ); ?></span>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['show_icon'] && ( ! empty( $data['icon']['value'] ) || ! empty( $data['image'] ) ) ) : ?>
				<div class="nv-service__icon">
					<?php
					if ( ! empty( $data['image'] ) ) {
						echo '<img src="' . esc_url( $data['image'] ) . '" alt="' . esc_attr( $data['title'] ) . '" loading="lazy">';
					} else {
						$this->nv_icon( $data['icon'] );
					}
					?>
				</div>
			<?php endif; ?>

			<h3 class="nv-service__title">
				<?php if ( ! empty( $data['link'] ) ) : ?>
					<a href="<?php echo esc_url( $data['link'] ); ?>"><?php echo esc_html( $data['title'] ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $data['title'] ); ?>
				<?php endif; ?>
			</h3>

			<?php if ( 'yes' === $settings['show_desc'] && ! empty( $data['desc'] ) ) : ?>
				<p class="nv-service__desc"><?php echo esc_html( $data['desc'] ); ?></p>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['show_features'] && ! empty( $data['features'] ) ) : ?>
				<ul class="nv-features">
					<?php foreach ( $data['features'] as $feature ) : ?>
						<li>
							<?php echo novin_ai_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span><?php echo esc_html( $feature ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( ! empty( $data['price'] ) ) : ?>
				<div class="nv-service__price"><?php echo esc_html( $data['price'] ); ?></div>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['show_link'] && ! empty( $data['link'] ) ) : ?>
				<a class="nv-link-more" href="<?php echo esc_url( $data['link'] ); ?>">
					<span><?php echo esc_html( $settings['link_text'] ); ?></span>
					<?php echo novin_ai_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			<?php endif; ?>
		</article>
		<?php
	}

	/**
	 * رندر خروجی.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'nv-widget nv-section nv-widget-services' );

		$items = array();

		if ( 'cpt' === $settings['source'] ) {
			$query = $this->nv_query( $settings, 'novin_service' );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					$post_id = get_the_ID();

					$items[] = array(
						'title'    => get_the_title(),
						'desc'     => novin_ai_excerpt( get_the_excerpt() ? get_the_excerpt() : get_the_content(), (int) $settings['excerpt_length'] ),
						'image'    => get_the_post_thumbnail_url( $post_id, 'thumbnail' ),
						'icon'     => array( 'value' => novin_ai_get_meta( $post_id, 'icon' ) ),
						'badge'    => novin_ai_get_meta( $post_id, 'badge' ),
						'price'    => novin_ai_get_meta( $post_id, 'price' ),
						'features' => novin_ai_meta_lines( novin_ai_get_meta( $post_id, 'features' ) ),
						'link'     => novin_ai_get_meta( $post_id, 'link' ) ? novin_ai_get_meta( $post_id, 'link' ) : get_permalink(),
					);
				}

				wp_reset_postdata();
			}
		} elseif ( ! empty( $settings['items'] ) ) {
			foreach ( $settings['items'] as $item ) {
				$items[] = array(
					'title'    => $item['title'],
					'desc'     => $item['desc'],
					'image'    => ! empty( $item['image']['url'] ) ? $item['image']['url'] : '',
					'icon'     => $item['icon'],
					'badge'    => $item['badge'],
					'price'    => $item['price'],
					'features' => novin_ai_meta_lines( $item['features'] ),
					'link'     => ! empty( $item['link']['url'] ) ? $item['link']['url'] : '',
				);
			}
		}
		?>
		<section <?php $this->nv_attr( 'wrapper' ); ?>>
			<?php $this->nv_backdrop( $settings ); ?>

			<div class="nv-container">
				<?php $this->render_heading( $settings ); ?>

				<div class="nv-grid">
					<?php
					if ( ! empty( $items ) ) {
						foreach ( $items as $item ) {
							$this->render_card( $item, $settings );
						}
					} else {
						echo '<p class="nv-widget__empty">' . esc_html__( 'موردی برای نمایش وجود ندارد. از منوی «خدمات» در پیشخوان محتوا اضافه کنید.', 'novin-ai' ) . '</p>';
					}
					?>
				</div>
			</div>
		</section>
		<?php
	}
}
