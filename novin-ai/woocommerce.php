<?php
/**
 * قالب اصلی فروشگاه (ووکامرس).
 *
 * زمانی که این فایل وجود داشته باشد، برگه‌های فروشگاه به جای page.php از آن استفاده می‌کنند
 * و کنترل کامل چیدمان در اختیار قالب یا المنتور خواهد بود.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="nv-section nv-section--shop">
	<div class="nv-orb nv-orb--1" aria-hidden="true"></div>

	<?php if ( is_product() ) : ?>

		<div class="nv-container">
			<?php woocommerce_breadcrumb(); ?>
		</div>

		<?php
		while ( have_posts() ) :
			the_post();
			wc_get_template_part( 'content', 'single-product' );
		endwhile;
		?>

	<?php else : ?>

		<div class="nv-container">
			<?php
			if ( woocommerce_product_loop() ) {

				/**
				 * رویدادهای پیش از حلقه محصولات.
				 */
				do_action( 'woocommerce_before_shop_loop' );

				woocommerce_product_loop_start();

				if ( wc_get_loop_prop( 'total' ) ) {
					while ( have_posts() ) {
						the_post();

						/**
						 * رویداد نمایش هر محصول.
						 */
						do_action( 'woocommerce_shop_loop' );

						wc_get_template_part( 'content', 'product' );
					}
				}

				woocommerce_product_loop_end();

				/**
				 * رویدادهای پس از حلقه محصولات.
				 */
				do_action( 'woocommerce_after_shop_loop' );

			} else {
				/**
				 * زمانی که محصولی وجود ندارد.
				 */
				do_action( 'woocommerce_no_products_found' );
			}
			?>
		</div>

	<?php endif; ?>
</section>

<?php
get_footer();
