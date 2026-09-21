<?php
/**
 * Sidebar.
 *
 * @package Neomorph
 */

if ( ! is_active_sidebar( 'sidebar-main' ) ) {
	return;
}
?>
<aside id="secondary" class="widget-area neo-sidebar" aria-label="<?php esc_attr_e( 'سایدبار', 'neomorph' ); ?>">
	<?php dynamic_sidebar( 'sidebar-main' ); ?>
</aside>
