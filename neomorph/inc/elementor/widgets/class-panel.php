<?php
/**
 * Widget: Neo Customer Panel (پنل مشتریان).
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Panel
 */
class Widget_Panel extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-panel';
	}

	public function get_title() {
		return esc_html__( 'پنل مشتریان', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-my-account';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'layout', array(
			'label'   => esc_html__( 'چیدمان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'sidebar',
			'options' => array(
				'sidebar' => esc_html__( 'منوی کناری (کلاسیک)', 'neomorph' ),
				'tabs'    => esc_html__( 'تب‌های بالا', 'neomorph' ),
				'cards'   => esc_html__( 'داشبورد کارتی', 'neomorph' ),
			),
		) );

		$this->add_control( 'tabs', array(
			'label'       => esc_html__( 'تب‌های فعال', 'neomorph' ),
			'type'        => \Elementor\Controls_Manager::SELECT2,
			'multiple'    => true,
			'default'     => array( 'dashboard', 'orders', 'finance', 'tickets', 'loyalty', 'consulting', 'profile' ),
			'options'     => array(
				'dashboard'  => esc_html__( 'داشبورد', 'neomorph' ),
				'orders'     => esc_html__( 'خریدهای انجام شده', 'neomorph' ),
				'finance'    => esc_html__( 'مالی و حسابداری', 'neomorph' ),
				'tickets'    => esc_html__( 'تیکت', 'neomorph' ),
				'loyalty'    => esc_html__( 'باشگاه مشتریان', 'neomorph' ),
				'consulting' => esc_html__( 'درخواست مشاوره و پشتیبانی', 'neomorph' ),
				'profile'    => esc_html__( 'پروفایل', 'neomorph' ),
			),
		) );

		$this->add_control( 'guest_view', array(
			'label'   => esc_html__( 'رفتار بازدیدکننده واردنشده', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'login',
			'options' => array(
				'login'   => esc_html__( 'نمایش فرم ورود OTP', 'neomorph' ),
				'message' => esc_html__( 'پیام + دکمه ورود', 'neomorph' ),
			),
		) );

		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل پنل', 'neomorph' ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		if ( neomorph_core_active() ) {
			echo do_shortcode(
				sprintf(
					'[neomorph_panel layout="%s" tabs="%s" guest="%s"]',
					esc_attr( $s['layout'] ),
					esc_attr( implode( ',', (array) $s['tabs'] ) ),
					esc_attr( $s['guest_view'] )
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput
			return;
		}
		?>
		<div class="neo-widget neo-surface neo-panel">
			<p><?php esc_html_e( 'پنل مشتریان نیازمند افزونه Neomorph Core است.', 'neomorph' ); ?></p>
			<a class="neo-btn neo-btn--primary" href="<?php echo esc_url( neomorph_panel_url( 'login' ) ); ?>"><?php esc_html_e( 'ورود', 'neomorph' ); ?></a>
		</div>
		<?php
	}
}
