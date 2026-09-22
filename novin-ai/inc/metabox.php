<?php
/**
 * متاباکس‌های اختصاصی برای پروژه‌ها، خدمات، تیم، پکیج‌ها و نظرات.
 *
 * مقادیر این باکس‌ها به صورت خودکار در المان‌های المنتور استفاده می‌شوند.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * تعریف فیلدهای هر نوع نوشته.
 *
 * @return array<string, array<string, array<string, mixed>>>
 */
function novin_ai_metabox_fields() {
	return array(
		'novin_project'     => array(
			'client' => array(
				'label'       => esc_html__( 'نام کارفرما', 'novin-ai' ),
				'type'        => 'text',
				'placeholder' => esc_html__( 'مثال: شرکت داده‌پردازان نوین', 'novin-ai' ),
			),
			'date'   => array(
				'label' => esc_html__( 'تاریخ انجام', 'novin-ai' ),
				'type'  => 'text',
			),
			'url'    => array(
				'label'       => esc_html__( 'لینک پروژه', 'novin-ai' ),
				'type'        => 'url',
				'placeholder' => 'https://',
			),
			'tech'   => array(
				'label'       => esc_html__( 'تکنولوژی‌ها (هر خط یک مورد)', 'novin-ai' ),
				'type'        => 'textarea',
				'placeholder' => "Python\nTensorFlow\nReact",
			),
		),
		'novin_service'     => array(
			'icon'          => array(
				'label'       => esc_html__( 'کلاس آیکون (اختیاری)', 'novin-ai' ),
				'type'        => 'text',
				'placeholder' => 'dashicons-analytics',
			),
			'badge'         => array(
				'label' => esc_html__( 'برچسب روی کارت', 'novin-ai' ),
				'type'  => 'text',
			),
			'price'         => array(
				'label' => esc_html__( 'شروع قیمت (اختیاری)', 'novin-ai' ),
				'type'  => 'text',
			),
			'link'          => array(
				'label'       => esc_html__( 'لینک اختصاصی', 'novin-ai' ),
				'type'        => 'url',
				'placeholder' => 'https://',
			),
			'features'      => array(
				'label' => esc_html__( 'ویژگی‌ها (هر خط یک مورد)', 'novin-ai' ),
				'type'  => 'textarea',
			),
		),
		'novin_team'        => array(
			'role'         => array(
				'label' => esc_html__( 'سمت / نقش', 'novin-ai' ),
				'type'  => 'text',
			),
			'experience'   => array(
				'label' => esc_html__( 'سال‌های تجربه (متن کوتاه)', 'novin-ai' ),
				'type'  => 'text',
			),
			'projects'     => array(
				'label' => esc_html__( 'تعداد پروژه (متن کوتاه)', 'novin-ai' ),
				'type'  => 'text',
			),
			'quote'        => array(
				'label'       => esc_html__( 'نقل‌قول کوتاه', 'novin-ai' ),
				'type'        => 'textarea',
				'placeholder' => esc_html__( 'یک جمله الهام‌بخش از این عضو تیم…', 'novin-ai' ),
			),
			'skills'       => array(
				'label'       => esc_html__( 'مهارت‌ها (هر خط: نام|درصد)', 'novin-ai' ),
				'type'        => 'textarea',
				'placeholder' => "Python|90\nیادگیری ماشین|85\nمعماری نرم‌افزار|80",
			),
			'experience_list' => array(
				'label'       => esc_html__( 'سوابق کاری (هر خط: سال|عنوان|شرکت)', 'novin-ai' ),
				'type'        => 'textarea',
				'placeholder' => "۱۴۰۰-۱۴۰۳|مدیر فنی|شرکت الف\n۱۳۹۶-۱۴۰۰|برنامه‌نویس ارشد|شرکت ب",
			),
			'education'    => array(
				'label'       => esc_html__( 'تحصیلات (هر خط: مقطع|رشته|دانشگاه)', 'novin-ai' ),
				'type'        => 'textarea',
				'placeholder' => "کارشناسی ارشد|هوش مصنوعی|دانشگاه تهران",
			),
			'certifications' => array(
				'label'       => esc_html__( 'گواهینامه‌ها (هر خط: عنوان|سال)', 'novin-ai' ),
				'type'        => 'textarea',
				'placeholder' => "AWS Solutions Architect|1402\nTensorFlow Developer|1401",
			),
			'languages'    => array(
				'label'       => esc_html__( 'زبان‌ها (هر خط: زبان|سطح)', 'novin-ai' ),
				'type'        => 'textarea',
				'placeholder' => "فارسی|زبان مادری\nانگلیسی|تسلط کامل",
			),
			'location'     => array(
				'label' => esc_html__( 'شهر / محل کار', 'novin-ai' ),
				'type'  => 'text',
			),
			'email'        => array(
				'label' => esc_html__( 'ایمیل', 'novin-ai' ),
				'type'  => 'text',
			),
			'phone'        => array(
				'label' => esc_html__( 'تلفن', 'novin-ai' ),
				'type'  => 'text',
			),
			'instagram'    => array(
				'label' => esc_html__( 'اینستاگرام', 'novin-ai' ),
				'type'  => 'url',
			),
			'linkedin'     => array(
				'label' => esc_html__( 'لینکدین', 'novin-ai' ),
				'type'  => 'url',
			),
			'telegram'     => array(
				'label' => esc_html__( 'تلگرام', 'novin-ai' ),
				'type'  => 'url',
			),
			'twitter'      => array(
				'label' => esc_html__( 'ایکس / توییتر', 'novin-ai' ),
				'type'  => 'url',
			),
			'github'       => array(
				'label' => esc_html__( 'گیت‌هاب', 'novin-ai' ),
				'type'  => 'url',
			),
			'dribbble'     => array(
				'label' => esc_html__( 'دریبل', 'novin-ai' ),
				'type'  => 'url',
			),
			'website'      => array(
				'label' => esc_html__( 'وب‌سایت شخصی', 'novin-ai' ),
				'type'  => 'url',
			),
			'resume'       => array(
				'label'       => esc_html__( 'لینک فایل رزومه (PDF)', 'novin-ai' ),
				'type'        => 'url',
				'placeholder' => 'https://',
			),
			'featured'     => array(
				'label' => esc_html__( 'عضو ویژه (برجسته)', 'novin-ai' ),
				'type'  => 'checkbox',
			),
		),
		'novin_package'     => array(
			'price'        => array(
				'label'       => esc_html__( 'قیمت (ماهانه)', 'novin-ai' ),
				'type'        => 'text',
				'placeholder' => '4,500,000',
			),
			'price_yearly' => array(
				'label' => esc_html__( 'قیمت (سالیانه)', 'novin-ai' ),
				'type'  => 'text',
			),
			'currency'     => array(
				'label'   => esc_html__( 'واحد پول', 'novin-ai' ),
				'type'    => 'text',
				'default' => 'تومان',
			),
			'period'       => array(
				'label'   => esc_html__( 'دوره', 'novin-ai' ),
				'type'    => 'text',
				'default' => '/ ماهانه',
			),
			'features'     => array(
				'label' => esc_html__( 'امکانات پکیج (هر خط یک مورد)', 'novin-ai' ),
				'type'  => 'textarea',
			),
			'badge'        => array(
				'label' => esc_html__( 'برچسب', 'novin-ai' ),
				'type'  => 'text',
			),
			'button_text'  => array(
				'label'   => esc_html__( 'متن دکمه', 'novin-ai' ),
				'type'    => 'text',
				'default' => 'سفارش پکیج',
			),
			'button_url'   => array(
				'label' => esc_html__( 'لینک دکمه', 'novin-ai' ),
				'type'  => 'url',
			),
			'featured'     => array(
				'label' => esc_html__( 'پکیج ویژه (برجسته)', 'novin-ai' ),
				'type'  => 'checkbox',
			),
		),
		'novin_testimonial' => array(
			'role'    => array(
				'label' => esc_html__( 'سمت / شرکت', 'novin-ai' ),
				'type'  => 'text',
			),
			'rating'  => array(
				'label'   => esc_html__( 'امتیاز (۱ تا ۵)', 'novin-ai' ),
				'type'    => 'number',
				'default' => 5,
			),
			'project' => array(
				'label' => esc_html__( 'پروژه مرتبط', 'novin-ai' ),
				'type'  => 'text',
			),
		),
	);
}

if ( ! function_exists( 'novin_ai_add_metaboxes' ) ) {
	/**
	 * افزودن متاباکس‌ها.
	 *
	 * @return void
	 */
	function novin_ai_add_metaboxes() {
		foreach ( novin_ai_metabox_fields() as $post_type => $fields ) {
			add_meta_box(
				'novin_ai_details',
				esc_html__( 'جزئیات بیشتر (Novin AI)', 'novin-ai' ),
				'novin_ai_render_metabox',
				$post_type,
				'advanced',
				'default',
				array( 'fields' => $fields )
			);
		}
	}
}
add_action( 'add_meta_boxes', 'novin_ai_add_metaboxes' );

if ( ! function_exists( 'novin_ai_render_metabox' ) ) {
	/**
	 * نمایش متاباکس.
	 *
	 * @param WP_Post $post نوشته.
	 * @param array<string, mixed> $box پارامترها.
	 * @return void
	 */
	function novin_ai_render_metabox( $post, $box ) {
		$fields = isset( $box['args']['fields'] ) ? $box['args']['fields'] : array();

		wp_nonce_field( 'novin_ai_save_meta', 'novin_ai_meta_nonce' );

		echo '<div class="novin-ai-metabox">';

		foreach ( $fields as $key => $field ) {
			$value   = get_post_meta( $post->ID, '_novin_' . $key, true );
			$default = isset( $field['default'] ) ? $field['default'] : '';

			if ( '' === $value && '' !== $default ) {
				$value = $default;
			}

			$id = 'novin_meta_' . $key;

			echo '<p class="novin-ai-metabox__row">';
			echo '<label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $field['label'] ) . '</strong></label>';

			switch ( $field['type'] ) {
				case 'textarea':
					printf(
						'<textarea id="%1$s" name="%1$s" rows="5" class="widefat" placeholder="%3$s">%2$s</textarea>',
						esc_attr( $id ),
						esc_textarea( $value ),
						esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' )
					);
					break;

				case 'checkbox':
					printf(
						'<input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s>',
						esc_attr( $id ),
						checked( $value, '1', false )
					);
					break;

				case 'number':
					printf(
						'<input type="number" id="%1$s" name="%1$s" value="%2$s" class="widefat" min="0" max="100">',
						esc_attr( $id ),
						esc_attr( $value )
					);
					break;

				default:
					printf(
						'<input type="text" id="%1$s" name="%1$s" value="%2$s" class="widefat" placeholder="%3$s">',
						esc_attr( $id ),
						esc_attr( $value ),
						esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' )
					);
			}

			echo '</p>';
		}

		echo '</div>';
	}
}

if ( ! function_exists( 'novin_ai_save_metabox' ) ) {
	/**
	 * ذخیره مقادیر متاباکس.
	 *
	 * @param int    $post_id شناسه نوشته.
	 * @param WP_Post $post   نوشته.
	 * @return void
	 */
	function novin_ai_save_metabox( $post_id, $post ) {
		$post_type = get_post_type( $post_id );
		$fields    = novin_ai_metabox_fields();

		if ( ! isset( $fields[ $post_type ] ) ) {
			return;
		}

		if ( ! isset( $_POST['novin_ai_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['novin_ai_meta_nonce'] ) ), 'novin_ai_save_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( $fields[ $post_type ] as $key => $field ) {
			$id = 'novin_meta_' . $key;

			if ( 'checkbox' === $field['type'] ) {
				$value = isset( $_POST[ $id ] ) ? '1' : '';
				update_post_meta( $post_id, '_novin_' . $key, $value );
				continue;
			}

			if ( ! isset( $_POST[ $id ] ) ) {
				continue;
			}

			$raw = wp_unslash( $_POST[ $id ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

			switch ( $field['type'] ) {
				case 'textarea':
					$value = sanitize_textarea_field( $raw );
					break;
				case 'url':
					$value = esc_url_raw( $raw );
					break;
				case 'number':
					$value = absint( $raw );
					break;
				default:
					$value = sanitize_text_field( $raw );
			}

			update_post_meta( $post_id, '_novin_' . $key, $value );
		}
	}
}
add_action( 'save_post', 'novin_ai_save_metabox', 10, 2 );
