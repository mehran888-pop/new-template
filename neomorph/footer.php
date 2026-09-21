<?php
/**
 * Footer — neumorphic, 3 widget columns + menu + socials.
 *
 * @package Neomorph
 */
?>
</div><!-- #content -->

<footer id="colophon" class="site-footer neo-footer">
	<div class="neo-container">
		<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) ) : ?>
			<div class="site-footer__widgets neo-grid neo-grid--3">
				<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
					<?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
						<div class="site-footer__col"><?php dynamic_sidebar( 'footer-' . $i ); ?></div>
					<?php endif; ?>
				<?php endfor; ?>
			</div>
		<?php endif; ?>

		<div class="site-footer__bottom">
			<nav class="site-footer__menu" aria-label="<?php esc_attr_e( 'منوی فوتر', 'neomorph' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'fallback_cb'    => false,
						'depth'          => 1,
					)
				);
				?>
			</nav>
			<?php
			$socials = neomorph_option( 'socials', array() );
			if ( ! empty( $socials ) && is_array( $socials ) ) :
				?>
				<div class="site-footer__socials">
					<?php foreach ( $socials as $social ) : ?>
						<?php if ( ! empty( $social['url'] ) ) : ?>
							<a class="neo-social" href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer">
								<?php echo esc_html( isset( $social['label'] ) && $social['label'] ? $social['label'] : __( 'لینک', 'neomorph' ) ); ?>
							</a>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<p class="site-footer__copy">
				<?php
				printf(
					/* translators: 1: year, 2: site name */
					esc_html__( '© %1$s %2$s — تمامی حقوق محفوظ است.', 'neomorph' ),
					esc_html( gmdate( 'Y' ) ),
					esc_html( get_bloginfo( 'name' ) )
				);
				?>
			</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
