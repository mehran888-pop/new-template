<?php
/**
 * Theme options panel (تنظیمات قالب) — Settings API based.
 *
 * Sections: Typography / Colors / Layout / Header & Footer / Blog / Product / Careers / Interview / Social / Integrations hint.
 *
 * @package Neomorph
 */

namespace Neomorph\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Options
 */
final class Options {

	const PAGE  = 'neomorph-settings';
	const OPT   = 'neomorph_options';

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
		add_action( 'admin_notices', array( __CLASS__, 'notices' ) );
	}

	/**
	 * Default option values (single source of truth).
	 */
	public static function defaults() {
		return array(
			// Typography.
			'font_body'            => 'vazirmatn',
			'font_heading'         => 'vazirmatn',
			'font_size_base'       => '16',
			// Colors.
			'color_bg'             => '#e8edf5',
			'color_text'           => '#2f3542',
			'color_muted'          => '#7b8494',
			'color_accent'         => '#6c5ce7',
			'color_accent_2'       => '#00b894',
			// Neumorphism engine.
			'radius'               => '24',
			'shadow_distance'      => '8',
			'shadow_softness'      => '2',
			'soft_style'           => 'light', // light|dark.
			// Layout.
			'site_layout'          => 'wide', // wide|boxed.
			'container_width'      => '1200',
			'header_layout'        => 'classic', // classic|centered|minimal.
			'header_sticky'        => '1',
			'footer_layout'        => '3col', // 3col|2col|1col.
			'page_layout'          => 'content', // content|sidebar.
			'archive_layout'       => 'content',
			'show_breadcrumbs'     => '1',
			// Blog.
			'blog_style'           => 'grid', // grid|list|masonry|list-wide.
			'blog_excerpt_length'  => '22',
			'single_layout'        => 'standard', // standard|compact|fullwidth-media.
			// Product (WooCommerce).
			'shop_columns'         => '3',
			'product_layout'       => 'gallery-right', // gallery-right|gallery-left|gallery-center|sticky-info.
			'product_card_style'   => 'raised', // raised|soft-border|inset-media.
			'catalog_mode'         => '0',
			'currency'             => 'تومان',
			'price_separator'      => ',',
			// Careers / Recruitment.
			'careers_intro'        => '',
			'careers_form_title'   => 'فرم درخواست همکاری',
			'careers_style'        => 'wizard', // wizard|vertical|cards.
			// Video interview.
			'interview_title'      => 'مصاحبه ویدیویی',
			'interview_provider'   => 'self', // self|aparat|video-url.
			'interview_bg'         => '',
			// Contacts / social.
			'socials'              => array(),
			'contact_phone'        => '',
			'contact_email'        => '',
			'contact_address'      => '',
			// Fallback hero (no-Elementor homepage).
			'fallback_hero_title'  => '',
			'fallback_hero_subtitle' => '',
			'fallback_hero_btn'    => '',
			'fallback_hero_link'   => '',
			// Pages mapping.
			'panel_page'           => 0,
			'invoice_page'         => 0,
			'login_page'           => 0,
			'register_page'        => 0,
			'shop_page'            => 0,
			'blog_page'            => 0,
		);
	}

	/**
	 * Admin menu (under Appearance).
	 */
	public static function menu() {
		add_theme_page(
			esc_html__( 'تنظیمات قالب نئومورف', 'neomorph' ),
			esc_html__( 'تنظیمات نئومورف', 'neomorph' ),
			'manage_options',
			self::PAGE,
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Settings API registration — single option; fields rendered by custom pro UI.
	 */
	public static function register() {
		register_setting(
			self::OPT,
			self::OPT,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);
	}

	/**
	 * Tab map: slug => title + fields (key => label/type/choices).
	 */
	public static function sections() {
		return array(
			'typography' => array(
				'title'  => esc_html__( 'تایپوگرافی و فونت', 'neomorph' ),
				'fields' => array(
					'font_body'      => array(
						'label'   => esc_html__( 'فونت متن', 'neomorph' ),
						'type'    => 'font',
					),
					'font_heading'   => array(
						'label'   => esc_html__( 'فونت تیترها', 'neomorph' ),
						'type'    => 'font',
					),
					'font_size_base' => array(
						'label' => esc_html__( 'اندازه پایه (px)', 'neomorph' ),
						'type'  => 'number',
					),
				),
			),
			'colors'     => array(
				'title'  => esc_html__( 'رنگ‌بندی', 'neomorph' ),
				'fields' => array(
					'color_bg'       => array(
						'label' => esc_html__( 'رنگ پس‌زمینه (پایه نئو)', 'neomorph' ),
						'type'  => 'color',
					),
					'color_text'     => array(
						'label' => esc_html__( 'رنگ متن', 'neomorph' ),
						'type'  => 'color',
					),
					'color_muted'    => array(
						'label' => esc_html__( 'رنگ متن ثانویه', 'neomorph' ),
						'type'  => 'color',
					),
					'color_accent'   => array(
						'label' => esc_html__( 'رنگ اصلی (اکشن)', 'neomorph' ),
						'type'  => 'color',
					),
					'color_accent_2' => array(
						'label' => esc_html__( 'رنگ مکمل', 'neomorph' ),
						'type'  => 'color',
					),
				),
			),
			'neo'        => array(
				'title'  => esc_html__( 'موتور نئومورفیسم', 'neomorph' ),
				'fields' => array(
					'radius'          => array(
						'label'       => esc_html__( 'گردی گوشه‌ها (px)', 'neomorph' ),
						'type'        => 'number',
						'description' => esc_html__( 'هرچه بزرگ‌تر، احساس نرمی بیشتر.', 'neomorph' ),
					),
					'shadow_distance' => array(
						'label' => esc_html__( 'فاصله سایه (px)', 'neomorph' ),
						'type'  => 'number',
					),
					'shadow_softness' => array(
						'label' => esc_html__( 'نرمی سایه (ضریب بلور)', 'neomorph' ),
						'type'  => 'number',
					),
					'soft_style'      => array(
						'label'   => esc_html__( 'سبک کلی', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'light' => esc_html__( 'نئو روشن', 'neomorph' ),
							'dark'  => esc_html__( 'نئو تیره', 'neomorph' ),
						),
					),
				),
			),
			'layout'     => array(
				'title'        => esc_html__( 'چیدمان و لایوت', 'neomorph' ),
				'fields'       => array(
					'site_layout'      => array(
						'label'   => esc_html__( 'چیدمان سایت', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'wide'   => esc_html__( 'تمام‌عرض', 'neomorph' ),
							'boxed'  => esc_html__( 'جعبه‌ای', 'neomorph' ),
						),
					),
					'container_width'  => array(
						'label' => esc_html__( 'عرض محتوا (px)', 'neomorph' ),
						'type'  => 'number',
					),
					'page_layout'      => array(
						'label'   => esc_html__( 'چیدمان صفحات', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'content' => esc_html__( 'فقط محتوا', 'neomorph' ),
							'sidebar' => esc_html__( 'محتوا + سایدبار', 'neomorph' ),
						),
					),
					'archive_layout'   => array(
						'label'   => esc_html__( 'چیدمان آرشیو/بلاگ', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'content' => esc_html__( 'فقط محتوا', 'neomorph' ),
							'sidebar' => esc_html__( 'محتوا + سایدبار', 'neomorph' ),
						),
					),
					'show_breadcrumbs' => array(
						'label'   => esc_html__( 'مسیر راهنما (Breadcrumb)', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'1' => esc_html__( 'فعال', 'neomorph' ),
							'0' => esc_html__( 'غیرفعال', 'neomorph' ),
						),
					),
				),
			),
			'header'     => array(
				'title'  => esc_html__( 'هدر و فوتر', 'neomorph' ),
				'fields' => array(
					'header_layout' => array(
						'label'   => esc_html__( 'چیدمان هدر', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'classic'   => esc_html__( 'کلاسیک (لوگو راست، منو چپ)', 'neomorph' ),
							'centered'  => esc_html__( 'وسط‌چین (لوگو بالا، منو پایین)', 'neomorph' ),
							'minimal'   => esc_html__( 'مینیمال (بدون منوی موبایل ثابت)', 'neomorph' ),
						),
					),
					'header_sticky' => array(
						'label'   => esc_html__( 'هدر چسبان', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'1' => esc_html__( 'فعال', 'neomorph' ),
							'0' => esc_html__( 'غیرفعال', 'neomorph' ),
						),
					),
					'footer_layout' => array(
						'label'   => esc_html__( 'چیدمان فوتر', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'3col' => esc_html__( 'سه ستونه', 'neomorph' ),
							'2col' => esc_html__( 'دو ستونه', 'neomorph' ),
							'1col' => esc_html__( 'تک ستونه', 'neomorph' ),
						),
					),
				),
			),
			'blog'       => array(
				'title'  => esc_html__( 'صفحه مقالات و آموزش', 'neomorph' ),
				'fields' => array(
					'blog_style'          => array(
						'label'   => esc_html__( 'سبک نمایش مقالات', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'grid'       => esc_html__( 'کارت‌های گرید', 'neomorph' ),
							'list'       => esc_html__( 'لیست', 'neomorph' ),
							'masonry'    => esc_html__( 'ماسونری', 'neomorph' ),
							'list-wide'  => esc_html__( 'لیست عریض', 'neomorph' ),
						),
					),
					'blog_excerpt_length' => array(
						'label' => esc_html__( 'تعداد کلمات خلاصه', 'neomorph' ),
						'type'  => 'number',
					),
					'single_layout'       => array(
						'label'   => esc_html__( 'چیدمان مقاله تکی', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'standard'         => esc_html__( 'استاندارد', 'neomorph' ),
							'compact'          => esc_html__( 'جمع‌وجور', 'neomorph' ),
							'fullwidth-media'  => esc_html__( 'رسانه تمام‌عرض', 'neomorph' ),
						),
					),
				),
			),
			'product'    => array(
				'title'  => esc_html__( 'فروشگاه و صفحه محصول', 'neomorph' ),
				'fields' => array(
					'shop_columns'       => array(
						'label'   => esc_html__( 'تعداد ستون‌های فروشگاه', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'2' => '2',
							'3' => '3',
							'4' => '4',
						),
					),
					'product_layout'     => array(
						'label'   => esc_html__( 'چیدمان صفحه محصول', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'gallery-right'  => esc_html__( 'گالری راست', 'neomorph' ),
							'gallery-left'   => esc_html__( 'گالری چپ', 'neomorph' ),
							'gallery-center' => esc_html__( 'گالری وسط + اطلاعات پایین', 'neomorph' ),
							'sticky-info'    => esc_html__( 'گالری راست + اطلاعات چسبان', 'neomorph' ),
						),
					),
					'product_card_style' => array(
						'label'   => esc_html__( 'سبک کارت محصول', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'raised'       => esc_html__( 'برآمده (Raised)', 'neomorph' ),
							'soft-border'  => esc_html__( 'حاشیه نرم', 'neomorph' ),
							'inset-media'  => esc_html__( 'رسانه فرورفته', 'neomorph' ),
						),
					),
					'catalog_mode'       => array(
						'label'   => esc_html__( 'حالت کاتالوگ (بدون سبد)', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'0' => esc_html__( 'غیرفعال', 'neomorph' ),
							'1' => esc_html__( 'فعال', 'neomorph' ),
						),
					),
					'currency'           => array(
						'label' => esc_html__( 'واحد پول', 'neomorph' ),
						'type'  => 'text',
					),
					'price_separator'    => array(
						'label' => esc_html__( 'جداکننده هزارگان', 'neomorph' ),
						'type'  => 'text',
					),
				),
			),
			'careers'    => array(
				'title'  => esc_html__( 'صفحه استخدام', 'neomorph' ),
				'fields' => array(
					'careers_intro'      => array(
						'label' => esc_html__( 'متن معرفی بالای لیست مشاغل', 'neomorph' ),
						'type'  => 'textarea',
					),
					'careers_form_title' => array(
						'label' => esc_html__( 'عنوان فرم استخدام', 'neomorph' ),
						'type'  => 'text',
					),
					'careers_style'      => array(
						'label'   => esc_html__( 'سبک فرم استخدام', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'wizard'   => esc_html__( 'مرحله‌ای (Wizard)', 'neomorph' ),
							'vertical' => esc_html__( 'عمودی یک‌صفحه‌ای', 'neomorph' ),
							'cards'    => esc_html__( 'کارت‌های گام‌به‌گام', 'neomorph' ),
						),
					),
				),
			),
			'interview'  => array(
				'title'  => esc_html__( 'صفحه مصاحبه ویدیویی', 'neomorph' ),
				'fields' => array(
					'interview_title'    => array(
						'label' => esc_html__( 'عنوان صفحه مصاحبه', 'neomorph' ),
						'type'  => 'text',
					),
					'interview_provider' => array(
						'label'   => esc_html__( 'منبع ویدیو', 'neomorph' ),
						'type'    => 'select',
						'choices' => array(
							'self'      => esc_html__( 'آپلود در رسانه وردپرس', 'neomorph' ),
							'aparat'    => esc_html__( 'آپارات (لینک)', 'neomorph' ),
							'video-url' => esc_html__( 'هر لینک ویدیو', 'neomorph' ),
						),
					),
					'interview_bg'       => array(
						'label'       => esc_html__( 'شناسه رسانه پس‌زمینه', 'neomorph' ),
						'type'        => 'number',
						'description' => esc_html__( 'اختیاری — شناسه تصویر از کتابخانه رسانه.', 'neomorph' ),
					),
				),
			),
			'contact'    => array(
				'title'  => esc_html__( 'اطلاعات تماس و شبکه‌های اجتماعی', 'neomorph' ),
				'fields' => array(
					'contact_phone'   => array(
						'label' => esc_html__( 'تلفن', 'neomorph' ),
						'type'  => 'text',
					),
					'contact_email'   => array(
						'label' => esc_html__( 'ایمیل', 'neomorph' ),
						'type'  => 'email',
					),
					'contact_address' => array(
						'label' => esc_html__( 'آدرس', 'neomorph' ),
						'type'  => 'textarea',
					),
				),
			),
			'pages'      => array(
				'title'  => esc_html__( 'صفحات سیستم', 'neomorph' ),
				'fields' => array(
					'panel_page'   => array(
						'label'       => esc_html__( 'صفحه پنل مشتریان', 'neomorph' ),
						'type'        => 'page',
						'description' => esc_html__( 'قالب «پنل مشتریان (Neomorph)» را روی آن بگذارید.', 'neomorph' ),
					),
					'login_page'   => array(
						'label' => esc_html__( 'صفحه ورود (OTP)', 'neomorph' ),
						'type'  => 'page',
					),
					'register_page' => array(
						'label' => esc_html__( 'صفحه ثبت‌نام', 'neomorph' ),
						'type'  => 'page',
					),
					'invoice_page' => array(
						'label' => esc_html__( 'صفحه فاکتور/پرداخت', 'neomorph' ),
						'type'  => 'page',
					),
					'shop_page'    => array(
						'label' => esc_html__( 'صفحه فروشگاه', 'neomorph' ),
						'type'  => 'page',
					),
					'blog_page'    => array(
						'label' => esc_html__( 'صفحه مقالات', 'neomorph' ),
						'type'  => 'page',
					),
				),
			),
		);
	}

	/**
	 * Sanitize all fields.
	 */
	public static function sanitize( $input ) {
		$out     = get_option( self::OPT, array() );
		$out     = is_array( $out ) ? $out : array();
		$defaults = self::defaults();
		$input   = is_array( $input ) ? $input : array();

		foreach ( $defaults as $key => $default ) {
			if ( ! isset( $input[ $key ] ) ) {
				continue;
			}
			$raw = $input[ $key ];
			if ( is_array( $default ) ) {
				$out[ $key ] = is_array( $raw ) ? self::sanitize_deep( $raw ) : array();
				continue;
			}
			if ( false !== strpos( $key, 'color_' ) ) {
				$out[ $key ] = sanitize_hex_color( $raw ) ? sanitize_hex_color( $raw ) : $default;
				continue;
			}
			if ( 'contact_email' === $key ) {
				$out[ $key ] = sanitize_email( $raw );
				continue;
			}
			if ( in_array( $key, array( 'careers_intro', 'contact_address', 'fallback_hero_subtitle', 'interview_title', 'careers_form_title', 'fallback_hero_title', 'fallback_hero_btn' ), true ) ) {
				$out[ $key ] = sanitize_textarea_field( $raw );
				continue;
			}
			if ( in_array( $key, array( 'panel_page', 'invoice_page', 'login_page', 'register_page', 'shop_page', 'blog_page', 'interview_bg', 'font_size_base', 'radius', 'shadow_distance', 'shadow_softness', 'container_width', 'blog_excerpt_length' ), true ) ) {
				$out[ $key ] = (int) $raw;
				continue;
			}
			$out[ $key ] = sanitize_text_field( $raw );
		}

		return $out;
	}

	/**
	 * Recursively sanitize nested arrays (e.g. socials repeater).
	 *
	 * @param array $data Data.
	 * @return array
	 */
	private static function sanitize_deep( $data ) {
		$out = array();
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$out[ sanitize_key( (string) $key ) ] = self::sanitize_deep( $value );
			} elseif ( is_string( $value ) && 0 === strpos( $key, 'url' ) ) {
				$out[ sanitize_key( (string) $key ) ] = esc_url_raw( $value );
			} else {
				$out[ sanitize_key( (string) $key ) ] = sanitize_text_field( $value );
			}
		}
		return $out;
	}

	/**
	 * Professional control renderer (toggle pills / segmented / sliders / swatches).
	 *
	 * @param array $args { key, type, choices, description }.
	 */
	public static function field( $args ) {
		$key      = $args['key'];
		$type     = isset( $args['type'] ) ? $args['type'] : 'text';
		$value    = neomorph_option( $key, '' );
		$name     = self::OPT . '[' . $key . ']';
		$id       = 'neomorph-' . $key;
		$choices  = isset( $args['choices'] ) ? $args['choices'] : array();

		// Number fields with known ranges → slider + numeric readout.
		$ranges = array(
			'font_size_base'       => array( 12, 22, 'px' ),
			'radius'               => array( 0, 48, 'px' ),
			'shadow_distance'      => array( 2, 20, 'px' ),
			'shadow_softness'      => array( 1, 4, '×' ),
			'container_width'      => array( 900, 1600, 'px' ),
			'blog_excerpt_length'  => array( 5, 60, 'کلمه' ),
		);

		// Boolean select → switch pills.
		if ( 'select' === $type && 2 === count( $choices ) && array_key_exists( '1', $choices ) && array_key_exists( '0', $choices ) ) {
			printf( '<div class="neo-switch" role="radiogroup">' );
			printf(
				'<label class="neo-switch__opt"><input type="radio" name="%s" value="1" %s><span>%s</span></label>',
				esc_attr( $name ),
				checked( (string) $value, '1', false ),
				esc_html( $choices['1'] )
			);
			printf(
				'<label class="neo-switch__opt"><input type="radio" name="%s" value="0" %s><span>%s</span></label>',
				esc_attr( $name ),
				checked( (string) $value, '0', false ),
				esc_html( $choices['0'] )
			);
			echo '</div>';
			return;
		}

		// Few-choice select → segmented pills.
		if ( 'select' === $type && count( $choices ) <= 4 && count( $choices ) > 1 ) {
			echo '<div class="neo-seg" role="radiogroup">';
			$i = 0;
			foreach ( $choices as $val => $label ) {
				printf(
					'<label class="neo-seg__opt"><input type="radio" name="%s" value="%s" %s><span>%s</span></label>',
					esc_attr( $name ),
					esc_attr( $val ),
					checked( (string) $value, (string) $val, false ),
					esc_html( $label )
				);
				$i++;
			}
			echo '</div>';
			return;
		}

		// Slider numbers.
		if ( 'number' === $type && isset( $ranges[ $key ] ) ) {
			list( $min, $max, $unit ) = $ranges[ $key ];
			printf(
				'<div class="neo-slider"><input type="range" class="neo-slider__range" min="%d" max="%d" value="%s" data-sync="%s"><span class="neo-slider__val"><input type="number" name="%s" id="%s" value="%s" min="%d" max="%d" class="neo-slider__num"><em>%s</em></span></div>',
				(int) $min,
				(int) $max,
				esc_attr( $value ),
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $id ),
				esc_attr( $value ),
				(int) $min,
				(int) $max,
				esc_html( $unit )
			);
			return;
		}

		switch ( $type ) {
			case 'color':
				printf(
					'<div class="neo-color"><span class="neo-color__chip" style="background:%s"></span><input type="text" class="neo-color-field" id="%s" name="%s" value="%s" data-default-color="%s" /></div>',
					esc_attr( $value ? $value : '#e8edf5' ),
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value ),
					esc_attr( self::defaults()[ $key ] )
				);
				break;

			case 'select':
			case 'font':
			case 'page':
				printf( '<div class="neo-select"><select id="%s" name="%s">', esc_attr( $id ), esc_attr( $name ) );
				if ( 'font' === $type ) {
					foreach ( \Neomorph\Custom_Fonts::get_available_fonts() as $handle => $font ) {
						printf( '<option value="%s" %s>%s</option>', esc_attr( $handle ), selected( $value, $handle, false ), esc_html( $font['label'] ) );
					}
				} elseif ( 'page' === $type ) {
					echo '<option value="0">' . esc_html__( '— انتخاب کنید —', 'neomorph' ) . '</option>';
					foreach ( get_pages( array( 'sort_column' => 'menu_order' ) ) as $page ) {
						printf( '<option value="%d" %s>%s</option>', (int) $page->ID, selected( (int) $value, (int) $page->ID, false ), esc_html( $page->post_title ) );
					}
				} else {
					foreach ( $choices as $val => $label ) {
						printf( '<option value="%s" %s>%s</option>', esc_attr( $val ), selected( (string) $value, (string) $val, false ), esc_html( $label ) );
					}
				}
				echo '</select></div>';
				break;

			case 'textarea':
				printf( '<textarea class="neo-textarea" rows="3" id="%s" name="%s">%s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $value ) );
				break;

			case 'number':
				printf( '<input type="number" id="%s" name="%s" value="%s" class="neo-input-sm" />', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
				break;

			case 'email':
				printf( '<input type="email" id="%s" name="%s" value="%s" class="neo-input" placeholder="mail@example.com" />', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
				break;

			default:
				printf( '<input type="text" id="%s" name="%s" value="%s" class="neo-input" />', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
		}
	}

	/**
	 * Hidden inputs preserving values of fields NOT on the active tab (per-tab save).
	 *
	 * @param string $active Active tab slug.
	 */
	public static function preserve_hidden( $active ) {
		$sections = self::sections();
		foreach ( $sections as $slug => $section ) {
			if ( $slug === $active ) {
				continue;
			}
			foreach ( $section['fields'] as $key => $field ) {
				$value = neomorph_option( $key, '' );
				if ( is_array( $value ) ) {
					continue; // handled by caller (socials).
				}
				printf(
					'<input type="hidden" name="%s[%s]" value="%s">',
					esc_attr( self::OPT ),
					esc_attr( $key ),
					esc_attr( $value )
				);
			}
		}
		// Socials repeater lives in contact tab.
		if ( 'contact' !== $active ) {
			$socials = neomorph_option( 'socials', array() );
			foreach ( (array) $socials as $i => $social ) {
				printf(
					'<input type="hidden" name="%s[socials][%d][label]" value="%s">',
					esc_attr( self::OPT ),
					(int) $i,
					esc_attr( isset( $social['label'] ) ? $social['label'] : '' )
				);
				printf(
					'<input type="hidden" name="%s[socials][%d][url]" value="%s">',
					esc_attr( self::OPT ),
					(int) $i,
					esc_attr( isset( $social['url'] ) ? $social['url'] : '' )
				);
			}
		}
	}

	/**
	 * Settings page view (tabs).
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'typography'; // phpcs:ignore WordPress.Security.NonceVerification
		require __DIR__ . '/views/panel.php';
	}

	/**
	 * Admin notices (saved / font upload).
	 */
	public static function notices() {
		if ( ! isset( $_GET['page'] ) || self::PAGE !== $_GET['page'] ) {
			return;
		}
		if ( isset( $_GET['settings-updated'] ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'تنظیمات قالب ذخیره شد.', 'neomorph' ) . '</p></div>';
		}
		if ( isset( $_GET['font-uploaded'] ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'فونت سفارشی بارگذاری شد. اکنون می‌توانید آن را از فهرست فونت‌ها انتخاب کنید.', 'neomorph' ) . '</p></div>';
		}
	}
}

Options::init();
