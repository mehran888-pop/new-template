<?php
/**
 * قالب آرشیوها و طبقه‌بندی‌ها.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$post_type   = get_post_type() ? get_post_type() : 'post';
$object      = get_post_type_object( $post_type );
$layout      = 'post' === $post_type ? novin_ai_option( 'blog_layout' ) : 'grid';
$columns     = ( 'novin_team' === $post_type ) ? 4 : 3;
?>

<section class="nv-section nv-section--archive">
	<div class="nv-grid-floor" aria-hidden="true"></div>
	<div class="nv-orb nv-orb--1" aria-hidden="true"></div>
	<div class="nv-container">
		<?php novin_ai_breadcrumb(); ?>

		<header class="nv-archive__header">
			<?php
			if ( is_post_type_archive() && $object ) {
				printf( '<h1 class="nv-section__title">%s</h1>', esc_html( $object->label ) );
			} else {
				the_archive_title( '<h1 class="nv-section__title">', '</h1>' );
				the_archive_description( '<p class="nv-section__desc">', '</p>' );
			}
			?>
		</header>

		<?php if ( have_posts() ) : ?>

			<div class="nv-posts nv-posts--<?php echo esc_attr( $layout ); ?>" style="--nv-cols:<?php echo esc_attr( (int) $columns ); ?>">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content/content', $post_type );
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
