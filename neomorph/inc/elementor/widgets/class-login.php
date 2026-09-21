<?php
/**
 * Widget: Neo Login (فرم ورود حرفه‌ای با OTP / رمز عبور).
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Login
 */
class Widget_Login extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-login';
	}

	public function get_title() {
		return esc_html__( 'فرم ورود حرفه‌ای', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-lock-user';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'ورود به حساب کاربری', 'neomorph' ),
		) );
		$this->add_control( 'subtitle', array(
			'label'   => esc_html__( 'زیرعنوان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'با شماره موبایل خود و کد یک‌بار مصرف وارد شوید', 'neomorph' ),
		) );
		$this->add_control( 'methods', array(
			'label'   => esc_html__( 'روش‌های ورود', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT2,
			'multiple' => true,
			'default' => array( 'otp' ),
			'options' => array(
				'otp'      => esc_html__( 'کد یک‌بار مصرف (پیامکی)', 'neomorph' ),
				'password' => esc_html__( 'نام کاربری / رمز عبور', 'neomorph' ),
			),
		) );
		$this->add_control( 'layout', array(
			'label'   => esc_html__( 'چیدمان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'card',
			'options' => array(
				'card'    => esc_html__( 'کارت متمرکز', 'neomorph' ),
				'split'   => esc_html__( 'دو ستونه با تصویر', 'neomorph' ),
				'inline'  => esc_html__( 'درون‌خطی (هدر/سایدبار)', 'neomorph' ),
			),
		) );
		$this->add_control( 'image', array(
			'label'     => esc_html__( 'تصویر کنار فرم', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::MEDIA,
			'condition' => array( 'layout' => 'split' ),
		) );
		$this->add_control( 'register_text', array(
			'label'   => esc_html__( 'لینک ثبت‌نام', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'حساب ندارید؟ ثبت‌نام کنید', 'neomorph' ),
		) );
		$this->add_control( 'register_link', array(
			'label' => esc_html__( 'آدرس صفحه ثبت‌نام', 'neomorph' ),
			'type'  => \Elementor\Controls_Manager::URL,
		) );
		$this->add_control( 'redirect', array(
			'label'       => esc_html__( 'آدرس هدایت پس از ورود', 'neomorph' ),
			'type'        => \Elementor\Controls_Manager::URL,
			'description' => esc_html__( 'خالی = پنل مشتریان', 'neomorph' ),
		) );

		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل فرم', 'neomorph' ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		if ( is_user_logged_in() && ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			echo '<div class="neo-widget neo-surface neo-login"><p>' . esc_html__( 'شما وارد شده‌اید.', 'neomorph' ) . '</p><a class="neo-btn neo-btn--primary" href="' . esc_url( neomorph_panel_url() ) . '">' . esc_html__( 'رفتن به پنل', 'neomorph' ) . '</a></div>';
			return;
		}

		if ( neomorph_core_active() ) {
			echo do_shortcode(
				sprintf(
					'[neomorph_login title="%s" subtitle="%s" methods="%s" layout="%s" register_text="%s" register_url="%s"]',
					esc_attr( $s['title'] ),
					esc_attr( $s['subtitle'] ),
					esc_attr( implode( ',', (array) $s['methods'] ) ),
					esc_attr( $s['layout'] ),
					esc_attr( $s['register_text'] ),
					esc_attr( isset( $s['register_link']['url'] ) ? $s['register_link']['url'] : '' )
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput
			return;
		}
		?>
		<div class="neo-widget neo-surface neo-login">
			<h2><?php echo esc_html( $s['title'] ); ?></h2>
			<p><?php echo esc_html( $s['subtitle'] ); ?></p>
			<form method="post" action="<?php echo esc_url( wp_login_url() ); ?>" class="neo-form">
				<label class="neo-label"><?php esc_html_e( 'نام کاربری', 'neomorph' ); ?><input class="neo-input" type="text" name="log"></label>
				<label class="neo-label"><?php esc_html_e( 'رمز عبور', 'neomorph' ); ?><input class="neo-input" type="password" name="pwd"></label>
				<input type="hidden" name="redirect_to" value="<?php echo esc_url( isset( $s['redirect']['url'] ) && $s['redirect']['url'] ? $s['redirect']['url'] : neomorph_panel_url() ); ?>">
				<button class="neo-btn neo-btn--primary neo-btn--block" type="submit"><?php esc_html_e( 'ورود', 'neomorph' ); ?></button>
			</form>
		</div>
		<?php
	}
}
