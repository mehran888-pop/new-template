<?php
/**
 * Main fallback template (blog index / archives fallback).
 *
 * @package Neomorph
 */

get_header();

$blog_style = neomorph_option( 'blog_style', 'grid' );
?>

<main id="primary" class="site-main neo-main <?php echo 'sidebar' === neomorph_option( 'archive_layout', 'content' ) ? 'has-sidebar' : 'no-sidebar'; ?>">

	<header class="page-header neo-surface">
		<h1 class="page-title"><?php esc_html_e( 'وبلاگ و مقالات', 'neomorph' ); ?></h1>
	</header>

	<?php if ( have_posts() ) : ?>

		<div class="post-archive post-archive--<?php echo esc_attr( $blog_style ); ?> neo-grid neo-grid--3">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', get_post_type() === 'post' ? 'card' : get_post_type() );
			endwhile;
			?>
		</div>

		<?php
		the_posts_pagination(
			array(
				'class' => 'neo-pagination',
			)
		);
		?>

	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>

</main>

<?php
if ( 'sidebar' === neomorph_option( 'archive_layout', 'content' ) ) {
	get_sidebar();
}
get_footer();
