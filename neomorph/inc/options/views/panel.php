<?php
/**
 * Theme settings panel view (tabbed UI).
 *
 * @package Neomorph\Options
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$sections = \Neomorph\Options\Options::sections();
?>
<div class="wrap neomorph-settings">

	<h1 class="neomorph-settings__title">🎨 <?php esc_html_e( 'تنظیمات قالب نئومورف', 'neomorph' ); ?></h1>
	<p class="neomorph-settings__desc"><?php esc_html_e( 'فونت، رنگ، چیدمان و سبک نئومورفیسم را از اینجا کنترل کنید. بخش عمده استایل‌ها با المنتور نیز قابل بازنویسی هستند.', 'neomorph' ); ?></p>

	<?php settings_errors(); ?>

	<nav class="nav-tab-wrapper">
		<?php foreach ( $sections as $slug => $section ) : ?>
			<a href="<?php echo esc_url( add_query_arg( array( 'page' => \Neomorph\Options\Options::PAGE, 'tab' => $slug ), admin_url( 'themes.php' ) ) ); ?>"
				class="nav-tab <?php echo $tab === $slug ? 'nav-tab-active' : ''; ?>">
				<?php echo esc_html( $section['title'] ); ?>
			</a>
		<?php endforeach; ?>
		<a href="<?php echo esc_url( add_query_arg( array( 'page' => \Neomorph\Options\Options::PAGE, 'tab' => 'fonts-upload' ), admin_url( 'themes.php' ) ) ); ?>"
			class="nav-tab <?php echo 'fonts-upload' === $tab ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'بارگذاری فونت دلخواه', 'neomorph' ); ?>
		</a>
	</nav>

	<?php if ( 'fonts-upload' === $tab ) : ?>
		<div class="neo-surface-admin" style="margin-top:20px;max-width:720px;">
			<h2><?php esc_html_e( 'بارگذاری فونت دلخواه', 'neomorph' ); ?></h2>
			<p><?php esc_html_e( 'فایل‌های woff2 / woff / ttf فونت را انتخاب کنید. برای وزن‌های مختلف چند فایل با هم بارگذاری کنید (نام فایل شامل وزن مثل 400 یا 700 باشد).', 'neomorph' ); ?></p>
			<form method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'neomorph_font_upload', 'neomorph_font_upload_nonce' ); ?>
				<table class="form-table">
					<tr>
						<th><label for="neomorph_font_family"><?php esc_html_e( 'نام خانواده فونت', 'neomorph' ); ?></label></th>
						<td><input required type="text" class="regular-text" id="neomorph_font_family" name="neomorph_font_family" placeholder="IRANSansX"></td>
					</tr>
					<tr>
						<th><label for="neomorph_font_slug"><?php esc_html_e( 'نام انگلیسی کوتاه', 'neomorph' ); ?></label></th>
						<td><input required type="text" class="regular-text" id="neomorph_font_slug" name="neomorph_font_slug" placeholder="iransansx"></td>
					</tr>
					<tr>
						<th><label><?php esc_html_e( 'فایل‌های فونت', 'neomorph' ); ?></label></th>
						<td><input required type="file" name="neomorph_font_files[]" multiple accept=".woff2,.woff,.ttf,.otf"></td>
					</tr>
				</table>
				<?php submit_button( esc_html__( 'بارگذاری فونت', 'neomorph' ) ); ?>
			</form>
		</div>
	<?php else : ?>
		<form method="post" action="options.php">
			<?php
			settings_fields( \Neomorph\Options\Options::OPT );
			?>
			<div class="neo-surface-admin" style="margin-top:20px;max-width:860px;">
				<?php do_settings_sections( \Neomorph\Options\Options::PAGE ); ?>
			</div>
			<?php
			// Social repeater (simple rows).
			if ( 'contact' === $tab ) :
				$socials = neomorph_option( 'socials', array() );
				?>
				<div class="neo-surface-admin" style="margin-top:20px;max-width:860px;">
					<h2><?php esc_html_e( 'شبکه‌های اجتماعی', 'neomorph' ); ?></h2>
					<div id="neomorph-socials">
						<?php
						$socials = is_array( $socials ) && $socials ? $socials : array( array( 'label' => '', 'url' => '' ) );
						foreach ( $socials as $i => $social ) :
							?>
							<div class="neomorph-social-row">
								<input type="text" name="<?php echo esc_attr( \Neomorph\Options\Options::OPT ); ?>[socials][<?php echo (int) $i; ?>][label]" value="<?php echo esc_attr( isset( $social['label'] ) ? $social['label'] : '' ); ?>" placeholder="<?php esc_attr_e( 'نام (مثلاً اینستاگرام)', 'neomorph' ); ?>">
								<input type="url" name="<?php echo esc_attr( \Neomorph\Options\Options::OPT ); ?>[socials][<?php echo (int) $i; ?>][url]" value="<?php echo esc_attr( isset( $social['url'] ) ? $social['url'] : '' ); ?>" placeholder="https://...">
								<button type="button" class="button neomorph-remove-social"><?php esc_html_e( 'حذف', 'neomorph' ); ?></button>
							</div>
						<?php endforeach; ?>
					</div>
					<p><button type="button" class="button" id="neomorph-add-social">+ <?php esc_html_e( 'افزودن شبکه اجتماعی', 'neomorph' ); ?></button></p>
				</div>
			<?php endif; ?>

			<?php submit_button( esc_html__( 'ذخیره تنظیمات', 'neomorph' ) ); ?>
		</form>
	<?php endif; ?>
</div>
