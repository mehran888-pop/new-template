<?php
/**
 * Template tags used across theme templates.
 *
 * @package Neomorph
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Header actions: search toggle, cart, panel/login buttons.
 */
function neomorph_header_actions() {
	?>
	<button class="neo-btn neo-btn--icon search-toggle" aria-expanded="false" aria-controls="header-search">
		<span aria-hidden="true">⌕</span>
		<span class="screen-reader-text"><?php esc_html_e( 'جستجو', 'neomorph' ); ?></span>
	</button>
	<div id="header-search" class="header-search neo-surface" hidden>
		<?php get_search_form(); ?>
	</div>

	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<a class="neo-btn neo-btn--icon neo-cart-btn" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
			<span aria-hidden="true">🛒</span>
			<span class="neo-cart-btn__count"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
			<span class="screen-reader-text"><?php esc_html_e( 'سبد خرید', 'neomorph' ); ?></span>
		</a>
	<?php endif; ?>

	<?php if ( is_user_logged_in() ) : ?>
		<a class="neo-btn neo-btn--primary neo-btn--sm" href="<?php echo esc_url( neomorph_panel_url() ); ?>"><?php esc_html_e( 'پنل من', 'neomorph' ); ?></a>
	<?php else : ?>
		<a class="neo-btn neo-btn--sm" href="<?php echo esc_url( neomorph_panel_url( 'login' ) ); ?>"><?php esc_html_e( 'ورود | ثبت‌نام', 'neomorph' ); ?></a>
	<?php endif; ?>
	<?php
}

/**
 * Panel / auth page URLs (pages created by the demo importer or set in options).
 */
function neomorph_panel_url( $view = '' ) {
	$panel = neomorph_option( 'panel_page', 0 );
	$url   = $panel ? get_permalink( $panel ) : home_url( '/panel/' );
	if ( $view ) {
		$url = add_query_arg( 'neo-auth', $view, $url );
	}
	return $url;
}

/**
 * Format money per theme option.
 */
function neomorph_price( $amount ) {
	$currency = neomorph_option( 'currency', 'تومان' );
	$sep       = neomorph_option( 'price_separator', ',' );
	return number_format_i18n( (float) $amount, 0, '.', $sep ) . ' ' . $currency;
}

/**
 * Print a soft-shadowed icon badge.
 */
function neomorph_badge( $text, $type = 'info' ) {
	printf( '<span class="neo-badge neo-badge--%s">%s</span>', esc_attr( $type ), esc_html( $text ) );
}
