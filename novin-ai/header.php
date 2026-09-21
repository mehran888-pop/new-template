<?php
/**
 * هدر سایت.
 *
 * اگر برای «هدر» در المنتور (Theme Builder) قالبی ساخته و منتشر کرده باشید،
 * همان قالب نمایش داده می‌شود؛ در غیر این صورت هدر پیش‌فرض قالب چاپ می‌شود.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'رفتن به محتوای اصلی', 'novin-ai' ); ?></a>

<div id="page" class="nv-site">

	<?php
	$header_rendered = false;

	if ( function_exists( 'elementor_theme_do_location' ) ) {
		$header_rendered = elementor_theme_do_location( 'header' );
	}

	if ( ! $header_rendered ) {
		novin_ai_header_fallback();
	}
	?>

	<main id="primary" class="nv-main">
