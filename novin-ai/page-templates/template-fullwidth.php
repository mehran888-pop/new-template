<?php
/**
 * Template Name: تمام‌عرض (بدون سایدبار)
 * Template Post Type: page, post, novin_project, novin_service
 *
 * مناسب برای صفحات ساخته‌شده با المنتور.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="nv-section nv-section--full">
	<?php
	while ( have_posts() ) :
		the_post();

		the_content();

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}

	endwhile;
	?>
</div>

<?php
get_footer();
