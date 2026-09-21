<?php
/**
 * Public invoice / payment page.
 * URL: /?nmc-invoice={token} — handled by Neomorph Core.
 *
 * @package Neomorph
 */

get_header();
?>

<main id="primary" class="site-main neo-main no-sidebar neo-main--invoice">
	<?php
	if ( neomorph_core_active() ) {
		echo do_shortcode( '[neomorph_invoice]' ); // phpcs:ignore WordPress.Security.EscapeOutput
	} else {
		echo '<div class="neo-surface neo-empty"><p>' . esc_html__( 'افزونه Neomorph Core فعال نیست.', 'neomorph' ) . '</p></div>';
	}
	?>
</main>

<?php
get_footer();
