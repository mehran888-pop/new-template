<?php
/**
 * Widget: Neo Contact (تماس با ما) — info cards + form (Gravity Forms or native).
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Contact
 */
class Widget_Contact extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-contact';
	}

	public function get_title() {
		return esc_html__( 'تماس با ما', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-phone';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'تماس با ما', 'neomorph' ),
		) );
		$this->add_control( 'subtitle', array(
			'label'   => esc_html__( 'زیرعنوان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
		) );

		$this->add_control( 'layout', array(
			'label'   => esc_html__( 'چیدمان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'side-by-side',
			'options' => array(
				'side-by-side'  => esc_html__( 'اطلاعات کنار فرم', 'neomorph' ),
				'cards-top'     => esc_html__( 'کارت‌های تماس بالا + فرم', 'neomorph' ),
				'minimal'       => esc_html__( 'حداقلی (فقط فرم)', 'neomorph' ),
			),
		) );

		$this->add_control( 'phone', array(
			'label'   => esc_html__( 'تلفن', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => neomorph_option( 'contact_phone', '' ),
		) );
		$this->add_control( 'email', array(
			'label'   => esc_html__( 'ایمیل', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => neomorph_option( 'contact_email', '' ),
		) );
		$this->add_control( 'address', array(
			'label'   => esc_html__( 'آدرس', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => neomorph_option( 'contact_address', '' ),
		) );
		$this->add_control( 'map_embed', array(
			'label'       => esc_html__( 'کد نقشه (iframe)', 'neomorph' ),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'description' => esc_html__( 'اختیاری — کد embed گوگل مپ یا نشان.', 'neomorph' ),
		) );

		$this->add_control( 'form_source', array(
			'label'   => esc_html__( 'منبع فرم', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'gravity',
			'options' => array(
				'gravity' => esc_html__( 'گراویتی فرم', 'neomorph' ),
				'native'  => esc_html__( 'فرم ساده داخلی', 'neomorph' ),
				'shortcode' => esc_html__( 'شورت‌کد دلخواه', 'neomorph' ),
			),
		) );
		$this->add_control( 'gravity_form', array(
			'label'       => esc_html__( 'شناسه فرم گراویتی', 'neomorph' ),
			'type'        => \Elementor\Controls_Manager::NUMBER,
			'condition'   => array( 'form_source' => 'gravity' ),
			'description' => esc_html__( 'با ارسال، به CRM افزونه Neomorph Core منتقل می‌شود.', 'neomorph' ),
		) );
		$this->add_control( 'custom_shortcode', array(
			'label'     => esc_html__( 'شورت‌کد فرم', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::TEXT,
			'condition' => array( 'form_source' => 'shortcode' ),
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'form_style', array(
			'label' => esc_html__( 'فرم', 'neomorph' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'input_bg', array(
			'label'     => esc_html__( 'رنگ ورودی', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .neo-input, {{WRAPPER}} .gform_wrapper input' => 'background: {{VALUE}};' ),
		) );
		$this->add_control( 'btn_bg', array(
			'label'     => esc_html__( 'رنگ دکمه ارسال', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .neo-btn--primary' => 'background: {{VALUE}}; color:#fff;' ),
		) );
		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل کلی', 'neomorph' ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="neo-widget neo-contact neo-contact--<?php echo esc_attr( $s['layout'] ); ?> <?php echo esc_attr( $this->neo_widget_class() ); ?>">
			<?php if ( $s['title'] ) : ?>
				<header class="neo-section__head">
					<h2 class="neo-section__title"><?php echo esc_html( $s['title'] ); ?></h2>
					<?php if ( $s['subtitle'] ) : ?><p class="neo-section__subtitle"><?php echo esc_html( $s['subtitle'] ); ?></p><?php endif; ?>
				</header>
			<?php endif; ?>

			<div class="neo-contact__grid">
				<div class="neo-contact__info">
					<?php if ( $s['phone'] ) : ?>
						<div class="neo-contact__card neo-surface"><span class="neo-contact__icon">📞</span><div><strong><?php esc_html_e( 'تلفن', 'neomorph' ); ?></strong><br><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $s['phone'] ) ); ?>"><?php echo esc_html( $s['phone'] ); ?></a></div></div>
					<?php endif; ?>
					<?php if ( $s['email'] ) : ?>
						<div class="neo-contact__card neo-surface"><span class="neo-contact__icon">✉️</span><div><strong><?php esc_html_e( 'ایمیل', 'neomorph' ); ?></strong><br><a href="mailto:<?php echo esc_attr( $s['email'] ); ?>"><?php echo esc_html( $s['email'] ); ?></a></div></div>
					<?php endif; ?>
					<?php if ( $s['address'] ) : ?>
						<div class="neo-contact__card neo-surface"><span class="neo-contact__icon">📍</span><div><strong><?php esc_html_e( 'آدرس', 'neomorph' ); ?></strong><br><?php echo esc_html( $s['address'] ); ?></div></div>
					<?php endif; ?>
					<?php if ( $s['map_embed'] ) : ?>
						<div class="neo-contact__map neo-inset"><?php echo wp_kses( $s['map_embed'], array( 'iframe' => array( 'src' => true, 'width' => true, 'height' => true, 'style' => true, 'loading' => true, 'allowfullscreen' => true, 'referrerpolicy' => true ) ) ); ?></div>
					<?php endif; ?>
				</div>

				<div class="neo-contact__form neo-inset">
					<?php
					if ( 'gravity' === $s['form_source'] && $s['gravity_form'] && class_exists( 'GFForms' ) ) {
						echo do_shortcode( '[gravityform id="' . (int) $s['gravity_form'] . '" title="false" description="false"]' ); // phpcs:ignore WordPress.Security.EscapeOutput
					} elseif ( 'shortcode' === $s['form_source'] && $s['custom_shortcode'] ) {
						echo do_shortcode( $s['custom_shortcode'] ); // phpcs:ignore WordPress.Security.EscapeOutput
					} else {
						?>
						<form class="neo-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<?php wp_nonce_field( 'neomorph_contact', 'neomorph_contact_nonce' ); ?>
							<input type="hidden" name="action" value="neomorph_contact">
							<div class="neo-form__row">
								<label class="neo-label"><?php esc_html_e( 'نام', 'neomorph' ); ?><input class="neo-input" type="text" name="neo_name" required></label>
								<label class="neo-label"><?php esc_html_e( 'ایمیل', 'neomorph' ); ?><input class="neo-input" type="email" name="neo_email" required></label>
							</div>
							<label class="neo-label"><?php esc_html_e( 'پیام', 'neomorph' ); ?><textarea class="neo-input neo-textarea" name="neo_message" rows="5" required></textarea></label>
							<button type="submit" class="neo-btn neo-btn--primary"><?php esc_html_e( 'ارسال پیام', 'neomorph' ); ?></button>
						</form>
						<?php
					}
					?>
				</div>
			</div>
		</section>
		<?php
	}
}
