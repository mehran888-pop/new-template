<?php
/**
 * المان اختصاصی بخش تیم.
 *
 * @package Novin_AI
 */

namespace Novin_AI\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

/**
 * کلاس تیم.
 */
class Team extends Novin_AI_Widget_Base {

	/**
	 * نام المان.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'novin-team';
	}

	/**
	 * عنوان المان.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'بخش تیم', 'novin-ai' );
	}

	/**
	 * آیکون المان.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-person';
	}

	/**
	 * کلمات کلیدی.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords() {
		return array( 'team', 'تیم', 'اعضا', 'member', 'کارکنان' );
	}

	/**
	 * ثبت کنترل‌ها.
	 *
	 * @return void
	 */
	protected function register_controls() {

		/* ---------------------------------- منبع ---------------------------------- */
		$this->start_controls_section(
			'section_source',
			array(
				'label' => esc_html__( 'منبع محتوا', 'novin-ai' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'منبع', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cpt',
				'options' => array(
					'cpt'    => esc_html__( 'نوع نوشته «تیم ما» (پیشنهادی)', 'novin-ai' ),
					'manual' => esc_html__( 'ورود دستی', 'novin-ai' ),
				),
			)
		);

		$this->add_control(
			'taxonomy_filter',
			array(
				'label'     => esc_html__( 'فیلتر دپارتمان', 'novin-ai' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''                 => esc_html__( 'همه', 'novin-ai' ),
					'novin_team_group' => esc_html__( 'دپارتمان‌ها', 'novin-ai' ),
				),
				'condition' => array( 'source' => 'cpt' ),
			)
		);

		$this->add_control(
			'terms',
			array(
				'label'       => esc_html__( 'ترم‌ها', 'novin-ai' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->nv_all_terms(),
				'label_block' => true,
				'condition'   => array(
					'source'           => 'cpt',
					'taxonomy_filter!' => '',
				),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'     => esc_html__( 'تعداد نفر', 'novin-ai' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4,
				'min'       => 1,
				'max'       => 24,
				'condition' => array( 'source' => 'cpt' ),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => esc_html__( 'مرتب‌سازی', 'novin-ai' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'menu_order',
				'options'   => array(
					'menu_order' => esc_html__( 'ترتیب دستی', 'novin-ai' ),
					'date'       => esc_html__( 'تاریخ', 'novin-ai' ),
					'title'      => esc_html__( 'عنوان', 'novin-ai' ),
					'rand'       => esc_html__( 'تصادفی', 'novin-ai' ),
				),
				'condition' => array( 'source' => 'cpt' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			array(
				'label' => esc_html__( 'تصویر پروفایل', 'novin-ai' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'   => esc_html__( 'نام', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'سارا احمدی', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'role',
			array(
				'label'   => esc_html__( 'سمت', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مدیر هوش مصنوعی', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'bio',
			array(
				'label' => esc_html__( 'درباره (اختیاری)', 'novin-ai' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);

		$repeater->add_control(
			'instagram',
			array(
				'label' => esc_html__( 'اینستاگرام', 'novin-ai' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$repeater->add_control(
			'linkedin',
			array(
				'label' => esc_html__( 'لینکدین', 'novin-ai' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$repeater->add_control(
			'telegram',
			array(
				'label' => esc_html__( 'تلگرام', 'novin-ai' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$repeater->add_control(
			'email',
			array(
				'label' => esc_html__( 'ایمیل', 'novin-ai' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'اعضا', 'novin-ai' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'name' => esc_html__( 'سارا احمدی', 'novin-ai' ),
						'role' => esc_html__( 'مدیر هوش مصنوعی', 'novin-ai' ),
					),
					array(
						'name' => esc_html__( 'علی رضایی', 'novin-ai' ),
						'role' => esc_html__( 'معمار نرم‌افزار', 'novin-ai' ),
					),
					array(
						'name' => esc_html__( 'نگار محمدی', 'novin-ai' ),
						'role' => esc_html__( 'مدیر محصول', 'novin-ai' ),
					),
					array(
						'name' => esc_html__( 'مهدی کریمی', 'novin-ai' ),
						'role' => esc_html__( 'مدیر زیرساخت', 'novin-ai' ),
					),
				),
				'title_field' => '{{{ name }}}',
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$this->end_controls_section();

		$this->section_title_controls(
			array(
				'title_default' => esc_html__( 'تیمی که پشت محصول شماست', 'novin-ai' ),
			)
		);

		/* ---------------------------------- چیدمان ---------------------------------- */
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'چیدمان', 'novin-ai' ),
			)
		);

		$this->add_control(
			'card_style',
			array(
				'label'   => esc_html__( 'استایل کارت', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'glass',
				'options' => array(
					'glass'   => esc_html__( 'شیشه‌ای', 'novin-ai' ),
					'solid'   => esc_html__( 'ساده', 'novin-ai' ),
					'outline' => esc_html__( 'خطی', 'novin-ai' ),
					'neon'    => esc_html__( 'نئونی', 'novin-ai' ),
				),
			)
		);

		$this->layout_controls( array( 'default' => 4 ) );

		$this->add_control(
			'image_ratio',
			array(
				'label'     => esc_html__( 'نسبت تصویر', 'novin-ai' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '3/4',
				'options'   => array(
					'1/1' => '1:1',
					'3/4' => '3:4',
					'4/3' => '4:3',
				),
				'selectors' => array(
					'{{WRAPPER}} .nv-member__media' => 'aspect-ratio: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'show_socials',
			array(
				'label'        => esc_html__( 'نمایش شبکه‌های اجتماعی', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_bio',
			array(
				'label'        => esc_html__( 'نمایش متن درباره', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'enable_tilt',
			array(
				'label'        => esc_html__( 'چرخش سه‌بعدی', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'enable_reveal',
			array(
				'label'        => esc_html__( 'ظهور تدریجی', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->backdrop_controls();

		/* ---------------------------------- استایل ---------------------------------- */
		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => esc_html__( 'استایل کارت', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->card_style_controls( '{{WRAPPER}} .nv-member' );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'avatar_border',
				'label'    => esc_html__( 'حاشیه تصویر', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-member__media',
			)
		);

		$this->add_control(
			'avatar_radius',
			array(
				'label'      => esc_html__( 'انحنای تصویر', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-member__media' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'label'    => esc_html__( 'تایپوگرافی نام', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-member__name',
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => esc_html__( 'رنگ نام', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-member__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'role_typography',
				'label'    => esc_html__( 'تایپوگرافی سمت', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-member__role',
			)
		);

		$this->add_control(
			'role_color',
			array(
				'label'     => esc_html__( 'رنگ سمت', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-member__role' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_color',
			array(
				'label'     => esc_html__( 'رنگ شبکه‌های اجتماعی', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-member__socials a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'social_border',
				'label'    => esc_html__( 'حاشیه شبکه‌های اجتماعی', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-member__socials a',
			)
		);

		$this->end_controls_section();

		$this->section_title_style_controls();
	}

	/**
	 * رندر شبکه‌های اجتماعی یک عضو.
	 *
	 * @param array<string, mixed> $data داده‌ها.
	 * @return void
	 */
	protected function render_socials( $data ) {
		$networks = array( 'instagram', 'linkedin', 'telegram', 'github', 'email' );
		$has      = false;

		foreach ( $networks as $network ) {
			if ( ! empty( $data[ $network ] ) ) {
				$has = true;
				break;
			}
		}

		if ( ! $has ) {
			return;
		}

		echo '<div class="nv-member__socials nv-socials">';

		foreach ( $networks as $network ) {
			$value = ! empty( $data[ $network ] ) ? $data[ $network ] : '';
			$href  = '';

			if ( is_array( $value ) ) {
				$href = ! empty( $value['url'] ) ? $value['url'] : '';
			} elseif ( 'email' === $network ) {
				$href = 'mailto:' . $value;
			} else {
				$href = $value;
			}

			if ( ! $href ) {
				continue;
			}

			printf(
				'<a class="nv-social nv-social--%1$s" href="%2$s" aria-label="%3$s">%4$s</a>',
				esc_attr( $network ),
				esc_url( $href ),
				esc_attr( $network ),
				novin_ai_social_icon( $network ) // phpcs:ignore WordPress.Security.EscapeOutput
			);
		}

		echo '</div>';
	}

	/**
	 * رندر خروجی.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'nv-widget nv-section nv-widget-team' );

		$members = array();

		if ( 'cpt' === $settings['source'] ) {
			$settings['post_type'] = 'novin_team';
			$query                 = $this->nv_query( $settings, 'novin_team' );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					$post_id = get_the_ID();

					$members[] = array(
						'name'      => get_the_title(),
						'role'      => novin_ai_get_meta( $post_id, 'role' ),
						'bio'       => novin_ai_excerpt( get_the_excerpt() ? get_the_excerpt() : get_the_content(), 24 ),
						'image'     => get_the_post_thumbnail_url( $post_id, 'novin-ai-portrait' ),
						'instagram' => novin_ai_get_meta( $post_id, 'instagram' ),
						'linkedin'  => novin_ai_get_meta( $post_id, 'linkedin' ),
						'telegram'  => novin_ai_get_meta( $post_id, 'telegram' ),
						'github'    => novin_ai_get_meta( $post_id, 'github' ),
						'email'     => novin_ai_get_meta( $post_id, 'email' ),
					);
				}

				wp_reset_postdata();
			}
		} elseif ( ! empty( $settings['items'] ) ) {
			foreach ( $settings['items'] as $item ) {
				$members[] = array(
					'name'      => $item['name'],
					'role'      => $item['role'],
					'bio'       => $item['bio'],
					'image'     => ! empty( $item['image']['url'] ) ? $item['image']['url'] : '',
					'instagram' => $item['instagram'],
					'linkedin'  => $item['linkedin'],
					'telegram'  => $item['telegram'],
					'github'    => array(),
					'email'     => $item['email'],
				);
			}
		}
		?>
		<section <?php $this->nv_attr( 'wrapper' ); ?>>
			<?php $this->nv_backdrop( $settings ); ?>

			<div class="nv-container">
				<?php $this->render_heading( $settings ); ?>

				<div class="nv-grid">
					<?php if ( ! empty( $members ) ) : ?>
						<?php foreach ( $members as $member ) : ?>
							<article class="nv-card nv-member <?php echo esc_attr( $this->nv_classes( $settings, array() ) ); ?>">
								<div class="nv-member__media nv-media">
									<?php if ( ! empty( $member['image'] ) ) : ?>
										<img class="nv-media__img" src="<?php echo esc_url( $member['image'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy">
									<?php else : ?>
										<span class="nv-media__placeholder" aria-hidden="true"><?php echo novin_ai_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
									<?php endif; ?>

									<?php if ( 'yes' === $settings['show_socials'] ) : ?>
										<div class="nv-member__socials-wrap">
											<?php $this->render_socials( $member ); ?>
										</div>
									<?php endif; ?>
								</div>

								<div class="nv-member__body">
									<h3 class="nv-member__name"><?php echo esc_html( $member['name'] ); ?></h3>

									<?php if ( ! empty( $member['role'] ) ) : ?>
										<p class="nv-member__role"><?php echo esc_html( $member['role'] ); ?></p>
									<?php endif; ?>

									<?php if ( 'yes' === $settings['show_bio'] && ! empty( $member['bio'] ) ) : ?>
										<p class="nv-member__bio"><?php echo esc_html( $member['bio'] ); ?></p>
									<?php endif; ?>
								</div>
							</article>
						<?php endforeach; ?>
					<?php else : ?>
						<p class="nv-widget__empty"><?php esc_html_e( 'عضوی ثبت نشده است. از منوی «تیم ما» در پیشخوان اعضا را اضافه کنید.', 'novin-ai' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
