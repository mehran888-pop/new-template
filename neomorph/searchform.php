<?php
/**
 * Search form — neumorphic.
 *
 * @package Neomorph
 */
?>
<form role="search" method="get" class="neo-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="neo-search-form__label">
		<span class="screen-reader-text"><?php esc_html_e( 'جستجو برای:', 'neomorph' ); ?></span>
		<input type="search" class="neo-input neo-search-form__input" placeholder="<?php esc_attr_e( 'جستجو…', 'neomorph' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
	</label>
	<button type="submit" class="neo-btn neo-btn--primary neo-search-form__submit"><?php esc_html_e( 'جستجو', 'neomorph' ); ?></button>
</form>
