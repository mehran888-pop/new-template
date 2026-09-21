<?php
/**
 * Widget: Neo Cart (سبد خرید حرفه‌ای نئومورف) — mini drawer + full cart page styles.
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Cart
 */
class Widget_Cart extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-cart';
	}

	public function get_title() {
		return esc_html__( 'سبد خرید حرفه‌ای', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-cart';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'mode', array(
			'label'   => esc_html__( 'حالت', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'mini',
			'options' => array(
				'mini'   => esc_html__( 'سبد کوچک (دکمه + کشو)', 'neomorph' ),
				'full'   => esc_html__( 'جدول سبد کامل', 'neomorph' ),
				'totals' => esc_html__( 'جمع کل + دکمه پرداخت', 'neomorph' ),
			),
		) );

		$this->add_control( 'show_thumb', array(
			'label'     => esc_html__( 'نمایش تصویر محصول', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => array( 'mode!' => 'totals' ),
		) );
		$this->add_control( 'show_coupon', array(
			'label'     => esc_html__( 'نمایش فرم کوپن', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::SWITCHER,
			'condition' => array( 'mode' => 'full' ),
		) );
		$this->add_control( 'btn_label', array(
			'label'   => esc_html__( 'متن دکمه پرداخت', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'تکمیل خرید', 'neomorph' ),
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'cart_style', array(
			'label' => esc_html__( 'اقلام سبد', 'neomorph' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'line_bg', array(
			'label'     => esc_html__( 'پس‌زمینه هر قلم', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .neo-cart-line' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'total_color', array(
			'label'     => esc_html__( 'رنگ جمع کل', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#6c5ce7',
			'selectors' => array( '{{WRAPPER}} .neo-cart-total' => 'color: {{VALUE}};' ),
		) );
		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل کلی', 'neomorph' ) ) );
	}

	protected function render() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<div class="neo-widget neo-empty"><p>' . esc_html__( 'ووکامرس فعال نیست.', 'neomorph' ) . '</p></div>';
			return;
		}
		$s = $this->get_settings_for_display();
		?>
		<div class="neo-widget neo-cart neo-cart--<?php echo esc_attr( $s['mode'] ); ?> <?php echo esc_attr( $this->neo_widget_class() ); ?>">
			<?php if ( 'mini' === $s['mode'] ) : ?>
				<button type="button" class="neo-btn neo-cart__toggle" aria-expanded="false">
					🛒 <?php esc_html_e( 'سبد خرید', 'neomorph' ); ?>
					<span class="neo-cart-btn__count"><?php echo esc_html( (int) WC()->cart->get_cart_contents_count() ); ?></span>
				</button>
				<div class="neo-cart__drawer neo-surface" hidden>
					<?php woocommerce_mini_cart(); ?>
				</div>
			<?php elseif ( 'full' === $s['mode'] ) : ?>
				<div class="neo-cart__table neo-surface">
					<?php echo do_shortcode( '[woocommerce_cart]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			<?php else : ?>
				<div class="neo-cart__totals neo-inset">
					<?php
					$total = WC()->cart ? WC()->cart->get_total( 'edit' ) : 0;
					?>
					<p class="neo-cart-total">
						<span><?php esc_html_e( 'جمع کل:', 'neomorph' ); ?></span>
						<strong><?php echo wp_kses_post( $total ); ?></strong>
					</p>
					<a class="neo-btn neo-btn--primary neo-btn--block" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php echo esc_html( $s['btn_label'] ); ?></a>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
