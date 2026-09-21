<?php
/**
 * Widget: Neo Team (تیم ما).
 *
 * @package Neomorph\Elementor\Widgets
 */

namespace Neomorph\Elementor\Widgets;

use Neomorph\Elementor\Neo_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Team
 */
class Widget_Team extends Neo_Widget_Base {

	public function get_name() {
		return 'neomorph-team';
	}

	public function get_title() {
		return esc_html__( 'تیم ما', 'neomorph' );
	}

	public function get_icon() {
		return 'eicon-people';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'neomorph' ) ) );

		$this->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان بخش', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'تیم ما', 'neomorph' ),
		) );

		$this->add_control( 'layout', array(
			'label'   => esc_html__( 'چیدمان', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'cards',
			'options' => array(
				'cards'     => esc_html__( 'کارت‌ها', 'neomorph' ),
				'circles'   => esc_html__( 'پرتره دایره‌ای', 'neomorph' ),
				'horizontal'=> esc_html__( 'نوار افقی (اسکرول)', 'neomorph' ),
			),
		) );

		$repeater = new \Elementor\Repeater();
		$repeater->add_control( 'photo', array(
			'label'   => esc_html__( 'عکس', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::MEDIA,
		) );
		$repeater->add_control( 'name', array(
			'label'   => esc_html__( 'نام', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'نام همکار', 'neomorph' ),
		) );
		$repeater->add_control( 'role', array(
			'label'   => esc_html__( 'سمت', 'neomorph' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'سمت شغلی', 'neomorph' ),
		) );
		$repeater->add_control( 'bio', array(
			'label' => esc_html__( 'بیوی کوتاه', 'neomorph' ),
			'type'  => \Elementor\Controls_Manager::TEXTAREA,
		) );
		$socials = new \Elementor\Repeater();
		$socials->add_control( 'slabel', array( 'label' => esc_html__( 'شبکه', 'neomorph' ), 'type' => \Elementor\Controls_Manager::TEXT ) );
		$socials->add_control( 'surl', array( 'label' => esc_html__( 'لینک', 'neomorph' ), 'type' => \Elementor\Controls_Manager::URL ) );
		$repeater->add_control( 'socials', array(
			'label'  => esc_html__( 'شبکه‌های اجتماعی', 'neomorph' ),
			'type'   => \Elementor\Controls_Manager::REPEATER,
			'fields' => $socials->get_controls(),
		) );

		$this->add_control( 'members', array(
			'label'       => esc_html__( 'اعضای تیم', 'neomorph' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'title_field' => '{{{ name }}}',
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'photo_style', array(
			'label' => esc_html__( 'عکس و متن', 'neomorph' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'photo_radius', array(
			'label'     => esc_html__( 'گردی عکس', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::SLIDER,
			'range'     => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
			'selectors' => array( '{{WRAPPER}} .neo-team-card img' => 'border-radius: calc({{SIZE}} * 1vw + {{SIZE}} * 1px);' ),
		) );
		$this->add_control( 'name_color', array(
			'label'     => esc_html__( 'رنگ نام', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .neo-team-card h3' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'role_color', array(
			'label'     => esc_html__( 'رنگ سمت', 'neomorph' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .neo-team-card .neo-team-card__role' => 'color: {{VALUE}};' ),
		) );
		$this->end_controls_section();

		$this->add_neo_style_controls( array( 'label' => esc_html__( 'استایل کارت', 'neomorph' ) ) );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="neo-widget neo-team neo-team--<?php echo esc_attr( $s['layout'] ); ?> <?php echo esc_attr( $this->neo_widget_class() ); ?>">
			<?php if ( $s['title'] ) : ?>
				<h2 class="neo-section__title"><?php echo esc_html( $s['title'] ); ?></h2>
			<?php endif; ?>
			<div class="neo-grid neo-grid--4 neo-team__grid">
				<?php foreach ( $s['members'] as $member ) : ?>
					<article class="neo-card neo-team-card">
						<div class="neo-team-card__photo neo-inset">
							<?php if ( ! empty( $member['photo']['url'] ) ) : ?>
								<img src="<?php echo esc_url( $member['photo']['url'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>">
							<?php else : ?>
								<span class="neo-team-card__placeholder">👤</span>
							<?php endif; ?>
						</div>
						<h3><?php echo esc_html( $member['name'] ); ?></h3>
						<p class="neo-team-card__role"><?php echo esc_html( $member['role'] ); ?></p>
						<?php if ( $member['bio'] ) : ?>
							<p class="neo-team-card__bio"><?php echo esc_html( $member['bio'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $member['socials'] ) ) : ?>
							<div class="neo-team-card__socials">
								<?php foreach ( $member['socials'] as $soc ) : ?>
									<a class="neo-social" href="<?php echo esc_url( isset( $soc['surl']['url'] ) ? $soc['surl']['url'] : '#' ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $soc['slabel'] ); ?></a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}
