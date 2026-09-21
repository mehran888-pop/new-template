<?php
/**
 * Widget: Neo Register (فرم ثبت‌نام حرفه‌ای).
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Register
 */
class Widget_Register extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-register';
	}

	public function get_title() {
		return esc_html__( 'فرم ثبت‌نام حرفه‌ای', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-user-add';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'ساخت حساب کاربری', 'neomorph' ),
		) );
		$this->add_control( 'subtitle', array(
			'label'   => esc_html__( 'زیرعنوان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'ثبت‌نام با شماره موبایل و کد تأیید — عضو باشگاه مشتریان شوید', 'neomorph' ),
		) );
		$this->add_control( 'fields', array(
			'label'   => esc_html__( 'فیلدهای فرم', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT2,
			'multiple' => true,
			'default' => array( 'first_name', 'last_name', 'phone', 'email', 'password' ),
			'options' => array(
				'first_name' => esc_html__( 'نام', 'neomorph' ),
				'last_name'  => esc_html__( 'نام خانوادگی', 'neomorph' ),
				'phone'      => esc_html__( 'موبایل (الزامی)', 'neomorph' ),
				'email'      => esc_html__( 'ایمیل', 'neomorph' ),
				'password'   => esc_html__( 'رمز عبور', 'neomorph' ),
				'company'    => esc_html__( 'شرکت', 'neomorph' ),
			),
		) );
		$this->add_control( 'loyalty_notice', array(
			'label'   => esc_html__( 'نمایش پیام عضویت باشگاه مشتریان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'loyalty_text', array(
			'label'     => esc_html__( 'متن پیام باشگاه', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::TEXTAREA,
			'default'   => esc_html__( 'با ثبت‌نام، عضو باشگاه مشتریان می‌شوید و از امتیاز هدیه استفاده کنید.', 'neomorph' ),
			'condition' => array( 'loyalty_notice' => 'yes' ),
		) );
		$this->add_control( 'terms', array(
			'label'   => esc_html__( 'متن قوانین', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'قوانین و مقررات را می‌پذیرم', 'neomorph' ),
		) );
		$this->add_control( 'login_text', array(
			'label'   => esc_html__( 'لینک ورود', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'قبلاً ثبت‌نام کرده‌اید؟ ورود', 'neomorph' ),
		) );
		$this->add_control( 'login_link', array(
			'label' => esc_html__( 'آدرس صفحه ورود', 'neomorph' ),
			'type'  => \Elementor\Controls_Manager::URL,
		) );

		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل فرم', 'neomorph' ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		if ( neomorph_core_active() ) {
			echo do_shortcode(
				sprintf(
					'[neomorph_register title="%s" subtitle="%s" fields="%s" loyalty="%s" loyalty_text="%s" terms="%s" login_text="%s" login_url="%s"]',
					esc_attr( $s['title'] ),
					esc_attr( $s['subtitle'] ),
					esc_attr( implode( ',', (array) $s['fields'] ) ),
					esc_attr( $s['loyalty_notice'] ),
					esc_attr( $s['loyalty_text'] ),
					esc_attr( $s['terms'] ),
					esc_attr( $s['login_text'] ),
					esc_attr( isset( $s['login_link']['url'] ) ? $s['login_link']['url'] : '' )
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput
			return;
		}
		?>
		<div class="neo-widget neo-surface neo-register">
			<h2><?php echo esc_html( $s['title'] ); ?></h2>
			<p><?php echo esc_html( $s['subtitle'] ); ?></p>
			<p class="neo-empty"><?php esc_html_e( 'افزونه Neomorph Core برای ثبت‌نام پیامکی فعال نیست.', 'neomorph' ); ?></p>
		</div>
		<?php
	}
}
