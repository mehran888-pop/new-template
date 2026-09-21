<?php
/**
 * Template Name: لندینگ (بدون هدر و فوتر)
 * Template Post Type: page
 *
 * برای لندینگ‌های Elementor که هدر/فوتر نمی‌خواهند.
 *
 * @package Neomorph
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'neo-landing' ); ?>
<main id="primary" class="site-main neo-main no-sidebar neo-main--landing">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<div class="entry-content"><?php the_content(); ?></div>
		<?php
	endwhile;
	?>
</main>
<?php wp_footer(); ?>
</body>
</html>
