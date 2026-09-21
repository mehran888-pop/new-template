<?php
/**
 * قالب برگه‌ها.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$page_class = 'nv-section nv-section--page';

if ( function_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->documents->get( get_the_ID() ) && \Elementor\Plugin::$instance->documents->get( get_the_ID() )->is_built_with_elementor() ) {
	$page_class .= ' nv-section--elementor';
}
?>

<section class="<?php echo esc_attr( $page_class ); ?>">
	<div class="nv-container">
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content/content', 'page' );

			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}

		endwhile;
		?>
	</div>
</section>

<?php
get_footer();
