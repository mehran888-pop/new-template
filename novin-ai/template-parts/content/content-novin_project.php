<?php
/**
 * کارت پروژه / نمونه‌کار در آرشیو.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$client = novin_ai_get_meta( get_the_ID(), 'client' );
$terms  = get_the_terms( get_the_ID(), 'novin_project_cat' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nv-card nv-project nv-tilt nv-reveal' ); ?>>

	<div class="nv-project__media nv-media">
		<a href="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
			<?php novin_ai_post_thumbnail( 'novin-ai-card' ); ?>
		</a>

		<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
			<span class="nv-badge"><?php echo esc_html( $terms[0]->name ); ?></span>
		<?php endif; ?>
	</div>

	<div class="nv-project__body">
		<h3 class="nv-project__title">
			<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
		</h3>

		<?php if ( $client ) : ?>
			<p class="nv-project__client"><?php echo esc_html( $client ); ?></p>
		<?php endif; ?>

		<a class="nv-link-more" href="<?php echo esc_url( get_permalink() ); ?>">
			<span><?php esc_html_e( 'مشاهده پروژه', 'novin-ai' ); ?></span>
			<?php echo novin_ai_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>
	</div>
</article>
