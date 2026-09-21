<?php
/**
 * Post card (archive / blog grid).
 *
 * @package Neomorph
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'neo-card neo-post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="neo-post-card__thumb neo-media" href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( 'neomorph-card' ); ?>
		</a>
	<?php endif; ?>
	<div class="neo-post-card__body">
		<div class="neo-meta">
			<span class="neo-meta__item"><?php echo esc_html( get_the_date() ); ?></span>
			<?php
			$cats = get_the_category();
			if ( $cats ) :
				?>
				<span class="neo-meta__item"><?php echo esc_html( $cats[0]->name ); ?></span>
			<?php endif; ?>
		</div>
		<h2 class="neo-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="neo-post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
		<a class="neo-btn neo-btn--ghost" href="<?php the_permalink(); ?>"><?php esc_html_e( 'ادامه مطلب', 'neomorph' ); ?></a>
	</div>
</article>
