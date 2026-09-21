<?php
/**
 * Widget: Neo Header (هدر قابل طراحی با المنتور).
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Header
 */
class Widget_Header extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-header';
	}

	public function get_title() {
		return esc_html__( 'هدر نئومورف', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-header';
	}

	public function get_style_depends() {
		return array( 'neomorph-elementor' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'layout', array(
			'label'   => esc_html__( 'چیدمان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'classic',
			'options' => array(
				'classic'  => esc_html__( 'کلاسیک', 'neomorph' ),
				'centered' => esc_html__( 'وسط‌چین', 'neomorph' ),
				'minimal'  => esc_html__( 'مینیمال', 'neomorph' ),
				'floating' => esc_html__( 'شناور (کپسولی)', 'neomorph' ),
			),
		) );

		$this->add_control( 'logo_type', array(
			'label'   => esc_html__( 'لوگو', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'customizer',
			'options' => array(
				'customizer' => esc_html__( 'لوگوی سایت (شخصی‌ساز)', 'neomorph' ),
				'image'      => esc_html__( 'تصویر دلخواه', 'neomorph' ),
				'text'       => esc_html__( 'متن', 'neomorph' ),
			),
		) );
		$this->add_control( 'logo_image', array(
			'label'     => esc_html__( 'تصویر لوگو', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::MEDIA,
			'condition' => array( 'logo_type' => 'image' ),
		) );
		$this->add_control( 'logo_text', array(
			'label'     => esc_html__( 'متن لوگو', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::TEXT,
			'default'   => get_bloginfo( 'name' ),
			'condition' => array( 'logo_type' => 'text' ),
		) );

		$this->add_control( 'show_menu', array(
			'label'   => esc_html__( 'نمایش منو', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'show_search', array(
			'label'   => esc_html__( 'دکمه جستجو', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'show_cart', array(
			'label'   => esc_html__( 'سبد خرید', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'show_auth', array(
			'label'   => esc_html__( 'دکمه ورود/پنل', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'cta_text', array(
			'label' => esc_html__( 'دکمه CTA', 'neomorph' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		) );
		$this->add_control( 'cta_link', array(
			'label'     => esc_html__( 'لینک CTA', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::URL,
			'condition' => array( 'cta_text!' => '' ),
		) );
		$this->add_control( 'sticky', array(
			'label'   => esc_html__( 'چسبان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'menu_style', array(
			'label' => esc_html__( 'منو', 'neomorph' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'menu_color', array(
			'label'     => esc_html__( 'رنگ آیتم‌ها', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .neo-header-widget__menu a' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'menu_hover', array(
			'label'     => esc_html__( 'رنگ هاور', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .neo-header-widget__menu a:hover' => 'color: {{VALUE}};' ),
		) );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'menu_typo',
			'selector' => '{{WRAPPER}} .neo-header-widget__menu a',
		) );
		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل هدر', 'neomorph' ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<header class="neo-widget neo-header-widget neo-header-widget--<?php echo esc_attr( $s['layout'] ); ?> <?php echo esc_attr( $this->neo_widget_class() ); ?><?php echo 'yes' === $s['sticky'] ? ' is-sticky' : ''; ?>">
			<div class="neo-header-widget__inner">
				<div class="neo-header-widget__brand">
					<?php
					if ( 'image' === $s['logo_type'] && ! empty( $s['logo_image']['url'] ) ) {
						printf( '<a href="%s"><img src="%s" alt="%s" class="neo-header-widget__logo"></a>', esc_url( home_url( '/' ) ), esc_url( $s['logo_image']['url'] ), esc_attr( get_bloginfo( 'name' ) ) );
					} elseif ( 'text' === $s['logo_type'] ) {
						printf( '<a href="%s" class="neo-header-widget__title">%s</a>', esc_url( home_url( '/' ) ), esc_html( $s['logo_text'] ) );
					} elseif ( has_custom_logo() ) {
						the_custom_logo();
					} else {
						printf( '<a href="%s" class="neo-header-widget__title">%s</a>', esc_url( home_url( '/' ) ), esc_html( get_bloginfo( 'name' ) ) );
					}
					?>
				</div>

				<?php if ( 'yes' === $s['show_menu'] ) : ?>
					<nav class="neo-header-widget__menu" aria-label="<?php esc_attr_e( 'منوی اصلی', 'neomorph' ); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'primary',
								'container'      => false,
								'fallback_cb'    => false,
								'depth'          => 2,
							)
						);
						?>
					</nav>
				<?php endif; ?>

				<div class="neo-header-widget__actions">
					<?php if ( 'yes' === $s['show_search'] ) : ?>
						<a class="neo-btn neo-btn--icon" href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" aria-label="<?php esc_attr_e( 'جستجو', 'neomorph' ); ?>">⌕</a>
					<?php endif; ?>
					<?php if ( 'yes' === $s['show_cart'] && class_exists( 'WooCommerce' ) ) : ?>
						<a class="neo-btn neo-btn--icon neo-cart-btn" href="<?php echo esc_url( wc_get_cart_url() ); ?>">🛒<span class="neo-cart-btn__count"><?php echo esc_html( (int) WC()->cart->get_cart_contents_count() ); ?></span></a>
					<?php endif; ?>
					<?php if ( 'yes' === $s['show_auth'] ) : ?>
						<?php if ( is_user_logged_in() ) : ?>
							<a class="neo-btn neo-btn--primary neo-btn--sm" href="<?php echo esc_url( neomorph_panel_url() ); ?>"><?php esc_html_e( 'پنل من', 'neomorph' ); ?></a>
						<?php else : ?>
							<a class="neo-btn neo-btn--sm" href="<?php echo esc_url( neomorph_panel_url( 'login' ) ); ?>"><?php esc_html_e( 'ورود', 'neomorph' ); ?></a>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ( $s['cta_text'] ) : ?>
						<a class="neo-btn neo-btn--primary neo-btn--sm" href="<?php echo esc_url( isset( $s['cta_link']['url'] ) ? $s['cta_link']['url'] : '#' ); ?>"><?php echo esc_html( $s['cta_text'] ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</header>
		<?php
	}
}
