<?php
/**
 * Jobs / careers archive — list of open positions.
 *
 * @package Neomorph
 */

get_header();
?>

<main id="primary" class="site-main neo-main no-sidebar">

	<header class="page-header neo-surface">
		<h1 class="page-title"><?php post_type_archive_title(); ?></h1>
		<?php if ( neomorph_option( 'careers_intro' ) ) : ?>
			<p class="archive-description"><?php echo esc_html( neomorph_option( 'careers_intro' ) ); ?></p>
		<?php endif; ?>
	</header>

	<div class="neo-grid neo-grid--2 neo-jobs">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class( 'neo-card neo-job-card' ); ?>>
					<h2 class="neo-job-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="neo-meta">
						<?php if ( class_exists( '\NeomorphCore\Recruitment\JobController' ) ) : ?>
							<span class="neo-meta__item"><?php echo esc_html( \NeomorphCore\Recruitment\JobController::get_job_meta_line( get_the_ID() ) ); ?></span>
						<?php endif; ?>
					</div>
					<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
					<a class="neo-btn neo-btn--primary" href="<?php the_permalink(); ?>"><?php esc_html_e( 'مشاهده و درخواست', 'neomorph' ); ?></a>
				</article>
				<?php
			endwhile;
			?>
		<?php else : ?>
			<p class="neo-empty"><?php esc_html_e( 'در حال حاضر موقعیت شغلی بازی وجود ندارد.', 'neomorph' ); ?></p>
		<?php endif; ?>
	</div>

	<?php the_posts_pagination( array( 'class' => 'neo-pagination' ) ); ?>

</main>

<?php
get_footer();
