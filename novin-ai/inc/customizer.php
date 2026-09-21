<?php
/**
 * سفارشی‌ساز وردپرس: رنگ‌ها، تایپوگرافی، چیدمان و افکت‌های سه‌بعدی.
 *
 * تمام این مقادیر به صورت CSS Variables در خروجی تزریق می‌شوند تا
 * المان‌های المنتور و استایل‌های قالب از یک منبع واحد استفاده کنند.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'novin_ai_sanitize_checkbox' ) ) {
	/**
	 * پاک‌سازی فیلد انتخابی.
	 *
	 * @param mixed $checked مقدار.
	 * @return bool
	 */
	function novin_ai_sanitize_checkbox( $checked ) {
		return ( isset( $checked ) && ( true === $checked || 1 === $checked || '1' === $checked ) );
	}
}

if ( ! function_exists( 'novin_ai_sanitize_number' ) ) {
	/**
	 * پاک‌سازی عدد.
	 *
	 * @param mixed $number مقدار.
	 * @return int
	 */
	function novin_ai_sanitize_number( $number ) {
		return is_numeric( $number ) ? $number + 0 : 0;
	}
}

if ( ! function_exists( 'novin_ai_sanitize_float' ) ) {
	/**
	 * پاک‌سازی عدد اعشاری.
	 *
	 * @param mixed $number مقدار.
	 * @return float
	 */
	function novin_ai_sanitize_float( $number ) {
		return is_numeric( $number ) ? (float) $number : 0;
	}
}

if ( ! function_exists( 'novin_ai_sanitize_select' ) ) {
	/**
	 * پاک‌سازی انتخاب از میان گزینه‌های مجاز.
	 *
	 * @param string                $input   ورودی.
	 * @param WP_Customize_Setting  $setting تنظیم.
	 * @return string
	 */
	function novin_ai_sanitize_select( $input, $setting ) {
		$choices = $setting->manager->get_control( $setting->id )->choices;

		return array_key_exists( $input, $choices ) ? $input : $setting->default;
	}
}

if ( ! function_exists( 'novin_ai_sanitize_css_color' ) ) {
	/**
	 * پاک‌سازی رنگ (هگز یا rgba).
	 *
	 * @param string $color مقدار.
	 * @return string
	 */
	function novin_ai_sanitize_css_color( $color ) {
		$color = trim( (string) $color );

		if ( preg_match( '/^rgba?\([\d\s.,%]+\)$/i', $color ) ) {
			return $color;
		}

		return sanitize_hex_color( $color ) ? sanitize_hex_color( $color ) : '';
	}
}

if ( ! function_exists( 'novin_ai_customize_register' ) ) {
	/**
	 * ثبت تنظیمات سفارشی‌ساز.
	 *
	 * @param WP_Customize_Manager $wp_customize مدیر سفارشی‌ساز.
	 * @return void
	 */
	function novin_ai_customize_register( $wp_customize ) {
		$defaults = novin_ai_defaults();

		/* ------------------------------------------------------------------
		 * پنل رنگ‌ها
		 * ---------------------------------------------------------------- */
		$wp_customize->add_panel(
			'novin_ai_colors',
			array(
				'title'       => esc_html__( 'رنگ‌ها و ظاهر کلی', 'novin-ai' ),
				'description' => esc_html__( 'رنگ‌های اصلی قالب و حالت‌های روشن/تیره.', 'novin-ai' ),
				'priority'    => 25,
			)
		);

		$wp_customize->add_section(
			'novin_ai_colors_main',
			array(
				'title' => esc_html__( 'رنگ‌های برند', 'novin-ai' ),
				'panel' => 'novin_ai_colors',
			)
		);

		$color_fields = array(
			'color_primary'   => esc_html__( 'رنگ اصلی', 'novin-ai' ),
			'color_secondary' => esc_html__( 'رنگ فرعی', 'novin-ai' ),
			'color_accent'    => esc_html__( 'رنگ تأکیدی', 'novin-ai' ),
		);

		foreach ( $color_fields as $key => $label ) {
			$wp_customize->add_setting(
				'novin_ai_' . $key,
				array(
					'default'           => $defaults[ $key ],
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'novin_ai_' . $key,
					array(
						'label'   => $label,
						'section' => 'novin_ai_colors_main',
					)
				)
			);
		}

		$wp_customize->add_section(
			'novin_ai_colors_surface',
			array(
				'title' => esc_html__( 'پس‌زمینه و سطوح', 'novin-ai' ),
				'panel' => 'novin_ai_colors',
			)
		);

		$surface_fields = array(
			'color_bg'       => esc_html__( 'پس‌زمینه اصلی', 'novin-ai' ),
			'color_bg_alt'   => esc_html__( 'پس‌زمینه بخش‌ها', 'novin-ai' ),
			'color_surface'  => esc_html__( 'سطح کارت‌ها (rgba)', 'novin-ai' ),
			'color_text'     => esc_html__( 'رنگ متن', 'novin-ai' ),
			'color_muted'    => esc_html__( 'رنگ متن کم‌رنگ', 'novin-ai' ),
			'color_border'   => esc_html__( 'رنگ خطوط (rgba)', 'novin-ai' ),
		);

		foreach ( $surface_fields as $key => $label ) {
			$wp_customize->add_setting(
				'novin_ai_' . $key,
				array(
					'default'           => $defaults[ $key ],
					'sanitize_callback' => 'novin_ai_sanitize_css_color',
				)
			);

			$wp_customize->add_control(
				'novin_ai_' . $key,
				array(
					'label'   => $label,
					'section' => 'novin_ai_colors_surface',
					'type'    => 'color',
				)
			);
		}

		/* ------------------------------------------------------------------
		 * پنل تایپوگرافی
		 * ---------------------------------------------------------------- */
		$wp_customize->add_panel(
			'novin_ai_typography',
			array(
				'title'    => esc_html__( 'تایپوگرافی', 'novin-ai' ),
				'priority' => 26,
			)
		);

		$wp_customize->add_section(
			'novin_ai_typography_base',
			array(
				'title' => esc_html__( 'فونت‌ها و اندازه‌ها', 'novin-ai' ),
				'panel' => 'novin_ai_typography',
			)
		);

		$text_fields = array(
			'font_body'    => esc_html__( 'فونت متن', 'novin-ai' ),
			'font_heading' => esc_html__( 'فونت عناوین', 'novin-ai' ),
		);

		foreach ( $text_fields as $key => $label ) {
			$wp_customize->add_setting(
				'novin_ai_' . $key,
				array(
					'default'           => $defaults[ $key ],
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_control(
				'novin_ai_' . $key,
				array(
					'label'       => $label,
					'description' => esc_html__( 'مثال: Vazirmatn, Tahoma, sans-serif', 'novin-ai' ),
					'section'     => 'novin_ai_typography_base',
					'type'        => 'text',
				)
			);
		}

		$wp_customize->add_setting(
			'novin_ai_load_google_fonts',
			array(
				'default'           => $defaults['load_google_fonts'],
				'sanitize_callback' => 'novin_ai_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'novin_ai_load_google_fonts',
			array(
				'label'   => esc_html__( 'بارگذاری فونت Vazirmatn از گوگل', 'novin-ai' ),
				'section' => 'novin_ai_typography_base',
				'type'    => 'checkbox',
			)
		);

		$number_fields = array(
			'font_size'           => array( esc_html__( 'اندازه متن (px)', 'novin-ai' ), 10, 30 ),
			'line_height'         => array( esc_html__( 'ارتفاع خط متن', 'novin-ai' ), 1, 3 ),
			'heading_line_height' => array( esc_html__( 'ارتفاع خط عناوین', 'novin-ai' ), 1, 3 ),
			'heading_weight'      => array( esc_html__( 'ضخامت عناوین', 'novin-ai' ), 300, 900 ),
			'letter_spacing'      => array( esc_html__( 'فاصله حروف', 'novin-ai' ), -2, 5 ),
		);

		foreach ( $number_fields as $key => $data ) {
			$is_float = in_array( $key, array( 'line_height', 'heading_line_height', 'letter_spacing' ), true );

			$wp_customize->add_setting(
				'novin_ai_' . $key,
				array(
					'default'           => $defaults[ $key ],
					'sanitize_callback' => $is_float ? 'novin_ai_sanitize_float' : 'novin_ai_sanitize_number',
				)
			);

			$wp_customize->add_control(
				'novin_ai_' . $key,
				array(
					'label'       => $data[0],
					'section'     => 'novin_ai_typography_base',
					'type'        => 'number',
					'input_attrs' => array(
						'min'  => $data[1],
						'max'  => $data[2],
						'step' => $is_float ? 0.05 : 1,
					),
				)
			);
		}

		/* ------------------------------------------------------------------
		 * پنل چیدمان
		 * ---------------------------------------------------------------- */
		$wp_customize->add_panel(
			'novin_ai_layout',
			array(
				'title'    => esc_html__( 'چیدمان و فاصله‌ها', 'novin-ai' ),
				'priority' => 27,
			)
		);

		$wp_customize->add_section(
			'novin_ai_layout_base',
			array(
				'title' => esc_html__( 'عرض و انحنای کارت‌ها', 'novin-ai' ),
				'panel' => 'novin_ai_layout',
			)
		);

		$layout_fields = array(
			'container_width'  => array( esc_html__( 'عرض کانتینر (px)', 'novin-ai' ), 960, 1600 ),
			'section_padding'  => array( esc_html__( 'فاصله عمودی بخش‌ها (px)', 'novin-ai' ), 20, 220 ),
			'radius'           => array( esc_html__( 'انحنای کارت‌ها (px)', 'novin-ai' ), 0, 48 ),
			'radius_sm'        => array( esc_html__( 'انحنای المان‌های کوچک (px)', 'novin-ai' ), 0, 32 ),
		);

		foreach ( $layout_fields as $key => $data ) {
			$wp_customize->add_setting(
				'novin_ai_' . $key,
				array(
					'default'           => $defaults[ $key ],
					'sanitize_callback' => 'novin_ai_sanitize_number',
				)
			);

			$wp_customize->add_control(
				'novin_ai_' . $key,
				array(
					'label'   => $data[0],
					'section' => 'novin_ai_layout_base',
					'type'    => 'number',
					'input_attrs' => array(
						'min' => $data[1],
						'max' => $data[2],
					),
				)
			);
		}

		/* ------------------------------------------------------------------
		 * پنل افکت‌های سه‌بعدی و فراگیر
		 * ---------------------------------------------------------------- */
		$wp_customize->add_panel(
			'novin_ai_effects',
			array(
				'title'       => esc_html__( 'افکت‌های 3D و فراگیر', 'novin-ai' ),
				'description' => esc_html__( 'مدیریت جلوه‌های حرکتی در کل سایت. برای عملکرد بهتر می‌توانید موارد غیرضروری را غیرفعال کنید.', 'novin-ai' ),
				'priority'    => 28,
			)
		);

		$wp_customize->add_section(
			'novin_ai_effects_motion',
			array(
				'title' => esc_html__( 'جلوه‌های حرکتی', 'novin-ai' ),
				'panel' => 'novin_ai_effects',
			)
		);

		$effect_toggles = array(
			'enable_tilt'             => esc_html__( 'چرخش سه‌بعدی کارت‌ها هنگام حرکت موس (Tilt)', 'novin-ai' ),
			'enable_particles'        => esc_html__( 'شبکه ذرات هوشمند در پس‌زمینه', 'novin-ai' ),
			'enable_orbs'             => esc_html__( 'کره‌های نوری شناور', 'novin-ai' ),
			'enable_grid_floor'       => esc_html__( 'شبکه پرسپکتیو سه‌بعدی', 'novin-ai' ),
			'enable_cursor'           => esc_html__( 'هاله نوری دنبال‌کننده موس', 'novin-ai' ),
			'enable_magnetic'         => esc_html__( 'دکمه‌های مغناطیسی', 'novin-ai' ),
			'enable_marquee'          => esc_html__( 'اسکرول بی‌پایان متن‌ها', 'novin-ai' ),
			'enable_reveal'           => esc_html__( 'ظهور تدریجی المان‌ها هنگام اسکرول', 'novin-ai' ),
			'hover_glow'              => esc_html__( 'درخشش نئونی هنگام هاور', 'novin-ai' ),
			'respect_reduced_motion'  => esc_html__( 'احترام به تنظیم کاهش حرکت مرورگر', 'novin-ai' ),
		);

		foreach ( $effect_toggles as $key => $label ) {
			$wp_customize->add_setting(
				'novin_ai_' . $key,
				array(
					'default'           => $defaults[ $key ],
					'sanitize_callback' => 'novin_ai_sanitize_checkbox',
				)
			);

			$wp_customize->add_control(
				'novin_ai_' . $key,
				array(
					'label'   => $label,
					'section' => 'novin_ai_effects_motion',
					'type'    => 'checkbox',
				)
			);
		}

		$wp_customize->add_setting(
			'novin_ai_tilt_strength',
			array(
				'default'           => $defaults['tilt_strength'],
				'sanitize_callback' => 'novin_ai_sanitize_number',
			)
		);

		$wp_customize->add_control(
			'novin_ai_tilt_strength',
			array(
				'label'       => esc_html__( 'شدت چرخش سه‌بعدی (درجه)', 'novin-ai' ),
				'section'     => 'novin_ai_effects_motion',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 30,
					'step' => 1,
				),
			)
		);

		/* ------------------------------------------------------------------
		 * بخش وبلاگ
		 * ---------------------------------------------------------------- */
		$wp_customize->add_section(
			'novin_ai_blog',
			array(
				'title'    => esc_html__( 'وبلاگ و مقالات', 'novin-ai' ),
				'priority' => 29,
			)
		);

		$wp_customize->add_setting(
			'novin_ai_blog_layout',
			array(
				'default'           => $defaults['blog_layout'],
				'sanitize_callback' => 'novin_ai_sanitize_select',
			)
		);

		$wp_customize->add_control(
			'novin_ai_blog_layout',
			array(
				'label'   => esc_html__( 'چیدمان بلاگ', 'novin-ai' ),
				'section' => 'novin_ai_blog',
				'type'    => 'select',
				'choices' => array(
					'grid'   => esc_html__( 'شبکه‌ای', 'novin-ai' ),
					'list'   => esc_html__( 'لیستی', 'novin-ai' ),
					'minimal' => esc_html__( 'مینیمال', 'novin-ai' ),
				),
			)
		);

		$wp_customize->add_setting(
			'novin_ai_blog_sidebar',
			array(
				'default'           => $defaults['blog_sidebar'],
				'sanitize_callback' => 'novin_ai_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'novin_ai_blog_sidebar',
			array(
				'label'   => esc_html__( 'نمایش سایدبار در بلاگ', 'novin-ai' ),
				'section' => 'novin_ai_blog',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'novin_ai_excerpt_length',
			array(
				'default'           => $defaults['excerpt_length'],
				'sanitize_callback' => 'novin_ai_sanitize_number',
			)
		);

		$wp_customize->add_control(
			'novin_ai_excerpt_length',
			array(
				'label'       => esc_html__( 'تعداد کلمات خلاصه', 'novin-ai' ),
				'section'     => 'novin_ai_blog',
				'type'        => 'number',
				'input_attrs' => array(
					'min' => 5,
					'max' => 80,
				),
			)
		);

		$wp_customize->add_setting(
			'novin_ai_read_more_text',
			array(
				'default'           => $defaults['read_more_text'],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'novin_ai_read_more_text',
			array(
				'label'   => esc_html__( 'متن دکمه ادامه مطلب', 'novin-ai' ),
				'section' => 'novin_ai_blog',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'novin_ai_show_reading_time',
			array(
				'default'           => $defaults['show_reading_time'],
				'sanitize_callback' => 'novin_ai_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'novin_ai_show_reading_time',
			array(
				'label'   => esc_html__( 'نمایش زمان مطالعه', 'novin-ai' ),
				'section' => 'novin_ai_blog',
				'type'    => 'checkbox',
			)
		);

		/* ------------------------------------------------------------------
		 * بخش فروشگاه
		 * ---------------------------------------------------------------- */
		$wp_customize->add_section(
			'novin_ai_shop',
			array(
				'title'    => esc_html__( 'فروشگاه (ووکامرس)', 'novin-ai' ),
				'priority' => 30,
			)
		);

		$shop_fields = array(
			'shop_columns'    => array( esc_html__( 'تعداد ستون محصولات', 'novin-ai' ), 2, 5 ),
			'shop_per_page'   => array( esc_html__( 'تعداد محصول در هر صفحه', 'novin-ai' ), 3, 48 ),
		);

		foreach ( $shop_fields as $key => $data ) {
			$wp_customize->add_setting(
				'novin_ai_' . $key,
				array(
					'default'           => $defaults[ $key ],
					'sanitize_callback' => 'novin_ai_sanitize_number',
				)
			);

			$wp_customize->add_control(
				'novin_ai_' . $key,
				array(
					'label'   => $data[0],
					'section' => 'novin_ai_shop',
					'type'    => 'number',
					'input_attrs' => array(
						'min' => $data[1],
						'max' => $data[2],
					),
				)
			);
		}

		$wp_customize->add_setting(
			'novin_ai_shop_card_style',
			array(
				'default'           => $defaults['shop_card_style'],
				'sanitize_callback' => 'novin_ai_sanitize_select',
			)
		);

		$wp_customize->add_control(
			'novin_ai_shop_card_style',
			array(
				'label'   => esc_html__( 'استایل کارت محصول', 'novin-ai' ),
				'section' => 'novin_ai_shop',
				'type'    => 'select',
				'choices' => array(
					'glass'   => esc_html__( 'شیشه‌ای (Glass)', 'novin-ai' ),
					'solid'   => esc_html__( 'ساده (Solid)', 'novin-ai' ),
					'outline' => esc_html__( 'خطی (Outline)', 'novin-ai' ),
					'neon'    => esc_html__( 'نئونی (Neon)', 'novin-ai' ),
				),
			)
		);

		$wp_customize->add_setting(
			'novin_ai_shop_hover_zoom',
			array(
				'default'           => $defaults['shop_hover_zoom'],
				'sanitize_callback' => 'novin_ai_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'novin_ai_shop_hover_zoom',
			array(
				'label'   => esc_html__( 'بزرگ‌نمایی تصویر محصول هنگام هاور', 'novin-ai' ),
				'section' => 'novin_ai_shop',
				'type'    => 'checkbox',
			)
		);

		/* ------------------------------------------------------------------
		 * بخش هدر و فوتر پیش‌فرض
		 * ---------------------------------------------------------------- */
		$wp_customize->add_section(
			'novin_ai_header_footer',
			array(
				'title'    => esc_html__( 'متن‌های هدر و فوتر', 'novin-ai' ),
				'priority' => 31,
			)
		);

		foreach ( array( 'header_cta_text' => esc_html__( 'متن دکمه هدر', 'novin-ai' ), 'footer_copyright' => esc_html__( 'متن کپی‌رایت فوتر', 'novin-ai' ) ) as $key => $label ) {
			$wp_customize->add_setting(
				'novin_ai_' . $key,
				array(
					'default'           => $defaults[ $key ],
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_control(
				'novin_ai_' . $key,
				array(
					'label'   => $label,
					'section' => 'novin_ai_header_footer',
					'type'    => 'text',
				)
			);
		}

		// انتخابگر لینک سریع به المنتور.
		$wp_customize->add_setting(
			'novin_ai_elementor_hint',
			array(
				'sanitize_callback' => '__return_empty_string',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'novin_ai_elementor_hint',
				array(
					'label'       => esc_html__( 'المان‌های اختصاصی', 'novin-ai' ),
					'description' => sprintf(
						/* translators: %s: لینک کتابخانه المنتور. */
						esc_html__( 'برای ساخت هدر، فوتر و بخش‌های صفحه اصلی از المان‌های گروه «Novin AI» در کتابخانه المنتور استفاده کنید. %s', 'novin-ai' ),
						'<a class="button button-primary" href="' . esc_url( admin_url( 'edit.php?post_type=elementor_library&tabs_group=library' ) ) . '">' . esc_html__( 'رفتن به قالب‌های المنتور', 'novin-ai' ) . '</a>'
					),
					'section'     => 'novin_ai_header_footer',
					'type'        => 'hidden',
				)
			)
		);
	}
}
add_action( 'customize_register', 'novin_ai_customize_register' );

if ( ! function_exists( 'novin_ai_dynamic_css' ) ) {
	/**
	 * تولید CSS پویا بر اساس تنظیمات سفارشی‌ساز.
	 *
	 * @return string
	 */
	function novin_ai_dynamic_css() {
		$o = array(
			'--nv-primary'     => novin_ai_option( 'color_primary' ),
			'--nv-secondary'   => novin_ai_option( 'color_secondary' ),
			'--nv-accent'      => novin_ai_option( 'color_accent' ),
			'--nv-bg'          => novin_ai_option( 'color_bg' ),
			'--nv-bg-alt'      => novin_ai_option( 'color_bg_alt' ),
			'--nv-surface'     => novin_ai_option( 'color_surface' ),
			'--nv-text'        => novin_ai_option( 'color_text' ),
			'--nv-muted'       => novin_ai_option( 'color_muted' ),
			'--nv-border'      => novin_ai_option( 'color_border' ),
			'--nv-font-body'   => novin_ai_option( 'font_body' ),
			'--nv-font-head'   => novin_ai_option( 'font_heading' ),
			'--nv-fs-base'     => (int) novin_ai_option( 'font_size' ) . 'px',
			'--nv-lh-body'     => (float) novin_ai_option( 'line_height' ),
			'--nv-lh-head'     => (float) novin_ai_option( 'heading_line_height' ),
			'--nv-fw-head'     => (int) novin_ai_option( 'heading_weight' ),
			'--nv-ls'          => (float) novin_ai_option( 'letter_spacing' ) . 'px',
			'--nv-container'   => (int) novin_ai_option( 'container_width' ) . 'px',
			'--nv-section-pad' => (int) novin_ai_option( 'section_padding' ) . 'px',
			'--nv-radius'      => (int) novin_ai_option( 'radius' ) . 'px',
			'--nv-radius-sm'   => (int) novin_ai_option( 'radius_sm' ) . 'px',
			'--nv-tilt'        => (int) novin_ai_option( 'tilt_strength' ) . 'deg',
		);

		$vars = '';

		foreach ( $o as $prop => $value ) {
			$vars .= $prop . ':' . $value . ';';
		}

		$css  = ':root{' . $vars . '}';
		$css .= 'body{font-family:var(--nv-font-body);font-size:var(--nv-fs-base);line-height:var(--nv-lh-body);letter-spacing:var(--nv-ls);color:var(--nv-text);background-color:var(--nv-bg);}';
		$css .= 'h1,h2,h3,h4,h5,h6,.nv-title{font-family:var(--nv-font-head);line-height:var(--nv-lh-head);font-weight:var(--nv-fw-head);}';

		if ( ! novin_ai_option( 'hover_glow' ) ) {
			$css .= '.nv-card:hover,.nv-hover-glow:hover{box-shadow:none !important;}';
		}

		return $css;
	}
}
