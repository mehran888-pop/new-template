<?php
/**
 * Base widget + shared neumorphic control helpers.
 *
 * @package Neomorph\Elementor
 */

namespace Neomorph\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Neo_Widget_Base
 */
abstract class Neo_Widget_Base extends \Elementor\Widget_Base {

	/**
	 * Category slug.
	 */
	public function get_categories() {
		return array( 'neomorph' );
	}

	/**
	 * Common style controls for any neo surface.
	 *
	 * @param array $args {
	 *     @type string $prefix  Control id prefix (default 'neo_').
	 *     @type bool   $shadow  Add shadow style selector (default true).
	 *     @type string $label   Section label.
	 * }
	 */
	protected function add_neo_style_controls( $args = array() ) {
		$prefix = isset( $args['prefix'] ) ? $args['prefix'] : 'neo_';
		$label  = isset( $args['label'] ) ? $args['label'] : esc_html__( 'استایل نئومورف', 'neomorph' );

		$this->start_controls_section(
			$prefix . 'style_section',
			array(
				'label' => $label,
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			$prefix . 'align',
			array(
				'label'     => esc_html__( 'چیدمان', 'neomorph' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'right'  => array( 'title' => esc_html__( 'راست', 'neomorph' ), 'icon' => 'eicon-text-align-right' ),
					'center' => array( 'title' => esc_html__( 'وسط', 'neomorph' ), 'icon' => 'eicon-text-align-center' ),
					'left'   => array( 'title' => esc_html__( 'چپ', 'neomorph' ), 'icon' => 'eicon-text-align-left' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .neo-widget' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$prefix . 'layout',
			array(
				'label'   => esc_html__( 'چیدمان ظاهری', 'neomorph' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'raised',
				'options' => array(
					'raised'    => esc_html__( 'برآمده (Raised)', 'neomorph' ),
					'inset'     => esc_html__( 'فرورفته (Inset)', 'neomorph' ),
					'flat'      => esc_html__( 'تخت بدون سایه', 'neomorph' ),
					'pill'      => esc_html__( 'کپسولی نرم', 'neomorph' ),
				),
			)
		);

		$this->add_control(
			$prefix . 'bg',
			array(
				'label'     => esc_html__( 'رنگ پس‌زمینه', 'neomorph' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .neo-widget' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$prefix . 'text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'neomorph' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .neo-widget' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$prefix . 'accent',
			array(
				'label'     => esc_html__( 'رنگ اکشن', 'neomorph' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .neo-widget .neo-btn--primary' => 'background: {{VALUE}}; color: #fff;',
					'{{WRAPPER}} .neo-widget a'                 => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			$prefix . 'radius',
			array(
				'label'      => esc_html__( 'گردی گوشه', 'neomorph' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .neo-widget' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			$prefix . 'padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'neomorph' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .neo-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			$prefix . 'margin',
			array(
				'label'      => esc_html__( 'فاصله بیرونی', 'neomorph' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .neo-widget' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => $prefix . 'typography',
				'label'    => esc_html__( 'تایپوگرافی', 'neomorph' ),
				'selector' => '{{WRAPPER}} .neo-widget',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => $prefix . 'shadow',
				'label'    => esc_html__( 'سایه سفارشی (در صورت نیاز)', 'neomorph' ),
				'selector' => '{{WRAPPER}} .neo-widget',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build neo widget class list from controls.
	 *
	 * @param string $prefix Control prefix.
	 * @param string $extra  Extra classes.
	 */
	protected function neo_widget_class( $prefix = 'neo_', $extra = '' ) {
		$settings = $this->get_settings_for_display();
		$layout   = isset( $settings[ $prefix . 'layout'] ) ? $settings[ $prefix . 'layout'] : 'raised';
		$map      = array(
			'raised' => 'neo-surface',
			'inset'  => 'neo-inset',
			'flat'   => 'neo-flat',
			'pill'   => 'neo-surface neo-pill',
		);
		return trim( 'neo-widget ' . ( isset( $map[ $layout ] ) ? $map[ $layout ] : 'neo-surface' ) . ' ' . $extra );
	}

	/**
	 * Repeater-style add button control shortcut.
	 */
	protected function add_icon_control( $id, $label ) {
		$this->add_control(
			$id,
			array(
				'label' => $label,
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
	}
}
