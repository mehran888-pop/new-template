<?php
/**
 * المان اختصاصی هدر (Header Builder).
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
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

/**
 * کلاس هدر.
 */
class Header extends Novin_AI_Widget_Base {

	/**
	 * نام المان.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'novin-header';
	}

	/**
	 * عنوان المان.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'هدر اختصاصی', 'novin-ai' );
	}

	/**
	 * آیکون المان.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-header';
	}

	/**
	 * کلمات کلیدی.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords() {
		return array( 'header', 'هدر', 'منو', 'menu', 'nav', 'سربرگ' );
	}

	/**
	 * ثبت کنترل‌ها.
	 *
	 * @return void
	 */
	protected function register_controls() {

		/* ---------------------------------- محتوا ---------------------------------- */
		$this->start_controls_section(
			'section_logo',
			array(
				'label' => esc_html__( 'لوگو و برند', 'novin-ai' ),
			)
		);

		$this->add_control(
			'logo_type',
			array(
				'label'   => esc_html__( 'نوع لوگو', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'theme',
				'options' => array(
					'theme' => esc_html__( 'لوگوی سفارشی‌ساز وردپرس', 'novin-ai' ),
					'image' => esc_html__( 'تصویر دلخواه', 'novin-ai' ),
					'text'  => esc_html__( 'متن', 'novin-ai' ),
				),
			)
		);

		$this->add_control(
			'logo_image',
			array(
				'label'     => esc_html__( 'تصویر لوگو', 'novin-ai' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'logo_type' => 'image' ),
			)
		);

		$this->add_control(
			'logo_text',
			array(
				'label'     => esc_html__( 'متن لوگو', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => get_bloginfo( 'name' ),
				'condition' => array( 'logo_type' => 'text' ),
			)
		);

		$this->add_control(
			'logo_tagline',
			array(
				'label'     => esc_html__( 'زیرعنوان لوگو', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'هوش مصنوعی و نرم‌افزار', 'novin-ai' ),
				'condition' => array( 'logo_type' => 'text' ),
			)
		);

		$this->add_responsive_control(
			'logo_width',
			array(
				'label'      => esc_html__( 'عرض لوگو', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 40,
						'max' => 400,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-logo img' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- منو ---------------------------------- */
		$this->start_controls_section(
			'section_menu',
			array(
				'label' => esc_html__( 'منو و ناوبری', 'novin-ai' ),
			)
		);

		$this->add_control(
			'menu',
			array(
				'label'   => esc_html__( 'منوی اصلی', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'options' => novin_ai_get_menus(),
				'default' => '',
			)
		);

		$this->add_control(
			'mobile_menu',
			array(
				'label'   => esc_html__( 'منوی موبایل', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'options' => novin_ai_get_menus(),
				'default' => '',
			)
		);

		$this->add_control(
			'sticky',
			array(
				'label'        => esc_html__( 'هدر چسبان', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'transparent',
			array(
				'label'        => esc_html__( 'شفاف روی محتوا', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'mobile_breakpoint',
			array(
				'label'       => esc_html__( 'نقطه شکست موبایل (px)', 'novin-ai' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1024,
				'description' => esc_html__( 'در عرض کمتر از این مقدار، منو به حالت آف‌کنوس تبدیل می‌شود.', 'novin-ai' ),
			)
		);

		$this->add_responsive_control(
			'header_height',
			array(
				'label'      => esc_html__( 'ارتفاع هدر', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-header__inner' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- اکشن‌ها ---------------------------------- */
		$this->start_controls_section(
			'section_actions',
			array(
				'label' => esc_html__( 'عناصر سمت چپ', 'novin-ai' ),
			)
		);

		$this->add_control(
			'show_search',
			array(
				'label'        => esc_html__( 'آیکون جستجو', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'search_placeholder',
			array(
				'label'     => esc_html__( 'متن جستجو', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'جستجو کنید…', 'novin-ai' ),
				'condition' => array( 'show_search' => 'yes' ),
			)
		);

		$this->add_control(
			'show_cart',
			array(
				'label'        => esc_html__( 'سبد خرید (ووکامرس)', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => class_exists( 'WooCommerce' ) ? 'yes' : '',
			)
		);

		$this->end_controls_section();

		// بخش دکمه اقدام و استایل آن.
		$this->button_controls( esc_html__( 'دکمه اقدام', 'novin-ai' ), 'cta' );

		/* ---------------------------------- آف‌کنوس ---------------------------------- */
		$this->start_controls_section(
			'section_offcanvas',
			array(
				'label' => esc_html__( 'منوی موبایل (آف‌کنوس)', 'novin-ai' ),
			)
		);

		$this->add_control(
			'offcanvas_note',
			array(
				'label'       => esc_html__( 'توضیح کوتاه', 'novin-ai' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'ما به کسب‌وکارها کمک می‌کنیم با هوش مصنوعی سریع‌تر رشد کنند.', 'novin-ai' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_offcanvas_socials',
			array(
				'label'        => esc_html__( 'نمایش شبکه‌های اجتماعی', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'network',
			array(
				'label'   => esc_html__( 'شبکه', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'instagram',
				'options' => novin_ai_social_networks(),
			)
		);

		$repeater->add_control(
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
				'fields'      => $repeater->get_controls(),
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
				),
				'title_field' => '{{{ network }}}',
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- استایل‌ها ---------------------------------- */
		$this->start_controls_section(
			'section_header_style',
			array(
				'label' => esc_html__( 'استایل هدر', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'header_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .nv-header',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'header_border',
				'selector' => '{{WRAPPER}} .nv-header',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'header_shadow',
				'selector' => '{{WRAPPER}} .nv-header',
			)
		);

		$this->add_control(
			'header_blur',
			array(
				'label'      => esc_html__( 'میزان ماتی (Blur)', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-header' => 'backdrop-filter: blur({{SIZE}}{{UNIT}}); -webkit-backdrop-filter: blur({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->add_responsive_control(
			'header_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'novin-ai' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-header__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'sticky_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه حالت اسکرول', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array( 'sticky' => 'yes' ),
				'selectors' => array(
					'{{WRAPPER}} .nv-header.is-scrolled' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- استایل منو ---------------------------------- */
		$this->start_controls_section(
			'section_menu_style',
			array(
				'label' => esc_html__( 'استایل منو', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'menu_typography',
				'selector' => '{{WRAPPER}} .nv-menu > li > a',
			)
		);

		$this->add_control(
			'menu_color',
			array(
				'label'     => esc_html__( 'رنگ', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-menu > li > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'menu_hover_color',
			array(
				'label'     => esc_html__( 'رنگ هاور', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-menu > li > a:hover, {{WRAPPER}} .nv-menu > li.current-menu-item > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'divider_width',
			array(
				'label'      => esc_html__( 'عرض خط زیر منو', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-menu > li > a::after' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'divider_color',
			array(
				'label'     => esc_html__( 'رنگ خط زیر منو', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-menu > li > a::after' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'menu_gap',
			array(
				'label'      => esc_html__( 'فاصله بین آیتم‌ها', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-menu' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'submenu_background',
				'label'    => esc_html__( 'پس‌زمینه زیرمنو', 'novin-ai' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .nv-menu .sub-menu',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'submenu_typography',
				'label'    => esc_html__( 'تایپوگرافی زیرمنو', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-menu .sub-menu a',
			)
		);

		$this->add_control(
			'submenu_color',
			array(
				'label'     => esc_html__( 'رنگ زیرمنو', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-menu .sub-menu a' => 'color: {{VALUE}};',
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
		$id       = $this->get_id();

		$classes = array( 'nv-header', 'nv-header--widget' );

		if ( 'yes' === $settings['sticky'] ) {
			$classes[] = 'is-sticky';
		}

		if ( 'yes' === $settings['transparent'] ) {
			$classes[] = 'nv-header--transparent';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );
		$this->add_render_attribute( 'wrapper', 'data-nv-header', '1' );

		$bp = ! empty( $settings['mobile_breakpoint'] ) ? (int) $settings['mobile_breakpoint'] : 1024;
		?>

		<?php if ( $bp ) : ?>
			<style>
				@media (max-width: <?php echo (int) $bp; ?>px) {
					.elementor-element-<?php echo esc_attr( $id ); ?> .nv-header__nav { display: none !important; }
					.elementor-element-<?php echo esc_attr( $id ); ?> .nv-burger { display: inline-flex !important; }
				}
			</style>
		<?php endif; ?>

		<header <?php $this->nv_attr( 'wrapper' ); ?>>
			<div class="nv-container nv-header__inner">

				<div class="nv-header__brand">
					<a class="nv-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<?php
						if ( 'image' === $settings['logo_type'] && ! empty( $settings['logo_image']['url'] ) ) {
							echo '<img src="' . esc_url( $settings['logo_image']['url'] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" loading="lazy">';
						} elseif ( 'text' === $settings['logo_type'] ) {
							echo '<span class="nv-logo__text">' . esc_html( $settings['logo_text'] ) . '</span>';

							if ( ! empty( $settings['logo_tagline'] ) ) {
								echo '<span class="nv-logo__tagline">' . esc_html( $settings['logo_tagline'] ) . '</span>';
							}
						} elseif ( has_custom_logo() ) {
							$logo_id = get_theme_mod( 'custom_logo' );
							echo wp_get_attachment_image( $logo_id, 'full', false, array( 'loading' => 'lazy' ) );
						} else {
							echo '<span class="nv-logo__text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
						}
						?>
					</a>
				</div>

				<nav class="nv-header__nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'novin-ai' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'menu'       => $settings['menu'],
							'menu_class' => 'nv-menu',
							'container'  => false,
							'fallback_cb' => false,
						)
					);
					?>
				</nav>

				<div class="nv-header__actions">
					<?php if ( 'yes' === $settings['show_search'] ) : ?>
						<button class="nv-icon-btn" type="button" data-nv-toggle="nv-search-<?php echo esc_attr( $id ); ?>" aria-label="<?php esc_attr_e( 'جستجو', 'novin-ai' ); ?>">
							<?php echo novin_ai_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</button>
					<?php endif; ?>

					<?php if ( 'yes' === $settings['show_cart'] && class_exists( 'WooCommerce' ) ) : ?>
						<a class="nv-icon-btn" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'سبد خرید', 'novin-ai' ); ?>">
							<?php echo novin_ai_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="nv-icon-btn__count"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
						</a>
					<?php endif; ?>

					<?php $this->render_button( $settings, 'cta', 'nv-btn--sm' ); ?>

					<button class="nv-burger" type="button" aria-expanded="false" aria-label="<?php esc_attr_e( 'منو', 'novin-ai' ); ?>" data-nv-offcanvas-open="nv-offcanvas-<?php echo esc_attr( $id ); ?>">
						<?php echo novin_ai_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</button>
				</div>
			</div>

			<?php if ( 'yes' === $settings['show_search'] ) : ?>
				<div class="nv-search-panel" id="nv-search-<?php echo esc_attr( $id ); ?>">
					<form role="search" method="get" class="nv-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<input type="search" name="s" placeholder="<?php echo esc_attr( $settings['search_placeholder'] ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
						<button type="submit" aria-label="<?php esc_attr_e( 'جستجو', 'novin-ai' ); ?>"><?php echo novin_ai_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
					</form>
				</div>
			<?php endif; ?>
		</header>

		<div class="nv-offcanvas nv-offcanvas--end" id="nv-offcanvas-<?php echo esc_attr( $id ); ?>" aria-hidden="true">
			<div class="nv-offcanvas__backdrop" data-nv-offcanvas-close></div>
			<div class="nv-offcanvas__panel nv-glass">
				<button class="nv-offcanvas__close" type="button" aria-label="<?php esc_attr_e( 'بستن', 'novin-ai' ); ?>" data-nv-offcanvas-close>
					<?php echo novin_ai_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>

				<?php if ( ! empty( $settings['offcanvas_note'] ) ) : ?>
					<p class="nv-offcanvas__note"><?php echo esc_html( $settings['offcanvas_note'] ); ?></p>
				<?php endif; ?>

				<nav aria-label="<?php esc_attr_e( 'منوی موبایل', 'novin-ai' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'menu'        => ! empty( $settings['mobile_menu'] ) ? $settings['mobile_menu'] : $settings['menu'],
							'menu_class'  => 'nv-menu nv-menu--stack',
							'container'   => false,
							'fallback_cb' => false,
						)
					);
					?>
				</nav>

				<?php if ( 'yes' === $settings['show_offcanvas_socials'] && ! empty( $settings['socials'] ) ) : ?>
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
		</div>
		<?php
	}
}
