<?php
/**
 * کلاس پایه تمام المان‌های اختصاصی المنتور.
 *
 * این کلاس کنترل‌های مشترک (عنوان بخش، کارت، دکمه، افکت‌های پس‌زمینه، کوئری)
 * را در اختیار ویجت‌ها قرار می‌دهد تا تمام استایل‌ها از طریق المنتور قابل کنترل باشد.
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
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
	// این فایل تنها از طریق Novin_AI_Elementor::include_widgets() و زمانی که
	// المنتور آماده است بارگذاری می‌شود، بنابراین این شرط صرفاً یک لایه ایمنی است.
	return;
}

/**
 * کلاس پایه تمام المان‌های اختصاصی المنتور.
 */
abstract class Novin_AI_Widget_Base extends Widget_Base {

	/**
	 * دسته‌بندی المان.
	 *
	 * @return array<int, string>
	 */
	public function get_categories() {
		return array( 'novin-ai' );
	}

	/**
	 * وابستگی‌های استایل.
	 *
	 * @return array<int, string>
	 */
	public function get_style_depends() {
		return array( 'novin-ai-widgets' );
	}

	/**
	 * وابستگی‌های اسکریپت.
	 *
	 * @return array<int, string>
	 */
	public function get_script_depends() {
		return array( 'novin-ai-frontend' );
	}

	/* --------------------------------------------------------------------
	 * ابزارهای رندر
	 * ------------------------------------------------------------------ */

	/**
	 * چاپ امن ویژگی‌های رندر.
	 *
	 * @param string $element نام المان.
	 * @return void
	 */
	protected function nv_attr( $element ) {
		if ( method_exists( $this, 'print_render_attribute_string' ) ) {
			$this->print_render_attribute_string( $element );
			return;
		}

		echo $this->get_render_attribute_string( $element ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * ساخت ویژگی‌های لینک.
	 *
	 * @param array<string, mixed> $settings تنظیمات.
	 * @param string               $key      کلید کنترل URL.
	 * @return array<string, string>
	 */
	protected function nv_link_attrs( $settings, $key ) {
		if ( empty( $settings[ $key ]['url'] ) ) {
			return array();
		}

		$attrs = array( 'href' => esc_url( $settings[ $key ]['url'] ) );

		if ( ! empty( $settings[ $key ]['is_external'] ) ) {
			$attrs['target'] = '_blank';
		}

		if ( ! empty( $settings[ $key ]['nofollow'] ) ) {
			$attrs['rel'] = 'nofollow';
		}

		if ( ! empty( $settings[ $key ]['custom_attributes'] ) ) {
			$attrs['rel'] = trim( ( isset( $attrs['rel'] ) ? $attrs['rel'] . ' ' : '' ) . 'noopener' );
		}

		return $attrs;
	}

	/**
	 * چاپ تگ باز لینک در صورت وجود.
	 *
	 * @param array<string, mixed> $settings تنظیمات.
	 * @param string               $key      کلید.
	 * @param string               $class    کلاس CSS.
	 * @param string               $tag      تگ HTML.
	 * @return void
	 */
	protected function nv_link_open( $settings, $key, $class = '', $tag = 'a' ) {
		$attrs = $this->nv_link_attrs( $settings, $key );

		if ( empty( $attrs ) ) {
			return;
		}

		$class = $class ? ' class="' . esc_attr( $class ) . '"' : '';

		echo '<' . esc_attr( $tag ) . $class;

		foreach ( $attrs as $name => $value ) {
			echo ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
		}

		echo '>';
	}

	/**
	 * بستن تگ لینک.
	 *
	 * @param array<string, mixed> $settings تنظیمات.
	 * @param string               $key      کلید.
	 * @param string               $tag      تگ HTML.
	 * @return void
	 */
	protected function nv_link_close( $settings, $key, $tag = 'a' ) {
		if ( empty( $settings[ $key ]['url'] ) ) {
			return;
		}

		echo '</' . esc_attr( $tag ) . '>';
	}

	/**
	 * رندر آیکون المنتور.
	 *
	 * @param array<string, mixed> $icon تنظیمات آیکون.
	 * @param string               $class کلاس اضافی.
	 * @return void
	 */
	protected function nv_icon( $icon, $class = '' ) {
		if ( empty( $icon ) || empty( $icon['value'] ) ) {
			return;
		}

		if ( class_exists( '\Elementor\Icons_Manager' ) && method_exists( 'Elementor\Icons_Manager', 'render_icon' ) ) {
			Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true', 'class' => $class ) );
			return;
		}

		// فالبک برای نسخه‌های قدیمی المنتور.
		if ( ! empty( $icon['value'] ) && false !== strpos( $icon['value'], '<' ) ) {
			echo '<span class="nv-icon ' . esc_attr( $class ) . '">' . $icon['value'] . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			echo '<i class="nv-icon ' . esc_attr( (string) $icon['value'] ) . ' ' . esc_attr( $class ) . '" aria-hidden="true"></i>';
		}
	}

	/**
	 * رندر تصویر از کنترل مدیا + اندازه.
	 *
	 * @param array<string, mixed> $settings تنظیمات.
	 * @param string               $key      کلید تصویر.
	 * @param string               $size_key کلید اندازه.
	 * @param string               $class    کلاس.
	 * @param string               $fallback تصویر جایگزین.
	 * @return void
	 */
	protected function nv_image( $settings, $key = 'image', $size_key = 'image_size', $class = 'nv-media__img', $fallback = '' ) {
		if ( empty( $settings[ $key ]['id'] ) && empty( $settings[ $key ]['url'] ) ) {
			if ( $fallback ) {
				echo '<img class="' . esc_attr( $class ) . '" src="' . esc_url( $fallback ) . '" alt="" loading="lazy">';
			}

			return;
		}

		if ( class_exists( '\Elementor\Group_Control_Image_Size' ) && ! empty( $settings[ $key ]['id'] ) ) {
			echo Group_Control_Image_Size::get_attachment_image_html( $settings, $size_key, $key ); // phpcs:ignore WordPress.Security.EscapeOutput
			return;
		}

		$url = ! empty( $settings[ $key ]['url'] ) ? $settings[ $key ]['url'] : '';

		if ( $url ) {
			echo '<img class="' . esc_attr( $class ) . '" src="' . esc_url( $url ) . '" alt="' . esc_attr( ! empty( $settings[ $key ]['alt'] ) ? $settings[ $key ]['alt'] : '' ) . '" loading="lazy">';
		}
	}

	/**
	 * رندر لایه‌های پس‌زمینه (کره نوری / شبکه / ذرات).
	 *
	 * @param array<string, mixed> $settings تنظیمات.
	 * @return void
	 */
	protected function nv_backdrop( $settings ) {
		if ( ! empty( $settings['show_orbs'] ) && 'yes' === $settings['show_orbs'] ) {
			echo '<span class="nv-orb nv-orb--1" aria-hidden="true"></span>';
			echo '<span class="nv-orb nv-orb--2" aria-hidden="true"></span>';
		}

		if ( ! empty( $settings['show_grid'] ) && 'yes' === $settings['show_grid'] ) {
			echo '<span class="nv-grid-floor" aria-hidden="true"></span>';
		}

		if ( ! empty( $settings['show_particles'] ) && 'yes' === $settings['show_particles'] ) {
			echo '<canvas class="nv-particles" data-nv-particles aria-hidden="true"></canvas>';
		}

		if ( ! empty( $settings['show_noise'] ) && 'yes' === $settings['show_noise'] ) {
			echo '<span class="nv-noise" aria-hidden="true"></span>';
		}
	}

	/**
	 * کلاس‌های افکت برای المان‌ها.
	 *
	 * @param array<string, mixed> $settings تنظیمات.
	 * @param array<int, string>   $extra   کلاس‌های اضافی.
	 * @return string
	 */
	protected function nv_classes( $settings, $extra = array() ) {
		$classes = $extra;

		if ( ! empty( $settings['enable_tilt'] ) && 'yes' === $settings['enable_tilt'] ) {
			$classes[] = 'nv-tilt';
		}

		if ( ! empty( $settings['enable_reveal'] ) && 'yes' === $settings['enable_reveal'] ) {
			$classes[] = 'nv-reveal';
		}

		if ( ! empty( $settings['card_style'] ) ) {
			$classes[] = 'nv-card--' . sanitize_html_class( $settings['card_style'] );
		}

		return implode( ' ', array_filter( $classes ) );
	}

	/**
	 * کوئری محتوا بر اساس تنظیمات.
	 *
	 * @param array<string, mixed> $settings تنظیمات.
	 * @param string               $type    نوع پیش‌فرض نوشته.
	 * @return \WP_Query
	 */
	protected function nv_query( $settings, $type = 'post' ) {
		$post_type = ! empty( $settings['post_type'] ) ? $settings['post_type'] : $type;
		$per_page  = ! empty( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 6;
		$orderby   = ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date';
		$order     = ! empty( $settings['order'] ) ? $settings['order'] : 'DESC';
		$offset    = ! empty( $settings['offset'] ) ? (int) $settings['offset'] : 0;

		$args = array(
			'post_type'              => $post_type,
			'posts_per_page'         => $per_page,
			'orderby'                => $orderby,
			'order'                  => $order,
			'offset'                 => $offset,
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'post_status'            => 'publish',
			'update_post_term_cache' => false,
		);

		if ( 'product' === $post_type ) {
			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				'relation' => 'AND',
			);
		}

		// فیلتر دسته‌بندی.
		if ( ! empty( $settings['taxonomy_filter'] ) && ! empty( $settings['terms'] ) && is_array( $settings['terms'] ) ) {
			$tax = $settings['taxonomy_filter'];

			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => $tax,
					'field'    => 'term_id',
					'terms'    => array_map( 'intval', $settings['terms'] ),
				),
			);
		}

		// حذف نوشته‌های مشخص.
		if ( ! empty( $settings['exclude_ids'] ) ) {
			$ids              = array_map( 'intval', array_filter( array_map( 'trim', explode( ',', (string) $settings['exclude_ids'] ) ) ) );
			$args['post__not_in'] = $ids;
		}

		// فقط نوشته‌های دارای تصویر شاخص.
		if ( ! empty( $settings['only_with_thumb'] ) && 'yes' === $settings['only_with_thumb'] ) {
			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'key'     => '_thumbnail_id',
					'compare' => 'EXISTS',
				),
			);
		}

		return new \WP_Query( $args );
	}

	/* --------------------------------------------------------------------
	 * کنترل‌های مشترک
	 * ------------------------------------------------------------------ */

	/**
	 * کنترل‌های عنوان بخش.
	 *
	 * @param array<string, mixed> $args تنظیمات اضافی.
	 * @return void
	 */
	protected function section_title_controls( $args = array() ) {
		$with_subtitle = ! isset( $args['subtitle'] ) || $args['subtitle'];
		$with_desc     = ! isset( $args['desc'] ) || $args['desc'];

		$this->start_controls_section(
			'section_heading',
			array(
				'label' => esc_html__( 'عنوان بخش', 'novin-ai' ),
			)
		);

		if ( $with_subtitle ) {
			$this->add_control(
				'subtitle',
				array(
					'label'       => esc_html__( 'برچسب کوتاه', 'novin-ai' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'هوش مصنوعی', 'novin-ai' ),
					'placeholder' => esc_html__( 'مثال: خدمات ما', 'novin-ai' ),
					'label_block' => true,
					'dynamic'     => array( 'active' => true ),
				)
			);
		}

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'novin-ai' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => isset( $args['title_default'] ) ? $args['title_default'] : esc_html__( 'راهکارهای هوشمند برای کسب‌وکار شما', 'novin-ai' ),
				'rows'        => 2,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		if ( $with_desc ) {
			$this->add_control(
				'description',
				array(
					'label'       => esc_html__( 'توضیح', 'novin-ai' ),
					'type'        => Controls_Manager::WYSIWYG,
					'default'     => esc_html__( 'ما با ترکیب هوش مصنوعی و مهندسی نرم‌افزار، محصولاتی می‌سازیم که رشد شما را شتاب می‌دهند.', 'novin-ai' ),
					'label_block' => true,
				)
			);
		}

		$this->add_control(
			'title_tag',
			array(
				'label'   => esc_html__( 'تگ عنوان', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'p'  => 'P',
				),
			)
		);

		$this->add_responsive_control(
			'heading_align',
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
					'{{WRAPPER}} .nv-heading' => 'align-items: {{VALUE}}; text-align: {{VALUE}};',
				),
				'condition' => array(),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * استایل‌های عنوان بخش.
	 *
	 * @return void
	 */
	protected function section_title_style_controls() {
		$this->start_controls_section(
			'section_heading_style',
			array(
				'label' => esc_html__( 'استایل عنوان', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-heading__title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-heading__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_gradient',
			array(
				'label'        => esc_html__( 'متن گرادیانتی', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'selectors'    => array(
					'{{WRAPPER}} .nv-heading__title .nv-gradient' => 'background-image: linear-gradient(120deg, {{title_gradient_a.VALUE}}, {{title_gradient_b.VALUE}}); -webkit-background-clip: text; background-clip: text; color: transparent;',
				),
			)
		);

		$this->add_control(
			'title_gradient_a',
			array(
				'label'     => esc_html__( 'رنگ اول گرادیان', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8b7cff',
				'condition' => array( 'title_gradient' => 'yes' ),
			)
		);

		$this->add_control(
			'title_gradient_b',
			array(
				'label'     => esc_html__( 'رنگ دوم گرادیان', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#22d3ee',
				'condition' => array( 'title_gradient' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'label'    => esc_html__( 'تایپوگرافی برچسب', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-heading__subtitle',
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => esc_html__( 'رنگ برچسب', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-heading__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'subtitle_background',
				'label'    => esc_html__( 'پس‌زمینه برچسب', 'novin-ai' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .nv-heading__subtitle',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'label'    => esc_html__( 'تایپوگرافی توضیح', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-heading__desc',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => esc_html__( 'رنگ توضیح', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-heading__desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'heading_spacing',
			array(
				'label'      => esc_html__( 'فاصله عنوان تا محتوا', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * کنترل‌های چیدمان (ستون‌ها و فاصله).
	 *
	 * @param array<string, mixed> $args تنظیمات.
	 * @return void
	 */
	protected function layout_controls( $args = array() ) {
		$default_cols = isset( $args['default'] ) ? $args['default'] : 3;

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => esc_html__( 'تعداد ستون', 'novin-ai' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => (string) $default_cols,
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '۱',
					'2' => '۲',
					'3' => '۳',
					'4' => '۴',
					'5' => '۵',
					'6' => '۶',
				),
				'selectors'      => array(
					'{{WRAPPER}} .nv-grid' => '--nv-cols: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => esc_html__( 'فاصله بین آیتم‌ها', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'default'    => array( 'size' => 28 ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-grid' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);
	}

	/**
	 * کنترل‌های کوئری محتوا.
	 *
	 * @param array<string, string> $types انواع نوشته مجاز.
	 * @return void
	 */
	protected function query_controls( $types = array() ) {
		$post_types = novin_ai_cpt_choices();

		if ( ! empty( $types ) ) {
			$post_types = array_intersect_key( $post_types, array_flip( $types ) );
		}

		$this->add_control(
			'post_type',
			array(
				'label'   => esc_html__( 'منبع محتوا', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => key( $post_types ),
				'options' => $post_types,
			)
		);

		$this->add_control(
			'taxonomy_filter',
			array(
				'label'   => esc_html__( 'فیلتر دسته‌بندی', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''                  => esc_html__( 'همه', 'novin-ai' ),
					'category'          => esc_html__( 'دسته‌بندی نوشته‌ها', 'novin-ai' ),
					'novin_project_cat' => esc_html__( 'دسته پروژه‌ها', 'novin-ai' ),
					'novin_service_cat' => esc_html__( 'دسته خدمات', 'novin-ai' ),
					'novin_team_group'  => esc_html__( 'دپارتمان‌ها', 'novin-ai' ),
					'novin_package_cat' => esc_html__( 'دسته پکیج‌ها', 'novin-ai' ),
					'product_cat'       => esc_html__( 'دسته محصولات', 'novin-ai' ),
				),
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
				'condition'   => array( 'taxonomy_filter!' => '' ),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'تعداد آیتم', 'novin-ai' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 1,
				'max'     => 50,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => esc_html__( 'مرتب‌سازی بر اساس', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'          => esc_html__( 'تاریخ', 'novin-ai' ),
					'title'         => esc_html__( 'عنوان', 'novin-ai' ),
					'rand'          => esc_html__( 'تصادفی', 'novin-ai' ),
					'menu_order'    => esc_html__( 'ترتیب دستی', 'novin-ai' ),
					'modified'      => esc_html__( 'آخرین بروزرسانی', 'novin-ai' ),
					'comment_count' => esc_html__( 'تعداد دیدگاه', 'novin-ai' ),
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
			'offset',
			array(
				'label' => esc_html__( 'رد کردن تعداد', 'novin-ai' ),
				'type'  => Controls_Manager::NUMBER,
				'min'   => 0,
				'step'  => 1,
			)
		);

		$this->add_control(
			'exclude_ids',
			array(
				'label'       => esc_html__( 'حذف آیتم‌ها (آیدی با کاما)', 'novin-ai' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '12,15,30',
			)
		);
	}

	/**
	 * تمام ترم‌های قابل استفاده.
	 *
	 * @return array<int|string, string>
	 */
	protected function nv_all_terms() {
		$taxonomies = array( 'category', 'novin_project_cat', 'novin_service_cat', 'novin_team_group', 'novin_package_cat', 'product_cat' );
		$options    = array();

		foreach ( $taxonomies as $tax ) {
			$terms = get_terms(
				array(
					'taxonomy'   => $tax,
					'hide_empty' => false,
				)
			);

			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				continue;
			}

			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $term->name . ' (' . $tax . ')';
			}
		}

		return $options;
	}

	/**
	 * کنترل‌های استایل کارت.
	 *
	 * @param string $selector سلکتور پیش‌فرض.
	 * @param array<string, mixed> $args تنظیمات.
	 * @return void
	 */
	protected function card_style_controls( $selector = '{{WRAPPER}} .nv-card', $args = array() ) {
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'card_background',
				'label'    => esc_html__( 'پس‌زمینه کارت', 'novin-ai' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => $selector,
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'novin-ai' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'انحنا', 'novin-ai' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => $selector,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => $selector,
			)
		);

		$this->add_control(
			'card_hover_heading',
			array(
				'label'     => esc_html__( 'حالت هاور', 'novin-ai' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_hover_shadow',
				'label'    => esc_html__( 'سایه در هاور', 'novin-ai' ),
				'selector' => $selector . ':hover',
			)
		);

		$this->add_control(
			'card_hover_border',
			array(
				'label'     => esc_html__( 'رنگ حاشیه در هاور', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$selector . ':hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_hover_lift',
			array(
				'label'      => esc_html__( 'بالا آمدن در هاور', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					$selector . ':hover' => 'transform: translateY(-{{SIZE}}{{UNIT}});',
				),
			)
		);
	}

	/**
	 * کنترل‌های افکت‌های پس‌زمینه.
	 *
	 * @return void
	 */
	protected function backdrop_controls() {
		$this->start_controls_section(
			'section_backdrop',
			array(
				'label' => esc_html__( 'جلوه‌های پس‌زمینه', 'novin-ai' ),
			)
		);

		$this->add_control(
			'show_orbs',
			array(
				'label'        => esc_html__( 'کره‌های نوری', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_grid',
			array(
				'label'        => esc_html__( 'شبکه سه‌بعدی', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_particles',
			array(
				'label'        => esc_html__( 'ذرات هوشمند', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_noise',
			array(
				'label'        => esc_html__( 'بافت نویز', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * کنترل‌های دکمه.
	 *
	 * @param string $label برچسب.
	 * @param string $key   کلید پیشوند.
	 * @return void
	 */
	protected function button_controls( $label = '', $key = 'button' ) {
		$label = $label ? $label : esc_html__( 'دکمه', 'novin-ai' );

		$this->start_controls_section(
			'section_' . $key,
			array(
				'label' => $label,
			)
		);

		$this->add_control(
			$key . '_text',
			array(
				'label'       => esc_html__( 'متن', 'novin-ai' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'مشاهده جزئیات', 'novin-ai' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			$key . '_link',
			array(
				'label'       => esc_html__( 'لینک', 'novin-ai' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			$key . '_icon',
			array(
				'label' => esc_html__( 'آیکون', 'novin-ai' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			$key . '_style',
			array(
				'label'   => esc_html__( 'استایل', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'primary',
				'options' => array(
					'primary'   => esc_html__( 'اصلی', 'novin-ai' ),
					'secondary' => esc_html__( 'فرعی', 'novin-ai' ),
					'outline'   => esc_html__( 'خطی', 'novin-ai' ),
					'ghost'     => esc_html__( 'شفاف', 'novin-ai' ),
					'neon'      => esc_html__( 'نئونی', 'novin-ai' ),
				),
			)
		);

		$this->add_control(
			$key . '_magnetic',
			array(
				'label'        => esc_html__( 'حالت مغناطیسی', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_responsive_control(
			$key . '_align',
			array(
				'label'     => esc_html__( 'تراز', 'novin-ai' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'راست', 'novin-ai' ),
						'icon'  => 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'وسط', 'novin-ai' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'چپ', 'novin-ai' ),
						'icon'  => 'eicon-h-align-left',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .nv-actions' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		/* استایل دکمه */
		$this->start_controls_section(
			'section_' . $key . '_style',
			array(
				'label' => $label . ' — ' . esc_html__( 'استایل', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $key . '_typography',
				'selector' => '{{WRAPPER}} .nv-btn',
			)
		);

		$this->add_control(
			$key . '_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => $key . '_background',
				'types'          => array( 'classic', 'gradient' ),
				'selector'       => '{{WRAPPER}} .nv-btn',
				'fields_options' => array(
					'background' => array( 'default' => 'gradient' ),
				),
			)
		);

		$this->add_responsive_control(
			$key . '_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'novin-ai' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			$key . '_radius',
			array(
				'label'      => esc_html__( 'انحنا', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => $key . '_shadow',
				'selector' => '{{WRAPPER}} .nv-btn',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * رندر دکمه.
	 *
	 * @param array<string, mixed> $settings تنظیمات.
	 * @param string               $key      کلید.
	 * @param string               $class    کلاس اضافه.
	 * @return void
	 */
	protected function render_button( $settings, $key = 'button', $class = '' ) {
		$text = ! empty( $settings[ $key . '_text' ] ) ? $settings[ $key . '_text' ] : '';

		if ( ! $text && empty( $settings[ $key . '_icon' ]['value'] ) ) {
			return;
		}

		$style    = ! empty( $settings[ $key . '_style' ] ) ? $settings[ $key . '_style' ] : 'primary';
		$magnetic = ! empty( $settings[ $key . '_magnetic' ] ) && 'yes' === $settings[ $key . '_magnetic' ] ? ' nv-magnetic' : '';

		$this->add_render_attribute( 'nv-btn-' . $key, 'class', 'nv-btn nv-btn--' . esc_attr( $style ) . $magnetic . ' ' . $class );

		$this->nv_link_open( $settings, $key . '_link', $this->get_render_attribute_string( 'nv-btn-' . $key ) );

		echo '<span class="nv-btn__text">' . wp_kses_post( $text ) . '</span>';

		if ( ! empty( $settings[ $key . '_icon' ]['value'] ) ) {
			echo '<span class="nv-btn__icon">';
			$this->nv_icon( $settings[ $key . '_icon' ] );
			echo '</span>';
		}

		$this->nv_link_close( $settings, $key . '_link' );
	}

	/**
	 * رندر عنوان بخش به صورت آماده.
	 *
	 * @param array<string, mixed> $settings تنظیمات.
	 * @return void
	 */
	protected function render_heading( $settings ) {
		$tag      = ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h2';
		$subtitle = isset( $settings['subtitle'] ) ? $settings['subtitle'] : '';
		$title    = isset( $settings['title'] ) ? $settings['title'] : '';
		$desc     = isset( $settings['description'] ) ? $settings['description'] : '';

		if ( ! $subtitle && ! $title && ! $desc ) {
			return;
		}

		echo '<div class="nv-heading">';

		if ( $subtitle ) {
			echo '<span class="nv-heading__subtitle">' . esc_html( $subtitle ) . '</span>';
		}

		if ( $title ) {
			$words = preg_split( '/\s+/u', trim( $title ) );
			$half  = count( $words ) > 2 ? ceil( count( $words ) / 2 ) : count( $words );
			$first = implode( ' ', array_slice( $words, 0, $half ) );
			$last  = implode( ' ', array_slice( $words, $half ) );

			echo '<' . esc_attr( $tag ) . ' class="nv-heading__title">';
			echo esc_html( $first ) . ' ';

			if ( $last ) {
				echo '<span class="nv-gradient">' . esc_html( $last ) . '</span>';
			}

			echo '</' . esc_attr( $tag ) . '>';
		}

		if ( $desc ) {
			echo '<div class="nv-heading__desc">' . wp_kses_post( $desc ) . '</div>';
		}

		echo '</div>';
	}
}
