<?php
/**
 * Template Name: پنل مشتریان (Neomorph)
 * Template Post Type: page
 *
 * Wrapper for the customer panel shortcode rendered by Neomorph Core.
 *
 * @package Neomorph
 */

get_header();
?>

<main id="primary" class="site-main neo-main neo-main--panel no-sidebar">

	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<?php if ( has_blocks() || '' !== trim( get_the_content() ) ) : ?>
			<div class="entry-content"><?php the_content(); ?></div>
		<?php else : ?>
			<h1 class="page-title"><?php the_title(); ?></h1>
			<?php
			if ( neomorph_core_active() ) {
				echo do_shortcode( '[neomorph_panel]' ); // phpcs:ignore WordPress.Security.EscapeOutput
			} else {
				echo '<div class="neo-surface neo-empty"><p>' . esc_html__( 'افزونه Neomorph Core فعال نیست. برای استفاده از پنل مشتریان، افزونه همراه قالب را نصب و فعال کنید.', 'neomorph' ) . '</p></div>';
			}
			?>
		<?php endif; ?>

		<?php
	endwhile;
	?>

</main>

<?php
get_footer();
