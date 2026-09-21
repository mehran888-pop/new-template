<?php
/**
 * Gravity Forms integration: neo field classes + validation styling + notification mirror hook.
 *
 * @package Neomorph
 */

namespace Neomorph;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Gravity_Forms
 */
final class Gravity_Forms {

	/**
	 * Hooks.
	 */
	public static function init() {
		if ( ! class_exists( 'GFForms' ) && ! class_exists( 'GFCommon' ) ) {
			// Styles still enqueue when GF loads late; hooks guard themselves.
		}
		add_filter( 'gform_field_css_class', array( __CLASS__, 'field_classes' ), 10, 3 );
		add_filter( 'gform_submit_button', array( __CLASS__, 'submit_button' ), 10, 2 );
		add_filter( 'gform_validation_message', array( __CLASS__, 'validation_message' ), 10, 2 );
		add_action( 'gform_after_submission', array( __CLASS__, 'mirror_to_theme_inbox' ), 5, 2 );
	}

	/**
	 * Add neumorphism classes to every field.
	 *
	 * @param string $classes Classes.
	 * @return string
	 */
	public static function field_classes( $classes ) {
		return $classes . ' neo-field';
	}

	/**
	 * Style the submit button as a neo primary button.
	 */
	public static function submit_button( $button, $form ) {
		return str_replace( 'gform_button ', 'gform_button neo-btn neo-btn--primary ', $button );
	}

	/**
	 * Wrap validation message.
	 */
	public static function validation_message( $message, $form ) {
		return '<div class="neo-inset gform_validation_errors">' . $message . '</div>';
	}

	/**
	 * Native contact form (widget) mirror: when GF is used with Neomorph Core,
	 * the Core Glue handles CRM. Here we keep a fallback that stores an admin notice-free log.
	 *
	 * @param array $entry Entry.
	 * @param array $form  Form.
	 */
	public static function mirror_to_theme_inbox( $entry, $form ) {
		/**
		 * Theme-side hook so custom code can react to any Gravity Forms submission.
		 */
		do_action( 'neomorph_gf_submission', $entry, $form );
	}
}

Gravity_Forms::init();
