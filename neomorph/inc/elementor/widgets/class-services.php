<?php
/**
 * Widget: Neo Services (خدمات) — grid / circles / list / tabs.
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Services
 */
class Widget_Services extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-services';
	}

	public function get_title() {
		return esc_html__( 'خدمات نئومورف', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-box';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان بخش', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'خدمات ما', 'neomorph' ),
		) );
		$this->add_control( 'subtitle', array(
			'label'   => esc_html__( 'زیرعنوان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
		) );

		$this->add_control( 'source', array(
			'label'       => esc_html__( 'منبع خدمات', 'neomorph' ),
			'type'        => \Elementor\Controls_Manager::SELECT,
			'default'     => 'manual',
			'options'     => array(
				'manual' => esc_html__( 'ورود دستی (زیر)', 'neomorph' ),
				'cpt'    => esc_html__( 'از بخش «خدمات» پیشخوان', 'neomorph' ),
			),
			'description' => esc_html__( 'با انتخاب «پیشخوان»، خدمات از فرم حرفه‌ای «المان‌ها ← خدمات» خوانده می‌شود.', 'neomorph' ),
		) );

		$this->add_control( 'cpt_count', array(
			'label'     => esc_html__( 'تعداد خدمات', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'default'   => 6,
			'condition' => array( 'source' => 'cpt' ),
		) );

		$this->add_control( 'cpt_link_text', array(
			'label'     => esc_html__( 'متن دکمه هر خدمت', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::TEXT,
			'default'   => esc_html__( 'بیشتر', 'neomorph' ),
			'condition' => array( 'source' => 'cpt' ),
		) );

		$this->add_control( 'layout', array(
			'label'   => esc_html__( 'چیدمان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'grid',
			'options' => array(
				'grid'    => esc_html__( 'گرید کارت‌ها', 'neomorph' ),
				'circles' => esc_html__( 'دایره‌های نرم', 'neomorph' ),
				'list'    => esc_html__( 'لیست افقی', 'neomorph' ),
				'tabs'    => esc_html__( 'تب‌دار', 'neomorph' ),
			),
		) );

		$this->add_control( 'columns', array(
			'label'       => esc_html__( 'تعداد ستون', 'neomorph' ),
			'type'        => \Elementor\Controls_Manager::SELECT,
			'default'     => '3',
			'options'     => array( '2' => '2', '3' => '3', '4' => '4' ),
			'condition'   => array( 'layout' => array( 'grid', 'circles' ) ),
		) );

		$repeater = new \Elementor\Repeater();

		$repeater->add_control( 'icon', array(
			'label'   => esc_html__( 'آیکون (ایموجی یا متن کوتاه)', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '✦',
		) );
		$repeater->add_control( 'image', array(
			'label' => esc_html__( 'یا تصویر آیکون', 'neomorph' ),
			'type'  => \Elementor\Controls_Manager::MEDIA,
		) );
		$repeater->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'خدمات حرفه‌ای', 'neomorph' ),
		) );
		$repeater->add_control( 'text', array(
			'label'   => esc_html__( 'توضیح', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'توضیح کوتاه خدمات شما در اینجا قرار می‌گیرد.', 'neomorph' ),
		) );
		$repeater->add_control( 'link', array(
			'label' => esc_html__( 'لینک', 'neomorph' ),
			'type'  => \Elementor\Controls_Manager::URL,
		) );

		$this->add_control( 'services', array(
			'label'       => esc_html__( 'لیست خدمات', 'neomorph' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => array(
				array( 'title' => esc_html__( 'طراحی وب', 'neomorph' ), 'icon' => '💻' ),
				array( 'title' => esc_html__( 'سئو', 'neomorph' ), 'icon' => '🚀' ),
				array( 'title' => esc_html__( 'پشتیبانی', 'neomorph' ), 'icon' => '🛟' ),
			),
			'title_field' => '{{{ title }}}',
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'item_style', array(
			'label' => esc_html__( 'آیتم‌ها', 'neomorph' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'icon_bg', array(
			'label'     => esc_html__( 'رنگ پس‌زمینه آیکون', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .neo-service-card__icon' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'item_title_color', array(
			'label'     => esc_html__( 'رنگ عنوان', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .neo-service-card h3' => 'color: {{VALUE}};' ),
		) );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'item_title_typo',
			'selector' => '{{WRAPPER}} .neo-service-card h3',
		) );
		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل کلی', 'neomorph' ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		// Source: manual repeater or Services CPT (professional admin forms).
		if ( 'cpt' === $s['source'] ) {
			$service_items = array();
			$q             = new \WP_Query(
				array(
					'post_type'      => 'service',
					'posts_per_page' => max( 1, (int) $s['cpt_count'] ),
					'post_status'    => 'publish',
					'orderby'        => 'menu_order date',
					'order'          => 'ASC date',
				)
			);
			while ( $q->have_posts() ) {
				$q->the_post();
				$icon_media = (int) \nmc_get_meta( get_the_ID(), 'icon_media' );
				$service_items[] = array(
					'icon'  => \nmc_get_meta( get_the_ID(), 'icon_text', '✦' ),
					'image' => $icon_media ? array( 'url' => (string) wp_get_attachment_image_url( $icon_media, 'thumbnail' ) ) : array(),
					'title' => get_the_title(),
					'text'  => \nmc_get_meta( get_the_ID(), 'short_desc', get_the_excerpt() ),
					'link'  => array(
						'url' => \nmc_get_meta( get_the_ID(), 'service_link', get_permalink() ),
					),
				);
			}
			wp_reset_postdata();
			$s['services'] = $service_items;
			// Use icon images when provided.
			$use_images = true;
		} else {
			$use_images = true;
		}
		?>
		<section class="neo-widget neo-services neo-services--<?php echo esc_attr( $s['layout'] ); ?> <?php echo esc_attr( $this->neo_widget_class() ); ?>">
			<?php if ( $s['title'] || $s['subtitle'] ) : ?>
				<header class="neo-section__head">
					<?php if ( $s['title'] ) : ?><h2 class="neo-section__title"><?php echo esc_html( $s['title'] ); ?></h2><?php endif; ?>
					<?php if ( $s['subtitle'] ) : ?><p class="neo-section__subtitle"><?php echo esc_html( $s['subtitle'] ); ?></p><?php endif; ?>
				</header>
			<?php endif; ?>

			<div class="neo-grid neo-grid--<?php echo esc_attr( $s['columns'] ); ?> neo-services__grid">
				<?php foreach ( $s['services'] as $item ) : ?>
					<article class="neo-card neo-service-card">
						<div class="neo-service-card__icon neo-inset">
							<?php
							if ( ! empty( $item['image']['url'] ) ) {
								printf( '<img src="%s" alt="">', esc_url( $item['image']['url'] ) );
							} else {
								echo esc_html( $item['icon'] );
							}
							?>
						</div>
						<h3><?php echo esc_html( $item['title'] ); ?></h3>
						<p><?php echo esc_html( $item['text'] ); ?></p>
						<?php if ( ! empty( $item['link']['url'] ) ) : ?>
							<a class="neo-link" href="<?php echo esc_url( $item['link']['url'] ); ?>">← <?php esc_html_e( 'بیشتر', 'neomorph' ); ?></a>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}
