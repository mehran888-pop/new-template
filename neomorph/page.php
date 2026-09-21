<?php
/**
 * Generic page template.
 *
 * @package Neomorph
 */

get_header();
?>

<main id="primary" class="site-main neo-main <?php echo neomorph_option( 'page_layout', 'content' ) === 'sidebar' ? 'has-sidebar' : 'no-sidebar'; ?>">

	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'neo-surface neo-page' ); ?>>
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</header>

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
		</article>

		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>

	<?php endwhile; ?>

</main>

<?php
if ( 'sidebar' === neomorph_option( 'page_layout', 'content' ) ) {
	get_sidebar();
}
get_footer();
