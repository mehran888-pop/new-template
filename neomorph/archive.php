<?php
/**
 * Archive template (category, tag, author, date + CPT archives from Neomorph Core).
 *
 * @package Neomorph
 */

get_header();
?>

<main id="primary" class="site-main neo-main has-sidebar">

	<header class="page-header neo-surface">
		<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
		<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="post-archive post-archive--<?php echo esc_attr( neomorph_option( 'blog_style', 'grid' ) ); ?> neo-grid neo-grid--3">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'card' );
			endwhile;
			?>
		</div>
		<?php the_posts_pagination( array( 'class' => 'neo-pagination' ) ); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>

</main>

<?php
get_sidebar();
get_footer();
