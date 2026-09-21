<?php
/**
 * Dynamic CSS — outputs :root variables from theme options (colors, fonts, neumorphism strengths).
 *
 * @package Neomorph
 */

namespace Neomorph;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Dynamic_Css
 */
final class Dynamic_Css {

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'print' ), 20 );
	}

	/**
	 * Print inline CSS after main stylesheet.
	 */
	public static function print() {
		wp_add_inline_style( 'neomorph-main', self::generate() );
	}

	/**
	 * Build the CSS variable payload.
	 */
	public static function generate() {
		$bg        = neomorph_option( 'color_bg', '#e8edf5' );
		$text      = neomorph_option( 'color_text', '#2f3542' );
		$muted     = neomorph_option( 'color_muted', '#7b8494' );
		$accent    = neomorph_option( 'color_accent', '#6c5ce7' );
		$accent2   = neomorph_option( 'color_accent_2', '#00b894' );
		$radius    = (int) neomorph_option( 'radius', 24 );
		$distance  = (int) neomorph_option( 'shadow_distance', 8 );
		$softness  = (int) neomorph_option( 'shadow_softness', 2 );
		$container = (int) neomorph_option( 'container_width', 1200 );
		$body_f    = Custom_Fonts::family( neomorph_option( 'font_body', 'vazirmatn' ) );
		$head_f    = Custom_Fonts::family( neomorph_option( 'font_heading', 'vazirmatn' ) );

		$dark  = self::hex_to_rgb( $bg, 0.55, true );
		$light = self::hex_to_rgb( '#ffffff', 0.92, false );

		$unit  = max( 2, $distance );
		$blur  = max( 4, $unit * $softness );

		$css  = ':root{';
		$css .= '--neo-bg:' . $bg . ';';
		$css .= '--neo-text:' . $text . ';';
		$css .= '--neo-muted:' . $muted . ';';
		$css .= '--neo-accent:' . $accent . ';';
		$css .= '--neo-accent-2:' . $accent2 . ';';
		$css .= '--neo-radius:' . $radius . 'px;';
		$css .= '--neo-radius-sm:' . max( 6, (int) ( $radius / 2 ) ) . 'px;';
		$css .= '--neo-radius-lg:' . ( $radius + 14 ) . 'px;';
		$css .= '--neo-distance:' . $unit . 'px;';
		$css .= '--neo-dark:' . $dark . ';';
		$css .= '--neo-light:' . $light . ';';
		$css .= '--neo-shadow:' . $unit . 'px ' . $unit . 'px ' . $blur . 'px var(--neo-dark),-' . $unit . 'px -' . $unit . 'px ' . $blur . 'px var(--neo-light);';
		$css .= '--neo-shadow-sm: ' . (int) ( $unit / 2 ) . 'px ' . (int) ( $unit / 2 ) . 'px ' . max( 3, (int) ( $blur / 2 ) ) . 'px var(--neo-dark),-' . (int) ( $unit / 2 ) . 'px -' . (int) ( $unit / 2 ) . 'px ' . max( 3, (int) ( $blur / 2 ) ) . 'px var(--neo-light);';
		$css .= '--neo-shadow-inset:inset ' . (int) ( $unit / 1.5 ) . 'px ' . (int) ( $unit / 1.5 ) . 'px ' . (int) ( $blur / 1.5 ) . 'px var(--neo-dark),inset -' . (int) ( $unit / 1.5 ) . 'px -' . (int) ( $unit / 1.5 ) . 'px ' . (int) ( $blur / 1.5 ) . 'px var(--neo-light);';
		$css .= '--neo-shadow-hover:' . ( $unit + 2 ) . 'px ' . ( $unit + 2 ) . 'px ' . ( $blur + 4 ) . 'px var(--neo-dark),-' . ( $unit + 2 ) . 'px -' . ( $unit + 2 ) . 'px ' . ( $blur + 4 ) . 'px var(--neo-light);';
		$css .= '--neo-container:' . $container . 'px;';
		$css .= '--neo-font-body:' . $body_f . ';';
		$css .= '--neo-font-heading:' . $head_f . ';';
		$css .= '}';

		// Dark neumorphism style variant.
		if ( 'dark' === neomorph_option( 'soft_style', 'light' ) ) {
			$css .= 'body.neo-style-dark{--neo-bg:#232935;--neo-text:#eef1f7;--neo-muted:#a3adbf;--neo-dark:rgba(12,15,22,.75);--neo-light:rgba(122,132,156,.28);}';
		}

		return $css;
	}

	/**
	 * Hex color to rgba() string; dark=true means shadow color (darker), false means highlight.
	 */
	private static function hex_to_rgb( $hex, $alpha, $dark ) {
		$hex = ltrim( (string) $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		$r = (int) hexdec( substr( $hex, 0, 2 ) );
		$g = (int) hexdec( substr( $hex, 2, 2 ) );
		$b = (int) hexdec( substr( $hex, 4, 2 ) );
		if ( $dark ) {
			$r = (int) max( 0, $r - 32 );
			$g = (int) max( 0, $g - 32 );
			$b = (int) max( 0, $b - 32 );
		}
		return sprintf( 'rgba(%d,%d,%d,%s)', $r, $g, $b, $alpha );
	}
}

Dynamic_Css::init();
