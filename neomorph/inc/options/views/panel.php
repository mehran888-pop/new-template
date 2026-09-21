<?php
/**
 * Theme settings panel — professional card UI (tabbed, per-tab save).
 *
 * @package Neomorph\Options
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$sections = \Neomorph\Options\Options::sections();
$tab_icons = array(
	'typography' => '🔤',
	'colors'     => '🎨',
	'neo'        => '🧊',
	'layout'     => '📐',
	'header'     => '🔝',
	'blog'       => '📰',
	'product'    => '🛍',
	'careers'    => '💼',
	'interview'  => '🎬',
	'contact'    => '📞',
	'pages'      => '🗺',
);
?>
<div class="wrap neomorph-settings neo-opt">

	<header class="neo-opt__hero">
		<div class="neo-opt__hero-text">
			<h1>🎨 <?php esc_html_e( 'تنظیمات قالب نئومورف', 'neomorph' ); ?></h1>
			<p><?php esc_html_e( 'فونت، رنگ، چیدمان و موتور نئومورفیسم را از اینجا کنترل کنید. همه استایل‌ها با المنتور نیز قابل بازنویسی هستند.', 'neomorph' ); ?></p>
		</div>
		<div class="neo-opt__hero-badges">
			<span class="neo-opt__badge">Neomorph v<?php echo esc_html( NEOMORPH_VERSION ); ?></span>
			<a class="neo-opt__badge neo-opt__badge--link" href="<?php echo esc_url( admin_url( 'themes.php?page=neomorph-demo' ) ); ?>">🧪 <?php esc_html_e( 'دموی اولیه', 'neomorph' ); ?></a>
		</div>
	</header>

	<?php settings_errors(); ?>

	<div class="neo-opt__layout">

		<nav class="neo-opt__tabs" aria-label="<?php esc_attr_e( 'بخش‌های تنظیمات', 'neomorph' ); ?>">
			<?php foreach ( $sections as $slug => $section ) : ?>
				<a class="neo-opt__tab <?php echo $tab === $slug ? 'is-active' : ''; ?>"
					href="<?php echo esc_url( add_query_arg( array( 'page' => \Neomorph\Options\Options::PAGE, 'tab' => $slug ), admin_url( 'themes.php' ) ) ); ?>">
					<span class="neo-opt__tab-icon"><?php echo esc_html( isset( $tab_icons[ $slug ] ) ? $tab_icons[ $slug ] : '•' ); ?></span>
					<span class="neo-opt__tab-label"><?php echo esc_html( $section['title'] ); ?></span>
					<span class="neo-opt__tab-count"><?php echo esc_html( (string) count( $section['fields'] ) ); ?></span>
				</a>
			<?php endforeach; ?>
			<a class="neo-opt__tab <?php echo 'fonts-upload' === $tab ? 'is-active' : ''; ?>"
				href="<?php echo esc_url( add_query_arg( array( 'page' => \Neomorph\Options\Options::PAGE, 'tab' => 'fonts-upload' ), admin_url( 'themes.php' ) ) ); ?>">
				<span class="neo-opt__tab-icon">⬆️</span>
				<span class="neo-opt__tab-label"><?php esc_html_e( 'فونت دلخواه', 'neomorph' ); ?></span>
			</a>
		</nav>

		<div class="neo-opt__content">

			<?php if ( 'fonts-upload' === $tab ) : ?>
				<section class="neo-opt__card">
					<h2 class="neo-opt__card-title">⬆️ <?php esc_html_e( 'بارگذاری فونت دلخواه', 'neomorph' ); ?></h2>
					<p class="neo-opt__card-desc"><?php esc_html_e( 'فایل‌های woff2 / woff / ttf فونت را انتخاب کنید. برای وزن‌های مختلف چند فایل با هم بارگذاری کنید (نام فایل شامل وزن مثل 400 یا 700 باشد).', 'neomorph' ); ?></p>
					<form method="post" enctype="multipart/form-data" class="neo-opt__form">
						<?php wp_nonce_field( 'neomorph_font_upload', 'neomorph_font_upload_nonce' ); ?>
						<div class="neo-opt__grid">
							<div class="neo-opt-field">
								<label class="neo-opt-field__label"><?php esc_html_e( 'نام خانواده فونت', 'neomorph' ); ?></label>
								<input required type="text" class="neo-input" name="neomorph_font_family" placeholder="IRANSansX">
							</div>
							<div class="neo-opt-field">
								<label class="neo-opt-field__label"><?php esc_html_e( 'نام انگلیسی کوتاه', 'neomorph' ); ?></label>
								<input required type="text" class="neo-input" name="neomorph_font_slug" placeholder="iransansx">
							</div>
							<div class="neo-opt-field neo-opt-field--wide">
								<label class="neo-opt-field__label"><?php esc_html_e( 'فایل‌های فونت', 'neomorph' ); ?></label>
								<input required type="file" name="neomorph_font_files[]" multiple accept=".woff2,.woff,.ttf,.otf" class="neo-input">
							</div>
						</div>
						<p>
							<button type="submit" class="button button-primary neo-opt__save">⬆️ <?php esc_html_e( 'بارگذاری فونت', 'neomorph' ); ?></button>
						</p>
					</form>
				</section>

			<?php else : ?>
				<form method="post" action="options.php" class="neo-opt__form">
					<?php settings_fields( \Neomorph\Options\Options::OPT ); ?>
					<?php \Neomorph\Options\Options::preserve_hidden( $tab ); ?>

					<section class="neo-opt__card">
						<h2 class="neo-opt__card-title">
							<?php echo esc_html( isset( $tab_icons[ $tab ] ) ? $tab_icons[ $tab ] . ' ' : '' ); ?>
							<?php echo esc_html( isset( $sections[ $tab ] ) ? $sections[ $tab ]['title'] : '' ); ?>
						</h2>

						<div class="neo-opt__grid">
							<?php if ( isset( $sections[ $tab ] ) ) : ?>
								<?php foreach ( $sections[ $tab ]['fields'] as $key => $field ) : ?>
									<div class="neo-opt-field<?php echo in_array( $field['type'] ?? 'text', array( 'textarea' ), true ) ? ' neo-opt-field--wide' : ''; ?>">
										<div class="neo-opt-field__head">
											<label class="neo-opt-field__label" for="neomorph-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
											<?php if ( ! empty( $field['description'] ) ) : ?>
												<span class="neo-opt-field__hint"><?php echo esc_html( $field['description'] ); ?></span>
											<?php endif; ?>
										</div>
										<div class="neo-opt-field__control">
											<?php
											\Neomorph\Options\Options::field(
												array(
													'key'     => $key,
													'type'    => isset( $field['type'] ) ? $field['type'] : 'text',
													'choices' => isset( $field['choices'] ) ? $field['choices'] : array(),
												)
											);
											?>
										</div>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>

						<?php
						// Socials repeater (contact tab).
						if ( 'contact' === $tab ) :
							$socials = neomorph_option( 'socials', array() );
							$socials = is_array( $socials ) && $socials ? $socials : array( array( 'label' => '', 'url' => '' ) );
							?>
							<div class="neo-opt-field neo-opt-field--wide">
								<div class="neo-opt-field__head">
									<label class="neo-opt-field__label"><?php esc_html_e( 'شبکه‌های اجتماعی', 'neomorph' ); ?></label>
									<span class="neo-opt-field__hint"><?php esc_html_e( 'در فوتر و ابزارک شبکه‌های اجتماعی استفاده می‌شود.', 'neomorph' ); ?></span>
								</div>
								<div id="neomorph-socials" class="neo-rows">
									<?php foreach ( $socials as $i => $social ) : ?>
										<div class="neo-rows__row">
											<span class="neo-rows__handle">⠿</span>
											<input type="text" name="<?php echo esc_attr( \Neomorph\Options\Options::OPT ); ?>[socials][<?php echo (int) $i; ?>][label]" value="<?php echo esc_attr( isset( $social['label'] ) ? $social['label'] : '' ); ?>" placeholder="<?php esc_attr_e( 'نام (مثلاً اینستاگرام)', 'neomorph' ); ?>" class="neo-input">
											<input type="url" name="<?php echo esc_attr( \Neomorph\Options\Options::OPT ); ?>[socials][<?php echo (int) $i; ?>][url]" value="<?php echo esc_attr( isset( $social['url'] ) ? $social['url'] : '' ); ?>" placeholder="https://..." class="neo-input neo-input--ltr">
											<button type="button" class="neo-rows__remove neomorph-remove-social" title="<?php esc_attr_e( 'حذف', 'neomorph' ); ?>">✕</button>
										</div>
									<?php endforeach; ?>
								</div>
								<p><button type="button" class="button" id="neomorph-add-social">＋ <?php esc_html_e( 'افزودن شبکه اجتماعی', 'neomorph' ); ?></button></p>
							</div>
						<?php endif; ?>

						<div class="neo-opt__footer">
							<button type="submit" class="button button-primary neo-opt__save">💾 <?php esc_html_e( 'ذخیره این بخش', 'neomorph' ); ?></button>
							<span class="neo-opt__hint"><?php esc_html_e( 'تغییرات هر بخش جداگانه ذخیره می‌شود؛ مقادیر بخش‌های دیگر دست‌نخورده باقی می‌ماند.', 'neomorph' ); ?></span>
						</div>
					</section>
				</form>
			<?php endif; ?>

		</div>
	</div>
</div>
