<?php
/**
 * Template Name: تمام‌عرض (Elementor / بدون سایدبار)
 * Template Post Type: page
 *
 * @package Neomorph
 */

get_header();
?>

<main id="primary" class="site-main neo-main no-sidebar neo-main--fullwidth">

	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<div class="entry-content"><?php the_content(); ?></div>
		<?php
	endwhile;
	?>

</main>

<?php
get_footer();
