<?php
/**
 * Gravity Forms glue:
 *  - neo CSS classes on fields
 *  - after submission → CRM contact sync + automation event (gform_{id}_submitted)
 *  - map GF field with CSS class `nmc-phone` to user phone
 *  - admin settings: per-form → create CRM case / assign segment / services
 *
 * @package NeomorphCore\GravityForms
 */

namespace NeomorphCore\GravityForms;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Glue
 */
final class Glue {

	public static function init() {
		add_filter( 'gform_field_css_class', array( __CLASS__, 'field_classes' ), 10, 3 );
		add_action( 'gform_after_submission', array( __CLASS__, 'after_submission' ), 10, 2 );
		add_action( 'gform_user_registered', array( __CLASS__, 'user_registered' ), 10, 4 );
		add_filter( 'gform_pre_send_email', array( __CLASS__, 'mirror_email_to_channels' ), 10, 4 );
	}

	/**
	 * Add neumorphism classes.
	 */
	public static function field_classes( $classes, $field, $form ) {
		$classes .= ' neo-field';
		return $classes;
	}

	/**
	 * Push submission into CRM + fire automation event.
	 *
	 * @param array $entry GF entry.
	 * @param array $form  GF form.
	 */
	public static function after_submission( $entry, $form ) {
		$settings = get_option( 'neomorph_core_gf_map', array() );
		$form_id  = (int) $form['id'];
		$map      = isset( $settings[ $form_id ] ) ? $settings[ $form_id ] : array();

		// Collect phone/email/name from entry (by input type or css class).
		$phone = $email = $name = '';
		foreach ( $form['fields'] as $field ) {
			$value = rgar( $entry, (string) $field->id );
			if ( ! $value ) {
				continue;
			}
			$css = isset( $field->cssClass ) ? $field->cssClass : '';
			if ( 'phone' === $field->type || false !== strpos( $css, 'nmc-phone' ) ) {
				$phone = $value;
			} elseif ( 'email' === $field->type ) {
				$email = $value;
			} elseif ( 'name' === $field->type || false !== strpos( $css, 'nmc-name' ) ) {
				$name = is_array( $value ) ? trim( ( $value['first'] ?? '' ) . ' ' . ( $value['last'] ?? '' ) ) : $value;
			}
		}

		$user_id = 0;
		if ( $phone && \nmc_is_valid_phone( $phone ) ) {
			$user_id = \nmc_user_id_by_phone( $phone );
			if ( ! $user_id ) {
				$user_id = wp_insert_user(
					array(
						'user_login' => \nmc_normalize_phone( $phone ),
						'user_pass'  => wp_generate_password( 24 ),
						'user_email' => $email ? $email : \nmc_normalize_phone( $phone ) . '@nmc.invalid',
						'display_name' => $name ? $name : \nmc_normalize_phone( $phone ),
						'role'       => 'nmc_customer',
					)
				);
				if ( ! is_wp_error( $user_id ) ) {
					update_user_meta( $user_id, 'phone', \nmc_normalize_phone( $phone ) );
					if ( ! $email ) {
						update_user_meta( $user_id, 'nmc_placeholder_email', 1 );
					}
				} else {
					$user_id = 0;
				}
			}
			if ( $user_id ) {
				\NeomorphCore\Crm\ContactController::sync_user( $user_id );
			}
		}

		// Optional: create CRM case from this form.
		if ( ! empty( $map['create_case'] ) ) {
			$case_id = wp_insert_post(
				array(
					'post_type'   => 'nmc_case',
					'post_status' => 'publish',
					'post_title'  => mb_substr( $name . ' — فرم ' . $form['title'], 0, 80 ),
				)
			);
			if ( $case_id ) {
				\nmc_update_meta( $case_id, 'stage', 'new' );
				\nmc_update_meta( $case_id, 'user_id', (int) $user_id );
				if ( ! empty( $map['segment'] ) ) {
					$segments   = (array) get_user_meta( $user_id, 'nmc_segments', true );
					$segments[] = (int) $map['segment'];
					update_user_meta( $user_id, 'nmc_segments', array_filter( array_unique( array_map( 'intval', $segments ) ) ) );
				}
				if ( ! empty( $map['services'] ) ) {
					wp_set_object_terms( $case_id, array_map( 'intval', (array) $map['services'] ), 'crm_service' );
				}
			}
		}

		\nmc_do_event(
			'gform_submitted',
			array(
				'user_id'  => (int) $user_id,
				'form_id'  => $form_id,
				'form_title' => $form['title'],
				'entry_id' => (int) $entry['id'],
			)
		);
	}

	/**
	 * GF user registration add-on: persist phone meta.
	 */
	public static function user_registered( $user_id, $config, $entry, $password ) {
		foreach ( $entry as $key => $value ) {
			$field_id = (int) $key;
			if ( $field_id && is_string( $value ) && \nmc_is_valid_phone( $value ) ) {
				update_user_meta( $user_id, 'phone', \nmc_normalize_phone( $value ) );
			}
		}
		if ( class_exists( '\NeomorphCore\Crm\ContactController' ) ) {
			\NeomorphCore\Crm\ContactController::sync_user( $user_id );
		}
	}

	/**
	 * Mirror notification emails to admin channels (Telegram/Bale) when enabled per form.
	 */
	public static function mirror_email_to_channels( $email, $message_format, $notification, $entry ) {
		$settings = get_option( 'neomorph_core_gf_map', array() );
		$form_id  = (int) $entry['form_id'];
		if ( empty( $settings[ $form_id ]['mirror_channels'] ) ) {
			return $email;
		}
		$text = wp_strip_all_tags( $email['message'] );
		foreach ( (array) $settings[ $form_id ]['mirror_channels'] as $channel ) {
			\NeomorphCore\Integrations\Notifier::send( sanitize_key( $channel ), '📩 ' . $email['subject'] . "\n" . mb_substr( $text, 0, 500 ) );
		}
		return $email;
	}
}

Glue::init();
