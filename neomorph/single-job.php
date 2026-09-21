<?php
/**
 * Single job (positions) template — used by Neomorph Core `job` CPT.
 * Falls back here when plugin template hierarchy resolves to theme.
 *
 * @package Neomorph
 */

get_header();
?>

<main id="primary" class="site-main neo-main has-sidebar">

	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<article <?php post_class( 'neo-surface neo-article neo-job' ); ?>>
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				<div class="neo-meta">
					<?php if ( class_exists( '\NeomorphCore\Recruitment\JobController' ) ) : ?>
						<span class="neo-meta__item"><?php echo esc_html( \NeomorphCore\Recruitment\JobController::get_job_meta_line( get_the_ID() ) ); ?></span>
					<?php endif; ?>
				</div>
			</header>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>

			<footer class="neo-job__apply neo-inset">
				<h2><?php esc_html_e( 'فرم درخواست همکاری', 'neomorph' ); ?></h2>
				<?php
				if ( neomorph_core_active() ) {
					echo do_shortcode( '[neomorph_apply job="' . get_the_ID() . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput
				} else {
					echo '<p>' . esc_html__( 'افزونه Neomorph Core برای فرم استخدام فعال نیست.', 'neomorph' ) . '</p>';
				}
				?>
			</footer>
		</article>

		<?php
	endwhile;
	?>

</main>

<?php
get_sidebar();
get_footer();
