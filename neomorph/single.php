<?php
/**
 * Single post template.
 *
 * @package Neomorph
 */

get_header();
?>

<main id="primary" class="site-main neo-main has-sidebar">

	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'neo-surface neo-article' ); ?>>
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				<div class="entry-meta neo-meta">
					<span class="neo-meta__item"><?php echo esc_html( get_the_date() ); ?></span>
					<span class="neo-meta__item"><?php the_author_posts_link(); ?></span>
					<span class="neo-meta__item"><?php echo esc_html( get_the_category_list( '، ' ) ? wp_strip_all_tags( get_the_category_list( '، ' ) ) : '' ); ?></span>
				</div>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="entry-thumbnail neo-media">
					<?php the_post_thumbnail( 'large' ); ?>
				</figure>
			<?php endif; ?>

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

			<footer class="entry-footer neo-meta">
				<?php the_tags( '<span class="neo-meta__item">', '، ', '</span>' ); ?>
			</footer>
		</article>

		<?php
		the_post_navigation(
			array(
				'class' => 'neo-nav-posts',
			)
		);

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>

	<?php endwhile; ?>

</main>

<?php
get_sidebar();
get_footer();
