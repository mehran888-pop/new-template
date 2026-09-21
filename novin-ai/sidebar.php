<?php
/**
 * سایدبار.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_active_sidebar( 'sidebar-blog' ) ) {
	return;
}
?>

<aside id="secondary" class="nv-widget-area">
	<?php dynamic_sidebar( 'sidebar-blog' ); ?>
</aside>
