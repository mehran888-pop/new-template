<?php
/**
 * Widget: Neo Social (شبکه‌های اجتماعی).
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Social
 */
class Widget_Social extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-social';
	}

	public function get_title() {
		return esc_html__( 'شبکه‌های اجتماعی', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-share-link';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان (اختیاری)', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
		) );

		$this->add_control( 'layout', array(
			'label'   => esc_html__( 'چیدمان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'pills',
			'options' => array(
				'pills'   => esc_html__( 'کپسول‌های نرم', 'neomorph' ),
				'circles' => esc_html__( 'دکمه‌های دایره‌ای', 'neomorph' ),
				'row'     => esc_html__( 'ردیف متنی ساده', 'neomorph' ),
			),
		) );

		$this->add_control( 'source', array(
			'label'   => esc_html__( 'منبع لینک‌ها', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'custom',
			'options' => array(
				'custom' => esc_html__( 'ورود دستی', 'neomorph' ),
				'theme'  => esc_html__( 'تنظیمات قالب', 'neomorph' ),
			),
		) );

		$rep = new \Elementor\Repeater();
		$rep->add_control( 'label', array(
			'label'   => esc_html__( 'نام', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Instagram',
		) );
		$rep->add_control( 'icon', array(
			'label'   => esc_html__( 'آیکون', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '◆',
		) );
		$rep->add_control( 'url', array(
			'label' => esc_html__( 'لینک', 'neomorph' ),
			'type'  => \Elementor\Controls_Manager::URL,
		) );

		$this->add_control( 'items', array(
			'label'  => esc_html__( 'شبکه‌ها', 'neomorph' ),
			'type'   => \Elementor\Controls_Manager::REPEATER,
			'fields' => $rep->get_controls(),
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'btn_style', array(
			'label' => esc_html__( 'دکمه‌ها', 'neomorph' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'hover_color', array(
			'label'     => esc_html__( 'رنگ حالت هاور', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#6c5ce7',
			'selectors' => array(
				'{{WRAPPER}} .neo-social:hover' => 'color: {{VALUE}};',
			),
		) );
		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل کلی', 'neomorph' ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$items = 'theme' === $s['source'] ? (array) neomorph_option( 'socials', array() ) : $s['items'];
		?>
		<section class="neo-widget neo-social-list neo-social-list--<?php echo esc_attr( $s['layout'] ); ?> <?php echo esc_attr( $this->neo_widget_class() ); ?>">
			<?php if ( $s['title'] ) : ?><h2 class="neo-section__title"><?php echo esc_html( $s['title'] ); ?></h2><?php endif; ?>
			<div class="neo-social-list__row">
				<?php foreach ( $items as $item ) : ?>
					<?php
					$url   = isset( $item['url']['url'] ) ? $item['url']['url'] : ( isset( $item['url'] ) && is_string( $item['url'] ) ? $item['url'] : '#' );
					$label = isset( $item['label'] ) ? $item['label'] : '';
					$icon  = isset( $item['icon'] ) && $item['icon'] ? $item['icon'] : '◆';
					?>
					<a class="neo-social" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="neo-social__icon" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
						<span class="neo-social__label"><?php echo esc_html( $label ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}
