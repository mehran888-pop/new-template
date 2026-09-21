<?php
/**
 * کارت پیش‌فرض نوشته (بلاگ، جستجو، آرشیو).
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$categories = get_the_category();
$read_time  = novin_ai_reading_time();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nv-card nv-post nv-tilt nv-reveal' ); ?>>

	<div class="nv-post__media nv-media">
		<a href="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
			<?php novin_ai_post_thumbnail( 'novin-ai-card' ); ?>
		</a>

		<?php if ( ! empty( $categories ) ) : ?>
			<a class="nv-badge" href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>">
				<?php echo esc_html( $categories[0]->name ); ?>
			</a>
		<?php endif; ?>
	</div>

	<div class="nv-post__body">
		<div class="nv-post__meta">
			<?php novin_ai_posted_on(); ?>
			<?php if ( novin_ai_option( 'show_reading_time' ) ) : ?>
				<span class="nv-post__read">· <?php echo esc_html( sprintf( '%d %s', $read_time, esc_html__( 'دقیقه مطالعه', 'novin-ai' ) ) ); ?></span>
			<?php endif; ?>
		</div>

		<h3 class="nv-post__title">
			<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
		</h3>

		<p class="nv-post__excerpt"><?php echo esc_html( novin_ai_excerpt( get_the_excerpt(), (int) novin_ai_option( 'excerpt_length' ) ) ); ?></p>

		<a class="nv-link-more" href="<?php echo esc_url( get_permalink() ); ?>">
			<span><?php echo esc_html( novin_ai_option( 'read_more_text' ) ); ?></span>
			<?php echo novin_ai_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>
	</div>
</article>
