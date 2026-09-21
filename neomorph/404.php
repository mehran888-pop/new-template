<?php
/**
 * 404 template.
 *
 * @package Neomorph
 */

get_header();
?>

<main id="primary" class="site-main neo-main no-sidebar">
	<section class="neo-surface neo-error404">
		<p class="neo-error404__code">404</p>
		<h1 class="neo-error404__title"><?php esc_html_e( 'صفحه مورد نظر پیدا نشد!', 'neomorph' ); ?></h1>
		<p class="neo-error404__text"><?php esc_html_e( 'به نظر می‌رسد صفحه‌ای که دنبال آن بودید جابه‌جا یا حذف شده است. از جستجو استفاده کنید ییا به صفحه اصلی برگردید.', 'neomorph' ); ?></p>
		<div class="neo-error404__actions">
			<a class="neo-btn neo-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحه اصلی', 'neomorph' ); ?></a>
			<?php get_search_form(); ?>
		</div>
	</section>
</main>

<?php
get_footer();
