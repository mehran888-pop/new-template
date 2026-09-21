<?php
/**
 * قالب نمایش تک‌نوشته (مقالات و انواع نوشته اختصاصی).
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="nv-section nv-section--single">
	<div class="nv-orb nv-orb--3" aria-hidden="true"></div>

	<div class="nv-container">
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content/content', 'single' );

			the_post_navigation(
				array(
					'prev_text' => '<span class="nv-nav__label">' . esc_html__( 'قبلی', 'novin-ai' ) . '</span><span class="nv-nav__title">%title</span>',
					'next_text' => '<span class="nv-nav__label">' . esc_html__( 'بعدی', 'novin-ai' ) . '</span><span class="nv-nav__title">%title</span>',
				)
			);

			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}

		endwhile;
		?>
	</div>
</section>

<?php
get_footer();
