<?php
/**
 * Widget: Neo Loyalty (باشگاه مشتریان).
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Loyalty
 */
class Widget_Loyalty extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-loyalty';
	}

	public function get_title() {
		return esc_html__( 'باشگاه مشتریان', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-ribbon';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'view', array(
			'label'   => esc_html__( 'نمایش', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'card',
			'options' => array(
				'card'       => esc_html__( 'کارت امتیاز کاربر', 'neomorph' ),
				'tiers'      => esc_html__( 'سطوح عضویت', 'neomorph' ),
				'rewards'    => esc_html__( 'جوایز قابل تعویض', 'neomorph' ),
				'leaderboard'=> esc_html__( 'جدول برترین‌ها', 'neomorph' ),
				'intro'      => esc_html__( 'معرفی باشگاه (عمومی)', 'neomorph' ),
			),
		) );
		$this->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'باشگاه مشتریان', 'neomorph' ),
		) );
		$this->add_control( 'count', array(
			'label'     => esc_html__( 'تعداد (رتبه‌ها/جوایز)', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'default'   => 5,
			'condition' => array( 'view' => array( 'rewards', 'leaderboard' ) ),
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'card_style', array(
			'label' => esc_html__( 'کارت امتیاز', 'neomorph' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'progress_color', array(
			'label'     => esc_html__( 'رنگ نوار پیشرفت', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#6c5ce7',
			'selectors' => array( '{{WRAPPER}} .neo-progress__bar' => 'background: {{VALUE}};' ),
		) );
		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل کلی', 'neomorph' ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$view = $s['view'];
		if ( neomorph_core_active() ) {
			echo do_shortcode( sprintf( '[neomorph_loyalty view="%s" title="%s" count="%d"]', esc_attr( $view ), esc_attr( $s['title'] ), (int) $s['count'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			return;
		}
		?>
		<section class="neo-widget neo-surface neo-loyalty neo-loyalty--<?php echo esc_attr( $view ); ?>">
			<h2 class="neo-section__title"><?php echo esc_html( $s['title'] ); ?></h2>
			<p><?php esc_html_e( 'با هر خرید امتیاز بگیرید و از جوایز باشگاه مشتریان بهره‌مند شوید. (نیازمند Neomorph Core)', 'neomorph' ); ?></p>
		</section>
		<?php
	}
}
