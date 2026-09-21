<?php
/**
 * Search result row.
 *
 * @package Neomorph
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'neo-card neo-post-row' ); ?>>
	<div class="neo-post-row__body">
		<h2 class="neo-post-row__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="neo-post-row__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
		<a class="neo-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'مشاهده', 'neomorph' ); ?></a>
	</div>
</article>
