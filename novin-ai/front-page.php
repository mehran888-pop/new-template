<?php
/**
 * صفحه اصلی (زمانی که یک برگه ثابت انتخاب شده باشد).
 *
 * این قالب تمام‌عرض است و محتوا معمولاً توسط المنتور ساخته می‌شود.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="nv-front-page">
	<?php
	while ( have_posts() ) :
		the_post();

		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="nv-page-links">',
				'after'  => '</div>',
			)
		);

	endwhile;
	?>
</div>

<?php
get_footer();
