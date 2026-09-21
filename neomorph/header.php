<?php
/**
 * Main header — 3 layouts: classic / centered / minimal (from theme options).
 *
 * @package Neomorph
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
$header_layout = neomorph_option( 'header_layout', 'classic' );
$sticky        = neomorph_option( 'header_sticky', '1' );
?>
<header id="masthead" class="site-header neo-header neo-header--<?php echo esc_attr( $header_layout ); ?><?php echo $sticky ? ' is-sticky' : ''; ?>">
	<div class="neo-container site-header__inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
				<p class="site-description"><?php bloginfo( 'description' ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( 'centered' === $header_layout ) : ?>
			<nav class="site-navigation" aria-label="<?php esc_attr_e( 'منوی اصلی', 'neomorph' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'container'      => false,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
			<div class="site-header__actions">
				<?php neomorph_header_actions(); ?>
			</div>
		<?php else : ?>
			<nav class="site-navigation" aria-label="<?php esc_attr_e( 'منوی اصلی', 'neomorph' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'container'      => false,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
			<div class="site-header__actions">
				<?php neomorph_header_actions(); ?>
				<button class="neo-btn neo-btn--icon nav-toggle" aria-controls="mobile-drawer" aria-expanded="false">
					<span class="nav-toggle__bars" aria-hidden="true"></span>
					<span class="screen-reader-text"><?php esc_html_e( 'منو', 'neomorph' ); ?></span>
				</button>
			</div>
		<?php endif; ?>
	</div>
	<?php if ( 'minimal' !== $header_layout ) : ?>
	<nav id="mobile-drawer" class="mobile-drawer neo-surface" aria-label="<?php esc_attr_e( 'منوی موبایل', 'neomorph' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'mobile',
				'menu_id'        => 'mobile-menu',
				'container'      => false,
				'fallback_cb'    => function () {
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'fallback_cb'    => false,
						)
					);
				},
			)
		);
		?>
	</nav>
	<?php endif; ?>
</header>

<div id="content" class="site-content neo-container">
