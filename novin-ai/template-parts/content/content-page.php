<?php
/**
 * محتوای برگه.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nv-entry nv-entry--page' ); ?>>

	<?php if ( ! is_front_page() ) : ?>
		<header class="nv-entry__header">
			<?php the_title( '<h1 class="nv-entry__title">', '</h1>' ); ?>
			<?php novin_ai_breadcrumb(); ?>
		</header>
	<?php endif; ?>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="nv-entry__thumb nv-media">
			<?php the_post_thumbnail( 'novin-ai-wide', array( 'loading' => 'lazy' ) ); ?>
		</div>
	<?php endif; ?>

	<div class="nv-entry__content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="nv-page-links">',
				'after'  => '</div>',
			)
		);
		?>
	</div>
</article>
