<?php
/**
 * کارت عضو تیم در آرشیو.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$role = novin_ai_get_meta( get_the_ID(), 'role' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nv-card nv-member nv-tilt nv-reveal' ); ?>>

	<div class="nv-member__media nv-media">
		<?php novin_ai_post_thumbnail( 'novin-ai-portrait' ); ?>
	</div>

	<div class="nv-member__body">
		<h3 class="nv-member__name"><?php echo esc_html( get_the_title() ); ?></h3>

		<?php if ( $role ) : ?>
			<p class="nv-member__role"><?php echo esc_html( $role ); ?></p>
		<?php endif; ?>
	</div>
</article>
