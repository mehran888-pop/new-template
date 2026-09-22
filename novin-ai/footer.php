<?php
/**
 * فوتر سایت.
 *
 * اگر برای «فوتر» در المنتور (Theme Builder) قالبی ساخته و منتشر کرده باشید،
 * همان قالب نمایش داده می‌شود؛ در غیر این صورت فوتر پیش‌فرض قالب چاپ می‌شود.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
	</main><!-- #primary -->

	<?php
	$footer_rendered = false;

	// ۱) المنتور پرو: قالب فوتر تعریف‌شده در Theme Builder.
	if ( function_exists( 'elementor_theme_do_location' ) ) {
		$footer_rendered = elementor_theme_do_location( 'footer' );
	}

	// ۲) المنتور رایگان: قالبی که در سفارشی‌ساز انتخاب شده است.
	if ( ! $footer_rendered ) {
		$footer_rendered = novin_ai_render_elementor_template( (int) novin_ai_option( 'footer_template' ), 'nv-footer-template' );
	}

	// ۳) فوتر پیش‌فرض قالب.
	if ( ! $footer_rendered ) {
		novin_ai_footer_fallback();
	}
	?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
