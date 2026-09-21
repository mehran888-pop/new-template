<?php
/**
 * Widget: Neo Footer (فوتر قابل طراحی با المنتور).
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Footer
 */
class Widget_Footer extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-footer';
	}

	public function get_title() {
		return esc_html__( 'فوتر نئومورف', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-footer';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'layout', array(
			'label'   => esc_html__( 'چیدمان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '3col',
			'options' => array(
				'3col'      => esc_html__( 'سه ستونه', 'neomorph' ),
				'2col'      => esc_html__( 'دو ستونه', 'neomorph' ),
				'1col'      => esc_html__( 'تک ستونه', 'neomorph' ),
				'centered'  => esc_html__( 'وسط‌چین مینیمال', 'neomorph' ),
			),
		) );

		$this->add_control( 'about_text', array(
			'label'   => esc_html__( 'متن معرفی کوتاه', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
		) );
		$this->add_control( 'show_menu', array(
			'label'   => esc_html__( 'منوی فوتر', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'show_social', array(
			'label'   => esc_html__( 'شبکه‌های اجتماعی (از تنظیمات قالب)', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'show_newsletter', array(
			'label'   => esc_html__( 'فرم خبرنامه (گراویتی)', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
		) );
		$this->add_control( 'newsletter_form', array(
			'label'     => esc_html__( 'شناسه فرم خبرنامه', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'condition' => array( 'show_newsletter' => 'yes' ),
		) );
		$this->add_control( 'copyright', array(
			'label'   => esc_html__( 'متن کپی‌رایت', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '© {year} {site}',
		) );

		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل فوتر', 'neomorph' ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$copy = str_replace(
			array( '{year}', '{site}' ),
			array( gmdate( 'Y' ), get_bloginfo( 'name' ) ),
			$s['copyright']
		);
		?>
		<footer class="neo-widget neo-footer-widget neo-footer-widget--<?php echo esc_attr( $s['layout'] ); ?> <?php echo esc_attr( $this->neo_widget_class() ); ?>">
			<div class="neo-footer-widget__grid">
				<?php if ( $s['about_text'] ) : ?>
					<div class="neo-footer-widget__col neo-footer-widget__about">
						<p><?php echo esc_html( $s['about_text'] ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( 'yes' === $s['show_menu'] ) : ?>
					<nav class="neo-footer-widget__col neo-footer-widget__menu">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'container'      => false,
								'fallback_cb'    => false,
								'depth'          => 1,
							)
						);
						?>
					</nav>
				<?php endif; ?>

				<?php if ( 'yes' === $s['show_social'] ) : ?>
					<div class="neo-footer-widget__col neo-footer-widget__social">
						<?php
						$socials = (array) neomorph_option( 'socials', array() );
						foreach ( $socials as $social ) :
							if ( empty( $social['url'] ) ) {
								continue;
							}
							?>
							<a class="neo-social" href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( isset( $social['label'] ) ? $social['label'] : __( 'لینک', 'neomorph' ) ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( 'yes' === $s['show_newsletter'] && $s['newsletter_form'] && class_exists( 'GFForms' ) ) : ?>
					<div class="neo-footer-widget__col neo-footer-widget__newsletter">
						<?php echo do_shortcode( '[gravityform id="' . (int) $s['newsletter_form'] . '" title="false" description="false"]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="neo-footer-widget__bottom">
				<p class="neo-footer-widget__copy"><?php echo esc_html( $copy ); ?></p>
			</div>
		</footer>
		<?php
	}
}
