<?php
/**
 * قالب نتایج جستجو.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="nv-section nv-section--search">
	<div class="nv-orb nv-orb--2" aria-hidden="true"></div>

	<div class="nv-container">
		<header class="nv-archive__header">
			<?php
			/* translators: %s: عبارت جستجو. */
			printf( '<h1 class="nv-section__title">%s</h1>', sprintf( esc_html__( 'نتایج جستجو برای: %s', 'novin-ai' ), '<span>' . esc_html( get_search_query() ) . '</span>' ) );
			?>
		</header>

		<div class="nv-search-form-wrap nv-glass">
			<?php get_search_form(); ?>
		</div>

		<?php if ( have_posts() ) : ?>

			<div class="nv-posts nv-posts--grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content/content', 'search' );
				endwhile;
				?>
			</div>

			<?php novin_ai_pagination(); ?>

		<?php else : ?>

			<?php novin_ai_loop_empty(); ?>

		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
