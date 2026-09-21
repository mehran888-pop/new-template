<?php
/**
 * WooCommerce wrapper — keeps shop inside the neumorphic container.
 *
 * @package Neomorph
 */

get_header();
?>

<main id="primary" class="site-main neo-main neo-woo-wrap <?php echo is_shop() || is_product_taxonomy() ? 'no-sidebar' : 'no-sidebar'; ?>">
	<?php woocommerce_content(); ?>
</main>

<?php
get_footer();
