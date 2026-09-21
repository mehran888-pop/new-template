<?php
/**
 * Search results.
 *
 * @package Neomorph
 */

get_header();
?>

<main id="primary" class="site-main neo-main no-sidebar">

	<header class="page-header neo-surface">
		<h1 class="page-title">
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'نتایج جستجو برای: %s', 'neomorph' ),
				'<span>' . esc_html( get_search_query() ) . '</span>'
			);
			?>
		</h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="post-archive post-archive--list neo-grid neo-grid--1">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'search' );
			endwhile;
			?>
		</div>
		<?php the_posts_pagination( array( 'class' => 'neo-pagination' ) ); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>

</main>

<?php
get_footer();
