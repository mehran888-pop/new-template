<?php
/**
 * المان اختصاصی پروژه‌ها و نمونه‌کارها.
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
 * کلاس پروژه‌ها.
 */
class Projects extends Novin_AI_Widget_Base {

	/**
	 * نام المان.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'novin-projects';
	}

	/**
	 * عنوان المان.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'پروژه‌ها و نمونه‌کارها', 'novin-ai' );
	}

	/**
	 * آیکون المان.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * کلمات کلیدی.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords() {
		return array( 'projects', 'پروژه', 'نمونه کار', 'portfolio', 'سابقه کار' );
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
					'cpt'    => esc_html__( 'نوع نوشته «پروژه‌ها» (پیشنهادی)', 'novin-ai' ),
					'manual' => esc_html__( 'ورود دستی', 'novin-ai' ),
				),
			)
		);

		$this->add_control(
			'post_type',
			array(
				'label'     => esc_html__( 'منبع محتوا', 'novin-ai' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'novin_project',
				'options'   => array(
					'novin_project' => esc_html__( 'پروژه‌ها', 'novin-ai' ),
					'post'          => esc_html__( 'نوشته‌ها', 'novin-ai' ),
					'product'       => esc_html__( 'محصولات', 'novin-ai' ),
					'page'          => esc_html__( 'برگه‌ها', 'novin-ai' ),
				),
				'condition' => array( 'source' => 'cpt' ),
			)
		);

		$this->add_control(
			'taxonomy_filter',
			array(
				'label'     => esc_html__( 'فیلتر دسته‌بندی', 'novin-ai' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''                  => esc_html__( 'همه', 'novin-ai' ),
					'novin_project_cat' => esc_html__( 'دسته پروژه‌ها', 'novin-ai' ),
					'category'          => esc_html__( 'دسته نوشته‌ها', 'novin-ai' ),
					'product_cat'       => esc_html__( 'دسته محصولات', 'novin-ai' ),
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
				'label'     => esc_html__( 'تعداد آیتم', 'novin-ai' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 6,
				'min'       => 1,
				'max'       => 40,
				'condition' => array( 'source' => 'cpt' ),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => esc_html__( 'مرتب‌سازی', 'novin-ai' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => array(
					'date'       => esc_html__( 'تاریخ', 'novin-ai' ),
					'menu_order' => esc_html__( 'ترتیب دستی', 'novin-ai' ),
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
				'label' => esc_html__( 'تصویر', 'novin-ai' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'   => esc_html__( 'عنوان', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'دستیار هوشمند پشتیبانی', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'category',
			array(
				'label'   => esc_html__( 'دسته', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'هوش مصنوعی', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'client',
			array(
				'label'   => esc_html__( 'کارفرما', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'excerpt',
			array(
				'label' => esc_html__( 'توضیح کوتاه', 'novin-ai' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);

		$repeater->add_control(
			'tech',
			array(
				'label'       => esc_html__( 'تکنولوژی‌ها (با کاما)', 'novin-ai' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Python, FastAPI, React',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'       => esc_html__( 'لینک', 'novin-ai' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'آیتم‌ها', 'novin-ai' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'title'    => esc_html__( 'دستیار هوشمند پشتیبانی', 'novin-ai' ),
						'category' => esc_html__( 'هوش مصنوعی', 'novin-ai' ),
					),
					array(
						'title'    => esc_html__( 'پورتال سازمانی', 'novin-ai' ),
						'category' => esc_html__( 'طراحی وب', 'novin-ai' ),
					),
					array(
						'title'    => esc_html__( 'مانیتورینگ زیرساخت', 'novin-ai' ),
						'category' => esc_html__( 'فناوری اطلاعات', 'novin-ai' ),
					),
					array(
						'title'    => esc_html__( 'سیستم تحلیل تصویر', 'novin-ai' ),
						'category' => esc_html__( 'بینایی ماشین', 'novin-ai' ),
					),
				),
				'title_field' => '{{{ title }}}',
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$this->end_controls_section();

		$this->section_title_controls(
			array(
				'title_default' => esc_html__( 'نمونه‌کارهایی که اعتماد می‌سازند', 'novin-ai' ),
			)
		);

		/* ---------------------------------- چیدمان ---------------------------------- */
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'چیدمان و فیلتر', 'novin-ai' ),
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

		$this->layout_controls( array( 'default' => 3 ) );

		$this->add_control(
			'show_filter',
			array(
				'label'        => esc_html__( 'نمایش فیلتر دسته‌بندی', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'filter_all_text',
			array(
				'label'     => esc_html__( 'متن دکمه همه', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'همه پروژه‌ها', 'novin-ai' ),
				'condition' => array( 'show_filter' => 'yes' ),
			)
		);

		$this->add_control(
			'image_ratio',
			array(
				'label'   => esc_html__( 'نسبت تصویر', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '4/3',
				'options' => array(
					'1/1'  => '1:1',
					'4/3'  => '4:3',
					'3/4'  => '3:4',
					'16/9' => '16:9',
					'3/2'  => '3:2',
				),
				'selectors' => array(
					'{{WRAPPER}} .nv-project__media' => 'aspect-ratio: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'show_client',
			array(
				'label'        => esc_html__( 'نمایش کارفرما', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_tech',
			array(
				'label'        => esc_html__( 'نمایش تکنولوژی‌ها', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_lightbox',
			array(
				'label'        => esc_html__( 'نمایش تصویر در لایت‌باکس', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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

		$this->add_control(
			'more_text',
			array(
				'label'   => esc_html__( 'متن لینک جزئیات', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مشاهده پروژه', 'novin-ai' ),
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

		$this->card_style_controls( '{{WRAPPER}} .nv-project' );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-project__title',
			)
		);

		$this->add_control(
			'item_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-project__title, {{WRAPPER}} .nv-project__title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_typography',
				'label'    => esc_html__( 'تایپوگرافی متادیتا', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-project__client, {{WRAPPER}} .nv-project__tech span',
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => esc_html__( 'رنگ متادیتا', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-project__client, {{WRAPPER}} .nv-project__tech span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'tech_border',
				'label'    => esc_html__( 'حاشیه برچسب تکنولوژی', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-project__tech span',
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => esc_html__( 'رنگ لایه روی تصویر', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-project__overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->section_title_style_controls();
	}

	/**
	 * رندر یک کارت پروژه.
	 *
	 * @param array<string, mixed> $data     داده‌ها.
	 * @param array<string, mixed> $settings تنظیمات.
	 * @return void
	 */
	protected function render_card( $data, $settings ) {
		$classes = array( 'nv-card', 'nv-project', $this->nv_classes( $settings, array() ) );
		$cats    = ! empty( $data['cat_slugs'] ) ? $data['cat_slugs'] : array();
		?>
		<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $cats ? ' data-nv-cat="' . esc_attr( implode( ' ', $cats ) ) . '"' : ''; ?>>
			<div class="nv-project__media nv-media">
				<?php if ( ! empty( $data['image'] ) ) : ?>
					<img class="nv-media__img" src="<?php echo esc_url( $data['image'] ); ?>" alt="<?php echo esc_attr( $data['title'] ); ?>" loading="lazy">
				<?php endif; ?>

				<div class="nv-project__overlay">
					<?php if ( 'yes' === $settings['show_lightbox'] && ! empty( $data['image'] ) ) : ?>
						<a class="nv-project__zoom" href="<?php echo esc_url( $data['image'] ); ?>" data-nv-lightbox aria-label="<?php echo esc_attr( $data['title'] ); ?>">
							<?php echo novin_ai_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</a>
					<?php endif; ?>

					<?php if ( ! empty( $data['link'] ) ) : ?>
						<a class="nv-project__link" href="<?php echo esc_url( $data['link'] ); ?>">
							<span><?php echo esc_html( $settings['more_text'] ); ?></span>
							<?php echo novin_ai_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $data['category'] ) ) : ?>
					<span class="nv-badge"><?php echo esc_html( $data['category'] ); ?></span>
				<?php endif; ?>
			</div>

			<div class="nv-project__body">
				<h3 class="nv-project__title">
					<?php if ( ! empty( $data['link'] ) ) : ?>
						<a href="<?php echo esc_url( $data['link'] ); ?>"><?php echo esc_html( $data['title'] ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $data['title'] ); ?>
					<?php endif; ?>
				</h3>

				<?php if ( 'yes' === $settings['show_client'] && ! empty( $data['client'] ) ) : ?>
					<p class="nv-project__client"><?php echo esc_html( $data['client'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $data['excerpt'] ) ) : ?>
					<p class="nv-project__excerpt"><?php echo esc_html( $data['excerpt'] ); ?></p>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_tech'] && ! empty( $data['tech'] ) ) : ?>
					<div class="nv-project__tech">
						<?php foreach ( $data['tech'] as $tech ) : ?>
							<span><?php echo esc_html( $tech ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</article>
		<?php
	}

	/**
	 * رندر خروجی.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'nv-widget nv-section nv-widget-projects' );

		$items      = array();
		$filter_map = array();

		if ( 'cpt' === $settings['source'] ) {
			$query = $this->nv_query( $settings, 'novin_project' );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					$post_id = get_the_ID();
					$terms   = get_the_terms( $post_id, 'novin_project_cat' );
					$cat     = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
					$slugs   = array();

					if ( $terms && ! is_wp_error( $terms ) ) {
						foreach ( $terms as $term ) {
							$slugs[]          = $term->slug;
							$filter_map[ $term->slug ] = $term->name;
						}
					}

					$items[] = array(
						'title'     => get_the_title(),
						'image'     => get_the_post_thumbnail_url( $post_id, 'novin-ai-card' ),
						'category'  => $cat,
						'cat_slugs' => $slugs,
						'client'    => novin_ai_get_meta( $post_id, 'client' ),
						'excerpt'   => novin_ai_excerpt( get_the_excerpt(), 18 ),
						'tech'      => novin_ai_meta_lines( novin_ai_get_meta( $post_id, 'tech' ) ),
						'link'      => novin_ai_get_meta( $post_id, 'url' ) ? novin_ai_get_meta( $post_id, 'url' ) : get_permalink(),
					);
				}

				wp_reset_postdata();
			}
		} elseif ( ! empty( $settings['items'] ) ) {
			foreach ( $settings['items'] as $item ) {
				$slug = sanitize_title( $item['category'] );

				if ( $slug ) {
					$filter_map[ $slug ] = $item['category'];
				}

				$items[] = array(
					'title'     => $item['title'],
					'image'     => ! empty( $item['image']['url'] ) ? $item['image']['url'] : '',
					'category'  => $item['category'],
					'cat_slugs' => $slug ? array( $slug ) : array(),
					'client'    => $item['client'],
					'excerpt'   => $item['excerpt'],
					'tech'      => array_filter( array_map( 'trim', explode( ',', (string) $item['tech'] ) ) ),
					'link'      => ! empty( $item['link']['url'] ) ? $item['link']['url'] : '',
				);
			}
		}
		?>
		<section <?php $this->nv_attr( 'wrapper' ); ?>>
			<?php $this->nv_backdrop( $settings ); ?>

			<div class="nv-container">
				<?php $this->render_heading( $settings ); ?>

				<?php if ( 'yes' === $settings['show_filter'] && count( $filter_map ) > 1 ) : ?>
					<div class="nv-filters" data-nv-filters>
						<button class="nv-filter is-active" type="button" data-nv-filter="*"><?php echo esc_html( $settings['filter_all_text'] ); ?></button>
						<?php foreach ( $filter_map as $slug => $name ) : ?>
							<button class="nv-filter" type="button" data-nv-filter="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $name ); ?></button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="nv-grid" data-nv-filterable>
					<?php
					if ( ! empty( $items ) ) {
						foreach ( $items as $item ) {
							$this->render_card( $item, $settings );
						}
					} else {
						echo '<p class="nv-widget__empty">' . esc_html__( 'پروژه‌ای ثبت نشده است. از منوی «پروژه‌ها» در پیشخوان محتوا اضافه کنید.', 'novin-ai' ) . '</p>';
					}
					?>
				</div>
			</div>
		</section>
		<?php
	}
}
