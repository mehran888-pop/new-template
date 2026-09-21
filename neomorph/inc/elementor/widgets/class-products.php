<?php
/**
 * Widget: Neo Products (محصولات ووکامرس).
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Products
 */
class Widget_Products extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-products';
	}

	public function get_title() {
		return esc_html__( 'محصولات', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان بخش', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'محصولات ویژه', 'neomorph' ),
		) );

		$this->add_control( 'source', array(
			'label'   => esc_html__( 'نوع نمایش', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'featured',
			'options' => array(
				'featured'     => esc_html__( 'ویژه', 'neomorph' ),
				'best_selling' => esc_html__( 'پرفروش‌ها', 'neomorph' ),
				'recent'       => esc_html__( 'جدیدترین', 'neomorph' ),
				'category'     => esc_html__( 'دسته خاص', 'neomorph' ),
				'ids'          => esc_html__( 'شناسه‌های خاص', 'neomorph' ),
			),
		) );
		$this->add_control( 'category', array(
			'label'     => esc_html__( 'نامک دسته محصول', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::TEXT,
			'condition' => array( 'source' => 'category' ),
		) );
		$this->add_control( 'ids', array(
			'label'     => esc_html__( 'شناسه محصولات (با ویرگول)', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::TEXT,
			'condition' => array( 'source' => 'ids' ),
		) );
		$this->add_control( 'count', array(
			'label'   => esc_html__( 'تعداد', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 4,
		) );
		$this->add_control( 'columns', array(
			'label'   => esc_html__( 'ستون‌ها', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '4',
			'options' => array( '2' => '2', '3' => '3', '4' => '4' ),
		) );
		$this->add_control( 'card_style', array(
			'label'   => esc_html__( 'سبک کارت', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'raised',
			'options' => array(
				'raised'      => esc_html__( 'برآمده', 'neomorph' ),
				'inset-media' => esc_html__( 'رسانه فرورفته', 'neomorph' ),
				'compact'     => esc_html__( 'فشرده', 'neomorph' ),
			),
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'price_style', array(
			'label' => esc_html__( 'قیمت و دکمه', 'neomorph' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'price_color', array(
			'label'     => esc_html__( 'رنگ قیمت', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#6c5ce7',
			'selectors' => array( '{{WRAPPER}} .price' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'btn_bg', array(
			'label'     => esc_html__( 'رنگ دکمه افزودن', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .button, {{WRAPPER}} .ajax_add_to_cart' => 'background: {{VALUE}}; border-color: {{VALUE}}; color:#fff;' ),
		) );
		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل کلی', 'neomorph' ) ) );
	}

	protected function render() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<div class="neo-widget neo-empty"><p>' . esc_html__( 'ووکامرس فعال نیست.', 'neomorph' ) . '</p></div>';
			return;
		}

		$s     = $this->get_settings_for_display();
		$atts  = array(
			'limit'   => (int) $s['count'],
			'columns' => (int) $s['columns'],
		);
		switch ( $s['source'] ) {
			case 'best_selling':
				$atts['orderby'] = 'popularity';
				break;
			case 'recent':
				$atts['orderby'] = 'date';
				break;
			case 'category':
				$atts['category'] = sanitize_title( $s['category'] );
				break;
			case 'ids':
				$atts['ids'] = $s['ids'];
				break;
			default:
				$atts['visibility'] = 'featured';
		}

		// Neomorph card class via filter for this render only.
		$card_style = $s['card_style'];
		$filter     = function ( $html ) use ( $card_style ) {
			return str_replace( 'product type-product', 'product neo-product-card neo-product-card--' . esc_attr( $card_style ) . ' neo-card type-product', $html );
		};
		add_filter( 'woocommerce_shortcode_products_loop', $filter, 20 );
		?>
		<section class="neo-widget neo-products neo-products--<?php echo esc_attr( $s['card_style'] ); ?> <?php echo esc_attr( $this->neo_widget_class() ); ?>">
			<?php if ( $s['title'] ) : ?>
				<h2 class="neo-section__title"><?php echo esc_html( $s['title'] ); ?></h2>
			<?php endif; ?>
			<div class="neo-products__loop">
				<?php echo do_shortcode( '[products ' . implode( ' ', array_map( function ( $k, $v ) { return $k . '="' . esc_attr( $v ) . '"'; }, array_keys( $atts ), array_values( $atts ) ) ) . ']' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</section>
		<?php
		remove_filter( 'woocommerce_shortcode_products_loop', $filter, 20 );
	}
}
