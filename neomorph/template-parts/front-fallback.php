<?php
/**
 * Front page fallback — neumorphic starter homepage (when no Elementor content).
 *
 * @package Neomorph
 */

$hero_title    = neomorph_option( 'fallback_hero_title', get_bloginfo( 'name' ) );
$hero_subtitle = neomorph_option( 'fallback_hero_subtitle', get_bloginfo( 'description' ) );
$hero_btn      = neomorph_option( 'fallback_hero_btn', __( 'مشاهده فروشگاه', 'neomorph' ) );
$hero_link     = neomorph_option( 'fallback_hero_link', class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '#' );
?>

<section class="neo-hero neo-surface">
	<div class="neo-hero__content">
		<h1 class="neo-hero__title"><?php echo esc_html( $hero_title ); ?></h1>
		<p class="neo-hero__subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
		<div class="neo-hero__actions">
			<a class="neo-btn neo-btn--primary" href="<?php echo esc_url( $hero_link ); ?>"><?php echo esc_html( $hero_btn ); ?></a>
			<a class="neo-btn" href="#home-services"><?php esc_html_e( 'خدمات ما', 'neomorph' ); ?></a>
		</div>
	</div>
</section>

<?php if ( class_exists( 'WooCommerce' ) ) : ?>
	<section class="neo-section" id="home-products">
		<h2 class="neo-section__title"><?php esc_html_e( 'محصولات ویژه', 'neomorph' ); ?></h2>
		<?php echo do_shortcode( '[products limit="4" columns="4" visibility="featured"]' ); ?>
	</section>
<?php endif; ?>

<section class="neo-section" id="home-services">
	<h2 class="neo-section__title"><?php esc_html_e( 'خدمات ما', 'neomorph' ); ?></h2>
	<div class="neo-grid neo-grid--3">
		<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
			<div class="neo-card neo-service-card">
				<div class="neo-service-card__icon neo-inset">✦</div>
				<h3><?php printf( esc_html__( 'خدمات حرفه‌ای %d', 'neomorph' ), $i ); ?></h3>
				<p><?php esc_html_e( 'توضیح کوتاه خود را اینجا بنویسید. همه چیز با سبک نئومورفیسم و سایه‌های نرم طراحی شده است.', 'neomorph' ); ?></p>
			</div>
		<?php endfor; ?>
	</div>
</section>

<section class="neo-section">
	<h2 class="neo-section__title"><?php esc_html_e( 'آخرین مقالات', 'neomorph' ); ?></h2>
	<div class="neo-grid neo-grid--3">
		<?php
		// NOTE: never use $posts / $post / $wp_query here — load_template() exports them as globals.
		$neo_home_query = new WP_Query(
			array(
				'posts_per_page'      => 3,
				'ignore_sticky_posts' => true,
			)
		);
		while ( $neo_home_query->have_posts() ) :
			$neo_home_query->the_post();
			get_template_part( 'template-parts/content', 'card' );
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
