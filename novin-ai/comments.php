<?php
/**
 * بخش دیدگاه‌ها.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="nv-comments">

	<?php if ( have_comments() ) : ?>
		<h2 class="nv-comments__title">
			<?php
			$comment_count = get_comments_number();
			printf(
				/* translators: %s: تعداد دیدگاه‌ها. */
				esc_html( _n( '%s دیدگاه', '%s دیدگاه', $comment_count, 'novin-ai' ) ),
				esc_html( number_format_i18n( $comment_count ) )
			);
			?>
		</h2>

		<ol class="nv-comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 64,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation(
			array(
				'prev_text' => esc_html__( 'دیدگاه‌های قدیمی‌تر', 'novin-ai' ),
				'next_text' => esc_html__( 'دیدگاه‌های جدیدتر', 'novin-ai' ),
			)
		);
		?>

	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="nv-no-comments"><?php esc_html_e( 'ارسال دیدگاه بسته شده است.', 'novin-ai' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply_before' => '<h3 id="reply-title" class="nv-comment-reply-title">',
			'title_reply_after'  => '</h3>',
			'class_submit'       => 'nv-btn nv-btn--primary',
		)
	);
	?>

</div>
