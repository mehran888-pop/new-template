<?php
/**
 * صفحه ۴۰۴.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="nv-section nv-section--404">
	<canvas class="nv-particles" data-nv-particles aria-hidden="true"></canvas>
	<div class="nv-orb nv-orb--1" aria-hidden="true"></div>
	<div class="nv-orb nv-orb--2" aria-hidden="true"></div>

	<div class="nv-container">
		<div class="nv-404 nv-glass">
			<span class="nv-404__code">۴۰۴</span>
			<h1 class="nv-404__title"><?php esc_html_e( 'صفحه مورد نظر پیدا نشد', 'novin-ai' ); ?></h1>
			<p class="nv-404__desc"><?php esc_html_e( 'شاید آدرس تغییر کرده باشد یا صفحه حذف شده باشد. از جستجو یا منوی زیر استفاده کنید.', 'novin-ai' ); ?></p>

			<div class="nv-404__actions">
				<a class="nv-btn nv-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span><?php esc_html_e( 'بازگشت به صفحه اصلی', 'novin-ai' ); ?></span>
					<?php echo novin_ai_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			</div>

			<div class="nv-search-form-wrap">
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
