<?php
/**
 * Fancy meta boxes for content elements (پروژه‌ها و خدمات) —
 * professional card UI instead of plain form-tables.
 *
 * Project meta (_nmc_*): client, year, project_url, status, featured, highlights[]
 * Service meta (_nmc_*): icon_text, icon_media, short_desc, features[] (title+desc), link
 *
 * @package NeomorphCore\Content
 */

namespace NeomorphCore\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class StylishMeta
 */
final class StylishMeta {

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'register' ) );
		add_action( 'save_post_project', array( __CLASS__, 'save_project' ) );
		add_action( 'save_post_service', array( __CLASS__, 'save_service' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	/**
	 * Assets on element edit screens only.
	 */
	public static function assets( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->post_type, array( 'project', 'service' ), true ) ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'nmc-fancy', NEOMORPH_CORE_URL . 'assets/css/admin-fancy.css', array(), NEOMORPH_CORE_VERSION );
		wp_enqueue_script( 'nmc-admin-js', NEOMORPH_CORE_URL . 'assets/js/admin.js', array( 'jquery' ), NEOMORPH_CORE_VERSION, true );
	}

	/**
	 * Register meta boxes.
	 */
	public static function register() {
		add_meta_box( 'nmc_project_meta', esc_html__( '🎨 جزئیات پروژه', 'neomorph-core' ), array( __CLASS__, 'project_box' ), 'project', 'normal', 'high' );
		add_meta_box( 'nmc_service_meta', esc_html__( '🎨 جزئیات خدمت', 'neomorph-core' ), array( __CLASS__, 'service_box' ), 'service', 'normal', 'high' );
	}

	/* ── Project ────────────────────────────────────────────────── */

	/**
	 * Project fields card.
	 */
	public static function project_box( $post ) {
		wp_nonce_field( 'nmc_project_meta', 'nmc_project_meta_nonce' );
		$featured = \nmc_get_meta( $post->ID, 'featured' );
		?>
		<div class="nmc-fancy-wrap">
			<div class="neo-opt__grid">
				<div class="neo-opt-field">
					<label class="neo-opt-field__label"><?php esc_html_e( 'کارفرما / مشتری', 'neomorph-core' ); ?></label>
					<input class="neo-input" type="text" name="nmc_client" value="<?php echo esc_attr( \nmc_get_meta( $post->ID, 'client' ) ); ?>">
				</div>
				<div class="neo-opt-field">
					<label class="neo-opt-field__label"><?php esc_html_e( 'سال انجام', 'neomorph-core' ); ?></label>
					<input class="neo-input-sm" type="text" name="nmc_year" value="<?php echo esc_attr( \nmc_get_meta( $post->ID, 'year' ) ); ?>" placeholder="1403">
				</div>
				<div class="neo-opt-field">
					<label class="neo-opt-field__label"><?php esc_html_e( 'لینک پروژه', 'neomorph-core' ); ?></label>
					<input class="neo-input" type="url" name="nmc_project_url" value="<?php echo esc_attr( \nmc_get_meta( $post->ID, 'project_url' ) ); ?>" placeholder="https://..." style="direction:ltr;text-align:left">
				</div>
				<div class="neo-opt-field">
					<label class="neo-opt-field__label"><?php esc_html_e( 'وضعیت', 'neomorph-core' ); ?></label>
					<div class="neo-seg" role="radiogroup">
						<?php
						$status_now = \nmc_get_meta( $post->ID, 'status', 'completed' );
						foreach ( array( 'completed' => esc_html__( 'تکمیل شده', 'neomorph-core' ), 'ongoing' => esc_html__( 'در حال اجرا', 'neomorph-core' ), 'showcase' => esc_html__( 'نمونه‌کار', 'neomorph-core' ) ) as $val => $label ) :
							?>
							<label class="neo-seg__opt">
								<input type="radio" name="nmc_status" value="<?php echo esc_attr( $val ); ?>" <?php checked( $status_now, $val ); ?>>
								<span><?php echo esc_html( $label ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="neo-opt-field">
					<label class="neo-opt-field__label"><?php esc_html_e( 'نمایش در صفحه اصلی', 'neomorph-core' ); ?></label>
					<?php $featured_yes = ( '1' === (string) $featured ); ?>
					<div class="neo-switch" role="radiogroup">
						<label class="neo-switch__opt"><input type="radio" name="nmc_featured" value="1" <?php checked( $featured_yes, true ); ?>><span><?php esc_html_e( 'بله', 'neomorph-core' ); ?></span></label>
						<label class="neo-switch__opt"><input type="radio" name="nmc_featured" value="0" <?php checked( $featured_yes, false ); ?>><span><?php esc_html_e( 'خیر', 'neomorph-core' ); ?></span></label>
					</div>
				</div>
				<div class="neo-opt-field neo-opt-field--wide">
					<div class="neo-opt-field__head">
						<label class="neo-opt-field__label"><?php esc_html_e( 'ویژگی‌های برجسته پروژه', 'neomorph-core' ); ?></label>
						<span class="neo-opt-field__hint"><?php esc_html_e( 'مثلاً: طراحی اختصاصی UI، افزایش ۳ برابری فروش…', 'neomorph-core' ); ?></span>
					</div>
					<?php
					$highlights = (array) \nmc_get_meta( $post->ID, 'highlights', array() );
					if ( ! $highlights ) {
						$highlights = array( '' );
					}
					?>
					<div class="neo-rows nmc-row-wrap" id="nmc-highlights">
						<?php foreach ( $highlights as $i => $row ) : ?>
							<div class="neo-rows__row nmc-rows__row">
								<span class="neo-rows__handle">⠿</span>
								<input class="neo-input" type="text" name="nmc_highlights[<?php echo (int) $i; ?>]" value="<?php echo esc_attr( $row ); ?>">
								<button type="button" class="neo-rows__remove nmc-remove-row" title="✕">✕</button>
							</div>
						<?php endforeach; ?>
					</div>
					<p><button type="button" class="button nmc-add-row" data-target="#nmc-highlights" data-template="#tpl-highlight">＋ <?php esc_html_e( 'افزودن ویژگی', 'neomorph-core' ); ?></button></p>
					<template id="tpl-highlight">
						<div class="neo-rows__row nmc-rows__row">
							<span class="neo-rows__handle">⠿</span>
							<input class="neo-input" type="text" name="nmc_highlights[__INDEX__]" value="">
							<button type="button" class="neo-rows__remove nmc-remove-row" title="✕">✕</button>
						</div>
					</template>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save project.
	 */
	public static function save_project( $post_id ) {
		if ( ! isset( $_POST['nmc_project_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nmc_project_meta_nonce'] ), 'nmc_project_meta' ) ) {
			return;
		}
		foreach ( array( 'nmc_client' => 'client', 'nmc_year' => 'year', 'nmc_project_url' => 'project_url', 'nmc_status' => 'status' ) as $field => $key ) {
			if ( isset( $_POST[ $field ] ) ) {
				$value = 'nmc_project_url' === $field ? esc_url_raw( wp_unslash( $_POST[ $field ] ) ) : sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
				\nmc_update_meta( $post_id, $key, $value );
			}
		}
		if ( isset( $_POST['nmc_featured'] ) ) {
			\nmc_update_meta( $post_id, 'featured', sanitize_text_field( wp_unslash( $_POST['nmc_featured'] ) ) );
		}
		if ( isset( $_POST['nmc_highlights'] ) && is_array( $_POST['nmc_highlights'] ) ) {
			$highlights = array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['nmc_highlights'] ) ) );
			\nmc_update_meta( $post_id, 'highlights', array_values( $highlights ) );
		}
	}

	/* ── Service ────────────────────────────────────────────────── */

	/**
	 * Service fields card.
	 */
	public static function service_box( $post ) {
		wp_nonce_field( 'nmc_service_meta', 'nmc_service_meta_nonce' );
		$icon_media = (int) \nmc_get_meta( $post->ID, 'icon_media' );
		$icon_url   = $icon_media ? wp_get_attachment_image_url( $icon_media, 'thumbnail' ) : '';
		?>
		<div class="nmc-fancy-wrap">
			<div class="neo-opt__grid">
				<div class="neo-opt-field">
					<label class="neo-opt-field__label"><?php esc_html_e( 'آیکون (ایموجی یا متن)', 'neomorph-core' ); ?></label>
					<input class="neo-input" type="text" name="nmc_icon_text" value="<?php echo esc_attr( \nmc_get_meta( $post->ID, 'icon_text' ) ); ?>" placeholder="💻">
				</div>
				<div class="neo-opt-field">
					<label class="neo-opt-field__label"><?php esc_html_e( 'آیکون تصویری (اختیاری)', 'neomorph-core' ); ?></label>
					<div class="nmc-media" style="display:flex;align-items:center;gap:10px">
						<input type="hidden" class="nmc-media-id" name="nmc_icon_media" value="<?php echo esc_attr( $icon_media ? $icon_media : '' ); ?>">
						<img class="nmc-media-preview" src="<?php echo esc_url( $icon_url ); ?>" alt="" style="<?php echo $icon_url ? '' : 'display:none'; ?>width:44px;height:44px;border-radius:12px;object-fit:cover">
						<button type="button" class="button nmc-media-pick"><?php esc_html_e( 'انتخاب', 'neomorph-core' ); ?></button>
						<button type="button" class="button nmc-media-clear" style="<?php echo $icon_url ? '' : 'display:none'; ?>"><?php esc_html_e( 'حذف', 'neomorph-core' ); ?></button>
					</div>
				</div>
				<div class="neo-opt-field neo-opt-field--wide">
					<label class="neo-opt-field__label"><?php esc_html_e( 'متن کوتاه کارت', 'neomorph-core' ); ?></label>
					<textarea class="neo-textarea" rows="2" name="nmc_short_desc"><?php echo esc_textarea( \nmc_get_meta( $post->ID, 'short_desc' ) ); ?></textarea>
				</div>
				<div class="neo-opt-field">
					<label class="neo-opt-field__label"><?php esc_html_e( 'لینک «بیشتر»', 'neomorph-core' ); ?></label>
					<input class="neo-input" type="url" name="nmc_service_link" value="<?php echo esc_attr( \nmc_get_meta( $post->ID, 'service_link' ) ); ?>" placeholder="https://..." style="direction:ltr;text-align:left">
				</div>
				<div class="neo-opt-field neo-opt-field--wide">
					<div class="neo-opt-field__head">
						<label class="neo-opt-field__label"><?php esc_html_e( 'ویژگی‌های خدمت', 'neomorph-core' ); ?></label>
						<span class="neo-opt-field__hint"><?php esc_html_e( 'هر ردیف: عنوان + توضیح کوتاه.', 'neomorph-core' ); ?></span>
					</div>
					<?php
					$features = (array) \nmc_get_meta( $post->ID, 'features', array() );
					if ( ! $features ) {
						$features = array( array( 'title' => '', 'desc' => '' ) );
					}
					?>
					<div class="neo-rows nmc-row-wrap" id="nmc-features">
						<?php foreach ( $features as $i => $row ) : ?>
							<div class="neo-rows__row nmc-rows__row" style="flex-wrap:wrap">
								<span class="neo-rows__handle">⠿</span>
								<input class="neo-input" style="flex:0 0 220px" type="text" name="nmc_features[<?php echo (int) $i; ?>][title]" value="<?php echo esc_attr( isset( $row['title'] ) ? $row['title'] : '' ); ?>" placeholder="<?php esc_attr_e( 'عنوان', 'neomorph-core' ); ?>">
								<input class="neo-input" type="text" name="nmc_features[<?php echo (int) $i; ?>][desc]" value="<?php echo esc_attr( isset( $row['desc'] ) ? $row['desc'] : '' ); ?>" placeholder="<?php esc_attr_e( 'توضیح', 'neomorph-core' ); ?>">
								<button type="button" class="neo-rows__remove nmc-remove-row" title="✕">✕</button>
							</div>
						<?php endforeach; ?>
					</div>
					<p><button type="button" class="button nmc-add-row" data-target="#nmc-features" data-template="#tpl-feature">＋ <?php esc_html_e( 'افزودن ویژگی', 'neomorph-core' ); ?></button></p>
					<template id="tpl-feature">
						<div class="neo-rows__row nmc-rows__row" style="flex-wrap:wrap">
							<span class="neo-rows__handle">⠿</span>
							<input class="neo-input" style="flex:0 0 220px" type="text" name="nmc_features[__INDEX__][title]" value="" placeholder="<?php esc_attr_e( 'عنوان', 'neomorph-core' ); ?>">
							<input class="neo-input" type="text" name="nmc_features[__INDEX__][desc]" value="" placeholder="<?php esc_attr_e( 'توضیح', 'neomorph-core' ); ?>">
							<button type="button" class="neo-rows__remove nmc-remove-row" title="✕">✕</button>
						</div>
					</template>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save service.
	 */
	public static function save_service( $post_id ) {
		if ( ! isset( $_POST['nmc_service_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nmc_service_meta_nonce'] ), 'nmc_service_meta' ) ) {
			return;
		}
		foreach ( array( 'nmc_icon_text' => 'icon_text', 'nmc_short_desc' => 'short_desc' ) as $field => $key ) {
			if ( isset( $_POST[ $field ] ) ) {
				$value = 'nmc_short_desc' === $field ? sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) : sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
				\nmc_update_meta( $post_id, $key, $value );
			}
		}
		if ( isset( $_POST['nmc_icon_media'] ) ) {
			\nmc_update_meta( $post_id, 'icon_media', (int) $_POST['nmc_icon_media'] );
		}
		if ( isset( $_POST['nmc_service_link'] ) ) {
			\nmc_update_meta( $post_id, 'service_link', esc_url_raw( wp_unslash( $_POST['nmc_service_link'] ) ) );
		}
		if ( isset( $_POST['nmc_features'] ) && is_array( $_POST['nmc_features'] ) ) {
			$features = array();
			foreach ( (array) wp_unslash( $_POST['nmc_features'] ) as $row ) {
				$title = sanitize_text_field( $row['title'] ?? '' );
				$desc  = sanitize_text_field( $row['desc'] ?? '' );
				if ( '' === $title && '' === $desc ) {
					continue;
				}
				$features[] = array( 'title' => $title, 'desc' => $desc );
			}
			\nmc_update_meta( $post_id, 'features', $features );
		}
	}
}

StylishMeta::init();
