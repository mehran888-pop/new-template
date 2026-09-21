<?php
/**
 * المان اختصاصی فوتر (Footer Builder).
 *
 * @package Novin_AI
 */

namespace Novin_AI\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

/**
 * کلاس فوتر.
 */
class Footer extends Novin_AI_Widget_Base {

	/**
	 * نام المان.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'novin-footer';
	}

	/**
	 * عنوان المان.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'فوتر اختصاصی', 'novin-ai' );
	}

	/**
	 * آیکون المان.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-footer';
	}

	/**
	 * کلمات کلیدی.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords() {
		return array( 'footer', 'فوتر', 'پاورقی', 'copyright' );
	}

	/**
	 * ثبت کنترل‌ها.
	 *
	 * @return void
	 */
	protected function register_controls() {

		/* ---------------------------------- درباره ما ---------------------------------- */
		$this->start_controls_section(
			'section_about',
			array(
				'label' => esc_html__( 'ستون معرفی', 'novin-ai' ),
			)
		);

		$this->add_control(
			'show_about',
			array(
				'label'        => esc_html__( 'نمایش ستون معرفی', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'logo_type',
			array(
				'label'     => esc_html__( 'لوگو', 'novin-ai' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'theme',
				'options'   => array(
					'theme' => esc_html__( 'لوگوی سفارشی‌ساز', 'novin-ai' ),
					'image' => esc_html__( 'تصویر دلخواه', 'novin-ai' ),
					'text'  => esc_html__( 'متن', 'novin-ai' ),
					'none'  => esc_html__( 'بدون لوگو', 'novin-ai' ),
				),
				'condition' => array( 'show_about' => 'yes' ),
			)
		);

		$this->add_control(
			'logo_image',
			array(
				'label'     => esc_html__( 'تصویر لوگو', 'novin-ai' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'show_about' => 'yes',
					'logo_type'  => 'image',
				),
			)
		);

		$this->add_control(
			'logo_text',
			array(
				'label'     => esc_html__( 'متن لوگو', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => get_bloginfo( 'name' ),
				'condition' => array(
					'show_about' => 'yes',
					'logo_type'  => 'text',
				),
			)
		);

		$this->add_control(
			'about_text',
			array(
				'label'       => esc_html__( 'متن معرفی', 'novin-ai' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'شرکت نوین با تمرکز بر هوش مصنوعی، توسعه نرم‌افزار و زیرساخت ابری، راهکارهایی می‌سازد که کسب‌وکار شما را هوشمندتر می‌کند.', 'novin-ai' ),
				'label_block' => true,
				'rows'        => 5,
				'condition'   => array( 'show_about' => 'yes' ),
			)
		);

		$this->add_control(
			'contact_info',
			array(
				'label'       => esc_html__( 'اطلاعات تماس (هر خط یک مورد)', 'novin-ai' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => "تهران، خیابان ولیعصر، برج فناوری\n۰۲۱-۱۲۳۴۵۶۷۸\ninfo@novin-ai.ir",
				'label_block' => true,
				'rows'        => 4,
				'condition'   => array( 'show_about' => 'yes' ),
			)
		);

		$this->add_control(
			'show_socials',
			array(
				'label'        => esc_html__( 'نمایش شبکه‌های اجتماعی', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$socials = new Repeater();

		$socials->add_control(
			'network',
			array(
				'label'   => esc_html__( 'شبکه', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'instagram',
				'options' => novin_ai_social_networks(),
			)
		);

		$socials->add_control(
			'url',
			array(
				'label'       => esc_html__( 'لینک', 'novin-ai' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);

		$this->add_control(
			'socials',
			array(
				'label'       => esc_html__( 'شبکه‌های اجتماعی', 'novin-ai' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $socials->get_controls(),
				'default'     => array(
					array(
						'network' => 'instagram',
						'url'     => array( 'url' => '#' ),
					),
					array(
						'network' => 'telegram',
						'url'     => array( 'url' => '#' ),
					),
					array(
						'network' => 'linkedin',
						'url'     => array( 'url' => '#' ),
					),
					array(
						'network' => 'youtube',
						'url'     => array( 'url' => '#' ),
					),
				),
				'title_field' => '{{{ network }}}',
				'condition'   => array( 'show_socials' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- ستون‌های لینک ---------------------------------- */
		$this->start_controls_section(
			'section_columns',
			array(
				'label' => esc_html__( 'ستون‌های لینک', 'novin-ai' ),
			)
		);

		$columns = new Repeater();

		$columns->add_control(
			'title',
			array(
				'label'   => esc_html__( 'عنوان ستون', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'دسترسی سریع', 'novin-ai' ),
			)
		);

		$links = new Repeater();

		$links->add_control(
			'text',
			array(
				'label'   => esc_html__( 'متن لینک', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'صفحه اصلی', 'novin-ai' ),
			)
		);

		$links->add_control(
			'url',
			array(
				'label'       => esc_html__( 'لینک', 'novin-ai' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);

		$columns->add_control(
			'links',
			array(
				'label'   => esc_html__( 'لینک‌ها', 'novin-ai' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $links->get_controls(),
				'default' => array(
					array( 'text' => esc_html__( 'صفحه اصلی', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
					array( 'text' => esc_html__( 'خدمات', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
					array( 'text' => esc_html__( 'نمونه‌کارها', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
					array( 'text' => esc_html__( 'تماس با ما', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
				),
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'       => esc_html__( 'ستون‌ها', 'novin-ai' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $columns->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'title' => esc_html__( 'شرکت', 'novin-ai' ),
						'links' => array(
							array( 'text' => esc_html__( 'درباره ما', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
							array( 'text' => esc_html__( 'تیم ما', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
							array( 'text' => esc_html__( 'فرصت‌های شغلی', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
							array( 'text' => esc_html__( 'وبلاگ', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
						),
					),
					array(
						'title' => esc_html__( 'خدمات', 'novin-ai' ),
						'links' => array(
							array( 'text' => esc_html__( 'هوش مصنوعی', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
							array( 'text' => esc_html__( 'طراحی وب', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
							array( 'text' => esc_html__( 'پشتیبانی', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
							array( 'text' => esc_html__( 'امنیت شبکه', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
						),
					),
					array(
						'title' => esc_html__( 'پشتیبانی', 'novin-ai' ),
						'links' => array(
							array( 'text' => esc_html__( 'سوالات متداول', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
							array( 'text' => esc_html__( 'مستندات', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
							array( 'text' => esc_html__( 'وضعیت سرویس‌ها', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
							array( 'text' => esc_html__( 'حریم خصوصی', 'novin-ai' ), 'url' => array( 'url' => '#' ) ),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'footer_columns',
			array(
				'label'          => esc_html__( 'تعداد ستون', 'novin-ai' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '۱',
					'2' => '۲',
					'3' => '۳',
					'4' => '۴',
					'5' => '۵',
				),
				'selectors'      => array(
					'{{WRAPPER}} .nv-footer__grid' => '--nv-cols: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- خبرنامه ---------------------------------- */
		$this->start_controls_section(
			'section_newsletter',
			array(
				'label' => esc_html__( 'خبرنامه', 'novin-ai' ),
			)
		);

		$this->add_control(
			'show_newsletter',
			array(
				'label'        => esc_html__( 'نمایش فرم خبرنامه', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'newsletter_title',
			array(
				'label'     => esc_html__( 'عنوان', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'در جریان تکنولوژی باشید', 'novin-ai' ),
				'condition' => array( 'show_newsletter' => 'yes' ),
			)
		);

		$this->add_control(
			'newsletter_desc',
			array(
				'label'     => esc_html__( 'توضیح', 'novin-ai' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => esc_html__( 'هر هفته یک ایمیل کوتاه درباره هوش مصنوعی و مهندسی نرم‌افزار.', 'novin-ai' ),
				'condition' => array( 'show_newsletter' => 'yes' ),
			)
		);

		$this->add_control(
			'newsletter_placeholder',
			array(
				'label'     => esc_html__( 'متن پیش‌فرض فیلد', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'ایمیل شما', 'novin-ai' ),
				'condition' => array( 'show_newsletter' => 'yes' ),
			)
		);

		$this->add_control(
			'newsletter_button',
			array(
				'label'     => esc_html__( 'متن دکمه', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'عضویت', 'novin-ai' ),
				'condition' => array( 'show_newsletter' => 'yes' ),
			)
		);

		$this->add_control(
			'newsletter_shortcode',
			array(
				'label'       => esc_html__( 'یا شورت‌کد فرم دلخواه', 'novin-ai' ),
				'description' => esc_html__( 'در صورت پر کردن این فیلد، فرم پیش‌فرض نمایش داده نمی‌شود.', 'novin-ai' ),
				'type'        => Controls_Manager::TEXT,
				'condition'   => array( 'show_newsletter' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- نوار پایین ---------------------------------- */
		$this->start_controls_section(
			'section_bottom',
			array(
				'label' => esc_html__( 'نوار پایین', 'novin-ai' ),
			)
		);

		$this->add_control(
			'copyright',
			array(
				'label'       => esc_html__( 'متن کپی‌رایت', 'novin-ai' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '© ' . gmdate( 'Y' ) . ' ' . esc_html__( 'تمامی حقوق محفوظ است.', 'novin-ai' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'bottom_menu',
			array(
				'label'   => esc_html__( 'منوی نوار پایین', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'options' => novin_ai_get_menus(),
				'default' => '',
			)
		);

		$this->add_control(
			'show_back_to_top',
			array(
				'label'        => esc_html__( 'دکمه بازگشت به بالا', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- استایل‌ها ---------------------------------- */
		$this->start_controls_section(
			'section_footer_style',
			array(
				'label' => esc_html__( 'استایل فوتر', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'footer_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .nv-footer',
			)
		);

		$this->add_responsive_control(
			'footer_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'novin-ai' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'footer_title_typography',
				'label'    => esc_html__( 'تایپوگرافی عناوین ستون', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-footer__title',
			)
		);

		$this->add_control(
			'footer_title_color',
			array(
				'label'     => esc_html__( 'رنگ عناوین', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-footer__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'footer_link_typography',
				'label'    => esc_html__( 'تایپوگرافی لینک‌ها', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-footer__links a',
			)
		);

		$this->add_control(
			'footer_link_color',
			array(
				'label'     => esc_html__( 'رنگ لینک‌ها', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-footer__links a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'footer_link_hover',
			array(
				'label'     => esc_html__( 'رنگ لینک در هاور', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-footer__links a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'footer_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-footer__about p, {{WRAPPER}} .nv-footer__bottom' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'footer_bottom_border',
				'label'          => esc_html__( 'خط نوار پایین', 'novin-ai' ),
				'selector'       => '{{WRAPPER}} .nv-footer__bottom',
				'fields_options' => array(
					'border' => array( 'default' => 'solid' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * رندر خروجی.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'nv-footer nv-footer--widget' );
		?>
		<footer <?php $this->nv_attr( 'wrapper' ); ?>>
			<span class="nv-orb nv-orb--1" aria-hidden="true"></span>
			<span class="nv-orb nv-orb--2" aria-hidden="true"></span>

			<div class="nv-container">
				<div class="nv-footer__grid">

					<?php if ( 'yes' === $settings['show_about'] ) : ?>
						<div class="nv-footer__col nv-footer__col--about">
							<a class="nv-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<?php
								if ( 'image' === $settings['logo_type'] && ! empty( $settings['logo_image']['url'] ) ) {
									echo '<img src="' . esc_url( $settings['logo_image']['url'] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" loading="lazy">';
								} elseif ( 'text' === $settings['logo_type'] ) {
									echo '<span class="nv-logo__text">' . esc_html( $settings['logo_text'] ) . '</span>';
								} elseif ( 'theme' === $settings['logo_type'] && has_custom_logo() ) {
									echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'loading' => 'lazy' ) );
								} else {
									echo '<span class="nv-logo__text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
								}
								?>
							</a>

							<?php if ( ! empty( $settings['about_text'] ) ) : ?>
								<p class="nv-footer__about-text"><?php echo esc_html( $settings['about_text'] ); ?></p>
							<?php endif; ?>

							<?php
							$contacts = novin_ai_meta_lines( $settings['contact_info'] );

							if ( ! empty( $contacts ) ) :
								?>
								<ul class="nv-footer__contact">
									<?php foreach ( $contacts as $contact ) : ?>
										<li><?php echo esc_html( $contact ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<?php if ( 'yes' === $settings['show_socials'] && ! empty( $settings['socials'] ) ) : ?>
								<div class="nv-socials">
									<?php
									foreach ( $settings['socials'] as $item ) {
										if ( empty( $item['url']['url'] ) ) {
											continue;
										}

										printf(
											'<a class="nv-social nv-social--%1$s" href="%2$s" aria-label="%3$s" target="%4$s" rel="%5$s">%6$s</a>',
											esc_attr( $item['network'] ),
											esc_url( $item['url']['url'] ),
											esc_attr( $item['network'] ),
											! empty( $item['url']['is_external'] ) ? '_blank' : '_self',
											! empty( $item['url']['nofollow'] ) ? 'nofollow' : '',
											novin_ai_social_icon( $item['network'] ) // phpcs:ignore WordPress.Security.EscapeOutput
										);
									}
									?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $settings['columns'] ) ) : ?>
						<?php foreach ( $settings['columns'] as $column ) : ?>
							<div class="nv-footer__col">
								<?php if ( ! empty( $column['title'] ) ) : ?>
									<h4 class="nv-footer__title"><?php echo esc_html( $column['title'] ); ?></h4>
								<?php endif; ?>

								<?php if ( ! empty( $column['links'] ) ) : ?>
									<ul class="nv-footer__links">
										<?php foreach ( $column['links'] as $link ) : ?>
											<?php if ( empty( $link['text'] ) ) { continue; } ?>
											<li>
												<?php $this->nv_link_open( $link, 'url' ); ?>
												<?php echo esc_html( $link['text'] ); ?>
												<?php $this->nv_link_close( $link, 'url' ); ?>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>

					<?php if ( 'yes' === $settings['show_newsletter'] ) : ?>
						<div class="nv-footer__col nv-footer__col--newsletter">
							<?php if ( ! empty( $settings['newsletter_title'] ) ) : ?>
								<h4 class="nv-footer__title"><?php echo esc_html( $settings['newsletter_title'] ); ?></h4>
							<?php endif; ?>

							<?php if ( ! empty( $settings['newsletter_desc'] ) ) : ?>
								<p class="nv-footer__about-text"><?php echo esc_html( $settings['newsletter_desc'] ); ?></p>
							<?php endif; ?>

							<?php if ( ! empty( $settings['newsletter_shortcode'] ) ) : ?>
								<?php echo do_shortcode( $settings['newsletter_shortcode'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php else : ?>
								<form class="nv-newsletter" method="post" data-nv-newsletter>
									<input type="email" name="email" placeholder="<?php echo esc_attr( $settings['newsletter_placeholder'] ); ?>" required>
									<button type="submit">
										<span><?php echo esc_html( $settings['newsletter_button'] ); ?></span>
										<?php echo novin_ai_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									</button>
									<span class="nv-newsletter__msg" aria-live="polite"></span>
								</form>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="nv-footer__bottom">
					<p class="nv-footer__copyright">
						<?php echo wp_kses_post( $settings['copyright'] ); ?>
					</p>

					<?php
					if ( ! empty( $settings['bottom_menu'] ) ) {
						wp_nav_menu(
							array(
								'menu'       => $settings['bottom_menu'],
								'menu_class' => 'nv-menu nv-menu--inline',
								'container'  => false,
								'fallback_cb' => false,
							)
						);
					}
					?>

					<?php if ( 'yes' === $settings['show_back_to_top'] ) : ?>
						<button class="nv-scroll-top nv-scroll-top--inline" type="button" aria-label="<?php esc_attr_e( 'بازگشت به بالا', 'novin-ai' ); ?>">
							<?php echo novin_ai_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</button>
					<?php endif; ?>
				</div>
			</div>
		</footer>
		<?php
	}
}
