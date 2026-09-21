<?php
/**
 * توابع قالب‌بندی (فالبک بدون المنتور، صفحه‌بندی، breadcrumb و ...).
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'novin_ai_posted_on' ) ) {
	/**
	 * نمایش تاریخ انتشار.
	 *
	 * @return void
	 */
	function novin_ai_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		printf(
			'<span class="nv-posted-on"><a href="%1$s" rel="bookmark">%2$s</a></span>',
			esc_url( get_permalink() ),
			$time_string // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}
}

if ( ! function_exists( 'novin_ai_posted_by' ) ) {
	/**
	 * نمایش نویسنده.
	 *
	 * @return void
	 */
	function novin_ai_posted_by() {
		printf(
			'<span class="nv-byline"><a href="%1$s">%2$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		);
	}
}

if ( ! function_exists( 'novin_ai_entry_footer' ) ) {
	/**
	 * دسته‌ها، برچسب‌ها و لینک ویرایش.
	 *
	 * @return void
	 */
	function novin_ai_entry_footer() {
		if ( 'post' === get_post_type() ) {
			$categories_list = get_the_category_list( esc_html__( ', ', 'novin-ai' ) );

			if ( $categories_list ) {
				printf( '<span class="nv-cat-links">%s</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput
			}

			$tags_list = get_the_tag_list( '', esc_html__( ', ', 'novin-ai' ) );

			if ( $tags_list ) {
				printf( '<span class="nv-tag-links">%s</span>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput
			}
		}

		edit_post_link( esc_html__( 'ویرایش', 'novin-ai' ), '<span class="nv-edit-link">', '</span>' );
	}
}

if ( ! function_exists( 'novin_ai_pagination' ) ) {
	/**
	 * صفحه‌بندی اختصاصی.
	 *
	 * @param array<string, mixed> $args آرگومان‌ها.
	 * @return void
	 */
	function novin_ai_pagination( $args = array() ) {
		global $wp_query;

		$query = isset( $args['query'] ) ? $args['query'] : $wp_query;

		$big   = 999999999;
		$pages = paginate_links(
			array(
				'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format'    => '?paged=%#%',
				'current'   => max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) ),
				'total'     => isset( $args['total'] ) ? $args['total'] : ( $query ? $query->max_num_pages : 1 ),
				'type'      => 'array',
				'prev_text' => novin_ai_icon( 'arrow-left' ) . '<span>' . esc_html__( 'قبلی', 'novin-ai' ) . '</span>',
				'next_text' => '<span>' . esc_html__( 'بعدی', 'novin-ai' ) . '</span>' . novin_ai_icon( 'arrow' ),
			)
		);

		if ( empty( $pages ) ) {
			return;
		}

		echo '<nav class="nv-pagination" aria-label="' . esc_attr__( 'صفحه‌بندی', 'novin-ai' ) . '"><ul>';

		foreach ( $pages as $page ) {
			$class = strpos( $page, 'current' ) !== false ? ' class="active"' : '';
			echo '<li' . $class . '>' . $page . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}

		echo '</ul></nav>';
	}
}

if ( ! function_exists( 'novin_ai_breadcrumb' ) ) {
	/**
	 * مسیر راهنما (Breadcrumb).
	 *
	 * @return void
	 */
	function novin_ai_breadcrumb() {
		if ( is_front_page() ) {
			return;
		}

		$items = array(
			'<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'خانه', 'novin-ai' ) . '</a>',
		);

		if ( is_home() ) {
			$items[] = '<span>' . esc_html( get_the_title( get_option( 'page_for_posts' ) ) ) . '</span>';
		} elseif ( is_singular() ) {
			$post_type = get_post_type();
			$obj       = get_post_type_object( $post_type );

			if ( $obj && $obj->has_archive && 'post' !== $post_type ) {
				$items[] = '<a href="' . esc_url( get_post_type_archive_link( $post_type ) ) . '">' . esc_html( $obj->label ) . '</a>';
			}

			$items[] = '<span>' . esc_html( get_the_title() ) . '</span>';
		} elseif ( is_post_type_archive() ) {
			$items[] = '<span>' . esc_html( post_type_archive_title( '', false ) ) . '</span>';
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$items[] = '<span>' . esc_html( single_term_title( '', false ) ) . '</span>';
		} elseif ( is_search() ) {
			/* translators: %s: عبارت جستجو. */
			$items[] = '<span>' . sprintf( esc_html__( 'نتایج جستجو برای: %s', 'novin-ai' ), esc_html( get_search_query() ) ) . '</span>';
		} elseif ( is_404() ) {
			$items[] = '<span>' . esc_html__( 'صفحه یافت نشد', 'novin-ai' ) . '</span>';
		} elseif ( is_archive() ) {
			$items[] = '<span>' . esc_html( get_the_archive_title() ) . '</span>';
		}

		echo '<nav class="nv-breadcrumb" aria-label="' . esc_attr__( 'مسیر راهنما', 'novin-ai' ) . '">';
		echo wp_kses_post( implode( '<span class="nv-breadcrumb__sep">' . novin_ai_icon( 'arrow-left' ) . '</span>', $items ) );
		echo '</nav>';
	}
}

if ( ! function_exists( 'novin_ai_header_fallback' ) ) {
	/**
	 * هدر پیش‌فرض (زمانی که قالب المنتور برای هدر ساخته نشده باشد).
	 *
	 * @return void
	 */
	function novin_ai_header_fallback() {
		?>
		<header id="nv-site-header" class="nv-header nv-header--fallback">
			<div class="nv-container nv-header__inner">
				<div class="nv-header__brand">
					<?php
					if ( has_custom_logo() ) {
						the_custom_logo();
					} else {
						printf(
							'<a class="nv-header__title" href="%1$s">%2$s</a>',
							esc_url( home_url( '/' ) ),
							esc_html( get_bloginfo( 'name' ) )
						);
					}
					?>
				</div>

				<nav class="nv-header__nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'novin-ai' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_class'     => 'nv-menu',
							'container'      => false,
							'fallback_cb'    => function () {
								wp_page_menu(
									array(
										'menu_class' => 'nv-menu',
										'container'  => false,
									)
								);
							},
						)
					);
					?>
				</nav>

				<div class="nv-header__actions">
					<?php if ( novin_ai_is_woocommerce_active() ) : ?>
						<a class="nv-icon-btn" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
							<?php echo novin_ai_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="nv-icon-btn__count"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
						</a>
					<?php endif; ?>

					<a class="nv-btn nv-btn--primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
						<span><?php echo esc_html( novin_ai_option( 'header_cta_text' ) ); ?></span>
						<?php echo novin_ai_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>

					<button class="nv-burger" type="button" aria-label="<?php esc_attr_e( 'منو', 'novin-ai' ); ?>" aria-expanded="false" data-nv-offcanvas-open="nv-offcanvas-menu">
						<?php echo novin_ai_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</button>
				</div>
			</div>
		</header>

		<div class="nv-offcanvas nv-offcanvas--end" id="nv-offcanvas-menu" aria-hidden="true">
			<div class="nv-offcanvas__backdrop" data-nv-offcanvas-close></div>
			<div class="nv-offcanvas__panel nv-glass">
				<button class="nv-offcanvas__close" type="button" aria-label="<?php esc_attr_e( 'بستن', 'novin-ai' ); ?>" data-nv-offcanvas-close>
					<?php echo novin_ai_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>

				<nav aria-label="<?php esc_attr_e( 'منوی موبایل', 'novin-ai' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'mobile',
							'menu_class'     => 'nv-menu nv-menu--stack',
							'container'      => false,
							'fallback_cb'    => 'novin_ai_menu_fallback',
						)
					);
					?>
				</nav>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'novin_ai_menu_fallback' ) ) {
	/**
	 * فالبک منو (نمایش منوی اصلی یا صفحات).
	 *
	 * @return void
	 */
	function novin_ai_menu_fallback() {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'menu_class'     => 'nv-menu nv-menu--stack',
				'container'      => false,
				'fallback_cb'    => 'wp_page_menu',
			)
		);
	}
}

if ( ! function_exists( 'novin_ai_footer_fallback' ) ) {
	/**
	 * فوتر پیش‌فرض.
	 *
	 * @return void
	 */
	function novin_ai_footer_fallback() {
		?>
		<footer class="nv-footer nv-footer--fallback">
			<div class="nv-orb nv-orb--1" aria-hidden="true"></div>
			<div class="nv-orb nv-orb--2" aria-hidden="true"></div>

			<div class="nv-container">
				<div class="nv-footer__grid">
					<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
						<div class="nv-footer__col">
							<?php dynamic_sidebar( 'footer-' . $i ); ?>
						</div>
					<?php endfor; ?>
				</div>

				<div class="nv-footer__bottom">
					<p><?php echo esc_html( novin_ai_option( 'footer_copyright' ) ); ?></p>
					<p class="nv-footer__credit">
						<?php esc_html_e( 'طراحی و توسعه با Novin AI', 'novin-ai' ); ?>
					</p>
				</div>
			</div>
		</footer>
		<?php
	}
}

if ( ! function_exists( 'novin_ai_scroll_top' ) ) {
	/**
	 * دکمه بازگشت به بالا.
	 *
	 * @return void
	 */
	function novin_ai_scroll_top() {
		echo '<button class="nv-scroll-top" type="button" aria-label="' . esc_attr__( 'بازگشت به بالا', 'novin-ai' ) . '">' . novin_ai_icon( 'arrow' ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
add_action( 'wp_footer', 'novin_ai_scroll_top' );

if ( ! function_exists( 'novin_ai_post_thumbnail' ) ) {
	/**
	 * تصویر شاخص با فالبک گرادیانتی.
	 *
	 * @param string $size اندازه تصویر.
	 * @return void
	 */
	function novin_ai_post_thumbnail( $size = 'novin-ai-card' ) {
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( $size, array( 'class' => 'nv-media__img', 'loading' => 'lazy' ) );
			return;
		}

		echo '<span class="nv-media__placeholder" aria-hidden="true">' . novin_ai_icon( 'spark' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

if ( ! function_exists( 'novin_ai_loop_empty' ) ) {
	/**
	 * پیام خالی بودن حلقه.
	 *
	 * @return void
	 */
	function novin_ai_loop_empty() {
		?>
		<div class="nv-empty nv-glass">
			<h2><?php esc_html_e( 'محتوایی یافت نشد', 'novin-ai' ); ?></h2>
			<p><?php esc_html_e( 'متأسفانه نتیجه‌ای برای درخواست شما پیدا نشد. می‌توانید جستجوی دیگری انجام دهید.', 'novin-ai' ); ?></p>
			<?php get_search_form(); ?>
		</div>
		<?php
	}
}
