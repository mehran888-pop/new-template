<?php
/**
 * Front page.
 *
 * With Elementor: the page content (built with Neomorph widgets) is printed as-is.
 * Without Elementor content: a graceful neumorphic starter layout is rendered.
 *
 * @package Neomorph
 */

get_header();
?>

<main id="primary" class="site-main neo-main neo-main--front no-sidebar">

	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<?php if ( has_blocks() || '' !== trim( get_the_content() ) ) : ?>
			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'صفحات:', 'neomorph' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>
		<?php else : ?>
			<?php get_template_part( 'template-parts/front', 'fallback' ); ?>
		<?php endif; ?>

		<?php
	endwhile;
	?>

</main>

<?php
get_footer();
