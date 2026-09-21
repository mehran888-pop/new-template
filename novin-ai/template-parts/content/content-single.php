<?php
/**
 * محتوای تک‌نوشته.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_type = get_post_type();
$client    = novin_ai_get_meta( get_the_ID(), 'client' );
$date      = novin_ai_get_meta( get_the_ID(), 'date' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nv-entry nv-entry--single' ); ?>>

	<header class="nv-entry__header">
		<?php novin_ai_breadcrumb(); ?>
		<?php the_title( '<h1 class="nv-entry__title">', '</h1>' ); ?>

		<div class="nv-entry__meta">
			<?php if ( 'post' === $post_type ) : ?>
				<?php novin_ai_posted_on(); ?>
				<?php novin_ai_posted_by(); ?>
				<span class="nv-entry__read"><?php echo esc_html( novin_ai_reading_time() . ' ' . esc_html__( 'دقیقه مطالعه', 'novin-ai' ) ); ?></span>
			<?php else : ?>
				<?php if ( $client ) : ?>
					<span class="nv-entry__client"><?php echo esc_html( $client ); ?></span>
				<?php endif; ?>
				<?php if ( $date ) : ?>
					<span class="nv-entry__date"><?php echo esc_html( $date ); ?></span>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</header>

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

	<?php if ( 'post' === $post_type ) : ?>
		<footer class="nv-entry__footer">
			<?php novin_ai_entry_footer(); ?>
		</footer>
	<?php endif; ?>

</article>
