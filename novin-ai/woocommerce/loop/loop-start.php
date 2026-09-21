<?php
/**
 * شروع حلقه محصولات (override ووکامرس).
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$columns = max( 2, (int) novin_ai_option( 'shop_columns', wc_get_loop_prop( 'columns', 3 ) ) );
?>
<div class="nv-products nv-products--<?php echo esc_attr( novin_ai_option( 'shop_card_style' ) ); ?>" style="--nv-cols:<?php echo esc_attr( $columns ); ?>">
