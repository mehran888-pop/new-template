<?php
/**
 * قالب پیش‌فرض (Fallback) — بلاگ و آرشیوها.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$show_sidebar = novin_ai_option( 'blog_sidebar' ) && is_active_sidebar( 'sidebar-blog' ) && ! is_singular();
?>

<section class="nv-section nv-section--archive">
	<div class="nv-grid-floor" aria-hidden="true"></div>
	<div class="nv-orb nv-orb--1" aria-hidden="true"></div>

	<div class="nv-container">
		<?php novin_ai_breadcrumb(); ?>

		<?php if ( is_home() || is_archive() || is_search() ) : ?>
			<header class="nv-archive__header">
				<?php
				if ( is_search() ) {
					/* translators: %s: عبارت جستجو. */
					printf( '<h1 class="nv-section__title">%s</h1>', sprintf( esc_html__( 'نتایج جستجو برای: %s', 'novin-ai' ), '<span>' . esc_html( get_search_query() ) . '</span>' ) );
				} elseif ( is_archive() ) {
					the_archive_title( '<h1 class="nv-section__title">', '</h1>' );
					the_archive_description( '<p class="nv-section__desc">', '</p>' );
				} else {
					printf( '<h1 class="nv-section__title">%s</h1>', esc_html( get_the_title( get_option( 'page_for_posts' ) ) ) );
				}
				?>
			</header>
		<?php endif; ?>

		<div class="nv-layout <?php echo $show_sidebar ? 'nv-layout--sidebar' : ''; ?>">
			<div class="nv-layout__content">
				<?php if ( have_posts() ) : ?>

					<div class="nv-posts nv-posts--<?php echo esc_attr( novin_ai_option( 'blog_layout' ) ); ?>">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/content/content', get_post_type() );
						endwhile;
						?>
					</div>

					<?php novin_ai_pagination(); ?>

				<?php else : ?>

					<?php novin_ai_loop_empty(); ?>

				<?php endif; ?>
			</div>

			<?php if ( $show_sidebar ) : ?>
				<aside class="nv-layout__sidebar">
					<?php get_sidebar(); ?>
				</aside>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
get_footer();
