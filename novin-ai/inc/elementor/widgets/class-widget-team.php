<?php
/**
 * المان اختصاصی بخش تیم — با رزومه کامل و جلوه‌های سه‌بعدی حرفه‌ای.
 *
 * امکانات:
 * - نمایش اعضا از نوع نوشته «تیم ما» یا ورود دستی
 * - رزومه کامل: مهارت‌ها (نوار درصد)، سوابق کاری، تحصیلات، گواهینامه‌ها، زبان‌ها، اطلاعات تماس
 * - پنجره رزومه (Modal) با جلوه عمق سه‌بعدی و پس‌زمینه مات
 * - جلوه‌ها: چرخش سه‌بعدی، درخشش هولوگرافیک، حلقه نوری چرخان، ظهور پله‌ای، دکمه مغناطیسی
 *
 * @package Novin_AI
 */

namespace Novin_AI\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
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
		return esc_html__( 'بخش تیم (رزومه کامل)', 'novin-ai' );
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
		return array( 'team', 'تیم', 'اعضا', 'member', 'رزومه', 'resume', 'کارکنان' );
	}

	/**
	 * شبکه‌های اجتماعی قابل نمایش برای هر عضو.
	 *
	 * @return array<string, string>
	 */
	protected function social_fields() {
		return array(
			'instagram' => esc_html__( 'اینستاگرام', 'novin-ai' ),
			'linkedin'  => esc_html__( 'لینکدین', 'novin-ai' ),
			'telegram'  => esc_html__( 'تلگرام', 'novin-ai' ),
			'twitter'   => esc_html__( 'ایکس / توییتر', 'novin-ai' ),
			'github'    => esc_html__( 'گیت‌هاب', 'novin-ai' ),
			'dribbble'  => esc_html__( 'دریبل', 'novin-ai' ),
			'website'   => esc_html__( 'وب‌سایت', 'novin-ai' ),
			'email'     => esc_html__( 'ایمیل', 'novin-ai' ),
		);
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
			'name',
			array(
				'label'   => esc_html__( 'نام و نام خانوادگی', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'سارا احمدی', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'role',
			array(
				'label'       => esc_html__( 'سمت / تخصص', 'novin-ai' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'مدیر هوش مصنوعی', 'novin-ai' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label' => esc_html__( 'تصویر پروفایل', 'novin-ai' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'experience',
			array(
				'label'   => esc_html__( 'سال‌های تجربه', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( '۱۰ سال تجربه', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'projects',
			array(
				'label'   => esc_html__( 'تعداد پروژه', 'novin-ai' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( '۴۵ پروژه موفق', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'quote',
			array(
				'label' => esc_html__( 'نقل‌قول کوتاه', 'novin-ai' ),
				'type'  => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'هوش مصنوعی زمانی ارزشمند است که مسئله‌ای واقعی را حل کند.', 'novin-ai' ),
			)
		);

		$repeater->add_control(
			'bio',
			array(
				'label' => esc_html__( 'درباره (رزومه متنی)', 'novin-ai' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 5,
			)
		);

		$repeater->add_control(
			'skills',
			array(
				'label'       => esc_html__( 'مهارت‌ها (هر خط: نام|درصد)', 'novin-ai' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => "یادگیری ماشین|95\nPython|90\nمعماری سیستم|85",
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'experience_list',
			array(
				'label'       => esc_html__( 'سوابق کاری (هر خط: سال|عنوان|شرکت)', 'novin-ai' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => "۱۴۰۰-اکنون|مدیر هوش مصنوعی|نوین ای‌آی\n۱۳۹۶-۱۴۰۰|دانشمند داده|داده‌پردازان",
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'education',
			array(
				'label'       => esc_html__( 'تحصیلات (هر خط: مقطع|رشته|دانشگاه)', 'novin-ai' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => "دکتری|هوش مصنوعی|دانشگاه صنعتی شریف",
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'certifications',
			array(
				'label'       => esc_html__( 'گواهینامه‌ها (هر خط: عنوان|سال)', 'novin-ai' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => "AWS Machine Learning|1402\nTensorFlow Developer|1400",
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'languages',
			array(
				'label'       => esc_html__( 'زبان‌ها (هر خط: زبان|سطح)', 'novin-ai' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => "فارسی|زبان مادری\nانگلیسی|تسلط کامل",
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'location',
			array(
				'label' => esc_html__( 'شهر / محل کار', 'novin-ai' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'email',
			array(
				'label' => esc_html__( 'ایمیل', 'novin-ai' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'phone',
			array(
				'label' => esc_html__( 'تلفن', 'novin-ai' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'resume',
			array(
				'label'       => esc_html__( 'لینک فایل رزومه (PDF)', 'novin-ai' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);

		foreach ( $this->social_fields() as $key => $label ) {
			$repeater->add_control(
				$key,
				array(
					'label' => $label,
					'type'  => 'email' === $key ? Controls_Manager::TEXT : Controls_Manager::URL,
				)
			);
		}

		$repeater->add_control(
			'featured',
			array(
				'label'        => esc_html__( 'عضو ویژه', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => '',
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
						'name'       => esc_html__( 'سارا احمدی', 'novin-ai' ),
						'role'       => esc_html__( 'مدیر هوش مصنوعی', 'novin-ai' ),
						'experience' => esc_html__( '۱۲ سال تجربه', 'novin-ai' ),
						'projects'   => esc_html__( '۶۰ پروژه موفق', 'novin-ai' ),
						'featured'   => 'yes',
					),
					array(
						'name'       => esc_html__( 'علی رضایی', 'novin-ai' ),
						'role'       => esc_html__( 'معمار نرم‌افزار', 'novin-ai' ),
						'experience' => esc_html__( '۹ سال تجربه', 'novin-ai' ),
						'projects'   => esc_html__( '۴۲ پروژه موفق', 'novin-ai' ),
					),
					array(
						'name'       => esc_html__( 'نگار محمدی', 'novin-ai' ),
						'role'       => esc_html__( 'مدیر محصول', 'novin-ai' ),
						'experience' => esc_html__( '۷ سال تجربه', 'novin-ai' ),
						'projects'   => esc_html__( '۳۵ پروژه موفق', 'novin-ai' ),
					),
					array(
						'name'       => esc_html__( 'مهدی کریمی', 'novin-ai' ),
						'role'       => esc_html__( 'مدیر زیرساخت و امنیت', 'novin-ai' ),
						'experience' => esc_html__( '۱۵ سال تجربه', 'novin-ai' ),
						'projects'   => esc_html__( '۸۰ پروژه موفق', 'novin-ai' ),
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
			'layout',
			array(
				'label'   => esc_html__( 'نوع نمایش', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid' => esc_html__( 'کارت شبکه‌ای', 'novin-ai' ),
					'list' => esc_html__( 'ردیف افقی (رزومه کوتاه)', 'novin-ai' ),
				),
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
			'avatar_shape',
			array(
				'label'   => esc_html__( 'قالب تصویر', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'rounded',
				'options' => array(
					'rounded' => esc_html__( 'گردی ملایم', 'novin-ai' ),
					'circle'  => esc_html__( 'دایره', 'novin-ai' ),
					'square'  => esc_html__( 'مربع', 'novin-ai' ),
				),
			)
		);

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
			'skills_limit',
			array(
				'label'   => esc_html__( 'تعداد مهارت روی کارت', 'novin-ai' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3,
				'min'     => 0,
				'max'     => 10,
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- نمایش ---------------------------------- */
		$this->start_controls_section(
			'section_display',
			array(
				'label' => esc_html__( 'موارد نمایش', 'novin-ai' ),
			)
		);

		$toggles = array(
			'show_role'       => esc_html__( 'سمت', 'novin-ai' ),
			'show_quote'      => esc_html__( 'نقل‌قول', 'novin-ai' ),
			'show_bio'        => esc_html__( 'متن کوتاه درباره', 'novin-ai' ),
			'show_meta'       => esc_html__( 'اطلاعات تماس و آمار', 'novin-ai' ),
			'show_skills'     => esc_html__( 'نوار مهارت‌ها روی کارت', 'novin-ai' ),
			'show_socials'    => esc_html__( 'شبکه‌های اجتماعی', 'novin-ai' ),
			'show_badge'      => esc_html__( 'برچسب تجربه روی تصویر', 'novin-ai' ),
			'show_resume_btn' => esc_html__( 'دکمه دانلود رزومه', 'novin-ai' ),
		);

		foreach ( $toggles as $key => $label ) {
			$default = 'show_bio' === $key ? '' : 'yes';

			$this->add_control(
				$key,
				array(
					'label'        => $label,
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'بله', 'novin-ai' ),
					'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
					'return_value' => 'yes',
					'default'      => $default,
				)
			);
		}

		$this->add_control(
			'resume_trigger',
			array(
				'label'       => esc_html__( 'شیوه نمایش رزومه کامل', 'novin-ai' ),
				'description' => esc_html__( 'در حالت «هاور» رزومه کامل به صورت پاپ‌آپ روی کارت ظاهر می‌شود و در موبایل با لمس کارت پنجره باز می‌شود.', 'novin-ai' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'hover',
				'options'     => array(
					'hover' => esc_html__( 'پاپ‌آپ در هاور موس (پیشنهادی)', 'novin-ai' ),
					'both'  => esc_html__( 'پاپ‌آپ در هاور + دکمه پنجره', 'novin-ai' ),
					'click' => esc_html__( 'فقط دکمه و پنجره', 'novin-ai' ),
				),
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'modal_text',
			array(
				'label'     => esc_html__( 'متن دکمه رزومه', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'مشاهده رزومه کامل', 'novin-ai' ),
				'condition' => array( 'resume_trigger' => array( 'click', 'both' ) ),
			)
		);

		$this->add_control(
			'resume_text',
			array(
				'label'     => esc_html__( 'متن دکمه دانلود', 'novin-ai' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'دانلود PDF', 'novin-ai' ),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- بخش‌های رزومه ---------------------------------- */
		$this->start_controls_section(
			'section_resume',
			array(
				'label' => esc_html__( 'بخش‌های رزومه (پاپ‌آپ و پنجره)', 'novin-ai' ),
			)
		);

		$sections = array(
			'modal_about'          => esc_html__( 'درباره من', 'novin-ai' ),
			'modal_skills'         => esc_html__( 'مهارت‌های تخصصی', 'novin-ai' ),
			'modal_experience'     => esc_html__( 'سوابق کاری', 'novin-ai' ),
			'modal_education'      => esc_html__( 'تحصیلات', 'novin-ai' ),
			'modal_certifications' => esc_html__( 'گواهینامه‌ها', 'novin-ai' ),
			'modal_languages'      => esc_html__( 'زبان‌ها', 'novin-ai' ),
			'modal_contact'        => esc_html__( 'راه‌های ارتباطی', 'novin-ai' ),
		);

		foreach ( $sections as $key => $label ) {
			$this->add_control(
				$key,
				array(
					'label'        => $label,
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'نمایش', 'novin-ai' ),
					'label_off'    => esc_html__( 'پنهان', 'novin-ai' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
		}

		$this->end_controls_section();

		/* ---------------------------------- جلوه‌ها ---------------------------------- */
		$this->start_controls_section(
			'section_effects',
			array(
				'label' => esc_html__( 'جلوه‌های سه‌بعدی', 'novin-ai' ),
			)
		);

		$this->add_control(
			'enable_tilt',
			array(
				'label'        => esc_html__( 'چرخش سه‌بعدی کارت', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'enable_shine',
			array(
				'label'        => esc_html__( 'درخشش هولوگرافیک روی تصویر', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'enable_ring',
			array(
				'label'        => esc_html__( 'حلقه نوری چرخان دور تصویر', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'enable_aura',
			array(
				'label'        => esc_html__( 'هاله رنگی پشت کارت', 'novin-ai' ),
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
				'label'        => esc_html__( 'ظهور پله‌ای', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'stagger',
			array(
				'label'      => esc_html__( 'تأخیر پله‌ای (میلی‌ثانیه)', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'ms' ),
				'range'      => array(
					'ms' => array(
						'min' => 0,
						'max' => 400,
					),
				),
				'default'    => array( 'size' => 110, 'unit' => 'ms' ),
				'condition'  => array( 'enable_reveal' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- پاپ‌آپ هاور ---------------------------------- */
		$this->start_controls_section(
			'section_hover',
			array(
				'label'     => esc_html__( 'پاپ‌آپ رزومه در هاور', 'novin-ai' ),
				'condition' => array( 'resume_trigger' => array( 'hover', 'both' ) ),
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'   => esc_html__( 'جلوه نمایش', 'novin-ai' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'depth',
				'options' => array(
					'depth' => esc_html__( 'عمق سه‌بعدی (پیشنهادی)', 'novin-ai' ),
					'flip'  => esc_html__( 'برگشت سه‌بعدی (Flip)', 'novin-ai' ),
					'slide' => esc_html__( 'سر خوردن از پایین', 'novin-ai' ),
					'fade'  => esc_html__( 'محو شدن ساده', 'novin-ai' ),
					'scale' => esc_html__( 'بزرگ شدن از مرکز', 'novin-ai' ),
				),
			)
		);

		$this->add_control(
			'hover_offset',
			array(
				'label'      => esc_html__( 'بیرون‌زدگی پاپ‌آپ از کارت', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 48,
					),
				),
				'default'    => array( 'size' => 14 ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-member__popup' => '--nv-popup-offset: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hover_speed',
			array(
				'label'      => esc_html__( 'سرعت انیمیشن (میلی‌ثانیه)', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'ms' ),
				'range'      => array(
					'ms' => array(
						'min' => 120,
						'max' => 1200,
					),
				),
				'default'    => array( 'size' => 420, 'unit' => 'ms' ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-member__popup' => 'transition-duration: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hover_scroll',
			array(
				'label'        => esc_html__( 'اسکرول داخلی در صورت طولانی بودن', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'hover_max_height',
			array(
				'label'      => esc_html__( 'حداکثر ارتفاع پاپ‌آپ', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 260,
						'max' => 900,
					),
					'vh' => array(
						'min' => 30,
						'max' => 100,
					),
				),
				'default'    => array( 'size' => 620, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-member__popup-inner' => 'max-height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'hover_scroll' => 'yes' ),
			)
		);

		$this->add_control(
			'hover_show_card',
			array(
				'label'        => esc_html__( 'نمایش تصویر و نام در بالای پاپ‌آپ', 'novin-ai' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'novin-ai' ),
				'label_off'    => esc_html__( 'خیر', 'novin-ai' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->backdrop_controls();

		/* ---------------------------------- استایل کارت ---------------------------------- */
		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => esc_html__( 'استایل کارت', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->card_style_controls( '{{WRAPPER}} .nv-member' );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'featured_background',
				'label'    => esc_html__( 'پس‌زمینه عضو ویژه', 'novin-ai' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .nv-member--featured',
			)
		);

		$this->add_responsive_control(
			'avatar_size',
			array(
				'label'      => esc_html__( 'عرض تصویر در حالت ردیفی', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 120,
						'max' => 420,
					),
				),
				'default'    => array( 'size' => 240 ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-member--list .nv-member__media' => 'flex: 0 0 {{SIZE}}{{UNIT}};',
				),
			)
		);

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

		$this->add_control(
			'ring_color',
			array(
				'label'     => esc_html__( 'رنگ حلقه نوری', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8b7cff',
				'selectors' => array(
					'{{WRAPPER}} .nv-member__ring' => '--nv-ring: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- استایل متن ---------------------------------- */
		$this->start_controls_section(
			'section_text_style',
			array(
				'label' => esc_html__( 'استایل متن', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'bio_typography',
				'label'    => esc_html__( 'تایپوگرافی متن درباره', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-member__bio, {{WRAPPER}} .nv-member__quote',
			)
		);

		$this->add_control(
			'bio_color',
			array(
				'label'     => esc_html__( 'رنگ متن درباره و نقل‌قول', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-member__bio, {{WRAPPER}} .nv-member__quote' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => esc_html__( 'رنگ اطلاعات تماس', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-member__meta li' => 'color: {{VALUE}};',
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

		/* ---------------------------------- استایل مهارت‌ها ---------------------------------- */
		$this->start_controls_section(
			'section_skills_style',
			array(
				'label' => esc_html__( 'استایل مهارت‌ها', 'novin-ai' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'skill_height',
			array(
				'label'      => esc_html__( 'ارتفاع نوار', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 2,
						'max' => 24,
					),
				),
				'default'    => array( 'size' => 6 ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-skill__track' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'skill_radius',
			array(
				'label'      => esc_html__( 'انحنای نوار', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-skill__track, {{WRAPPER}} .nv-skill__bar' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'skill_track_color',
			array(
				'label'     => esc_html__( 'رنگ زمینه نوار', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.10)',
				'selectors' => array(
					'{{WRAPPER}} .nv-skill__track' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'skill_bar_color_a',
			array(
				'label'     => esc_html__( 'رنگ اول نوار', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6d5efc',
				'selectors' => array(
					'{{WRAPPER}} .nv-skill__bar' => 'background-image: linear-gradient(90deg, {{VALUE}}, {{skill_bar_color_b.VALUE}});',
				),
			)
		);

		$this->add_control(
			'skill_bar_color_b',
			array(
				'label'   => esc_html__( 'رنگ دوم نوار', 'novin-ai' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#22d3ee',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'skill_label_typography',
				'label'    => esc_html__( 'تایپوگرافی برچسب مهارت', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-skill__label, {{WRAPPER}} .nv-skill__value',
			)
		);

		$this->add_control(
			'skill_label_color',
			array(
				'label'     => esc_html__( 'رنگ برچسب مهارت', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-skill__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- استایل پنجره رزومه ---------------------------------- */
		$this->start_controls_section(
			'section_modal_style',
			array(
				'label'     => esc_html__( 'استایل پنجره رزومه', 'novin-ai' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'resume_trigger!' => 'hover' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'modal_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .nv-profile__panel',
			)
		);

		$this->add_control(
			'modal_overlay',
			array(
				'label'     => esc_html__( 'رنگ پس‌زمینه', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(3, 5, 18, 0.78)',
				'selectors' => array(
					'{{WRAPPER}} .nv-profile__backdrop' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'modal_width',
			array(
				'label'      => esc_html__( 'عرض پنجره', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 480,
						'max' => 1400,
					),
				),
				'default'    => array( 'size' => 1040, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .nv-profile__panel' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'modal_radius',
			array(
				'label'      => esc_html__( 'انحنا', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-profile__panel' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'modal_shadow',
				'selector' => '{{WRAPPER}} .nv-profile__panel',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'modal_title_typography',
				'label'    => esc_html__( 'تایپوگرافی عناوین بخش‌ها', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-profile__section h4',
			)
		);

		$this->add_control(
			'modal_title_color',
			array(
				'label'     => esc_html__( 'رنگ عناوین بخش‌ها', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-profile__section h4' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'modal_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-profile__panel' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		/* ---------------------------------- استایل پاپ‌آپ هاور ---------------------------------- */
		$this->start_controls_section(
			'section_hover_style',
			array(
				'label'     => esc_html__( 'استایل پاپ‌آپ رزومه', 'novin-ai' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'resume_trigger' => array( 'hover', 'both' ) ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'popup_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .nv-member__popup-inner',
			)
		);

		$this->add_responsive_control(
			'popup_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'novin-ai' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => '22',
					'right'    => '22',
					'bottom'   => '22',
					'left'     => '22',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-member__popup-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'popup_radius',
			array(
				'label'      => esc_html__( 'انحنا', 'novin-ai' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 48,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .nv-member__popup-inner' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'popup_shadow',
				'selector' => '{{WRAPPER}} .nv-member__popup-inner',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'popup_border',
				'selector' => '{{WRAPPER}} .nv-member__popup-inner',
			)
		);

		$this->add_control(
			'popup_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-member__popup-inner' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'popup_title_typography',
				'label'    => esc_html__( 'تایپوگرافی عناوین بخش‌ها', 'novin-ai' ),
				'selector' => '{{WRAPPER}} .nv-member__popup .nv-profile__section h4',
			)
		);

		$this->add_control(
			'popup_title_color',
			array(
				'label'     => esc_html__( 'رنگ عناوین بخش‌ها', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .nv-member__popup .nv-profile__section h4' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'popup_overlay',
			array(
				'label'     => esc_html__( 'رنگ لایه روی کارت', 'novin-ai' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(6, 8, 22, 0.72)',
				'selectors' => array(
					'{{WRAPPER}} .nv-member__popup' => 'background-color: {{VALUE}};',
				),
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
		$has = false;

		foreach ( array_keys( $this->social_fields() ) as $network ) {
			if ( ! empty( $data[ $network ] ) ) {
				$has = true;
				break;
			}
		}

		if ( ! $has ) {
			return;
		}

		echo '<div class="nv-member__socials nv-socials">';

		foreach ( $this->social_fields() as $network => $label ) {
			$value = ! empty( $data[ $network ] ) ? $data[ $network ] : '';
			$href  = '';

			if ( is_array( $value ) ) {
				$href = ! empty( $value['url'] ) ? $value['url'] : '';
			} elseif ( 'email' === $network && $value ) {
				$href = 'mailto:' . $value;
			} elseif ( 'website' === $network && $value ) {
				$href = $value;
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
				esc_attr( $label ),
				novin_ai_social_icon( $network ) // phpcs:ignore WordPress.Security.EscapeOutput
			);
		}

		echo '</div>';
	}

	/**
	 * رندر نوارهای مهارت.
	 *
	 * @param array<int, array<int, string>> $skills مهارت‌ها.
	 * @param int                            $limit  تعداد.
	 * @return void
	 */
	protected function render_skills( $skills, $limit = 3 ) {
		if ( empty( $skills ) ) {
			return;
		}

		$skills = $limit > 0 ? array_slice( $skills, 0, (int) $limit ) : $skills;

		echo '<div class="nv-member__skills">';

		foreach ( $skills as $skill ) {
			$label   = isset( $skill[0] ) ? $skill[0] : '';
			$percent = isset( $skill[1] ) ? (float) $skill[1] : 0;

			if ( ! $label ) {
				continue;
			}

			$percent = max( 0, min( 100, $percent ) );

			echo '<div class="nv-skill" data-nv-skill>';
			echo '<span class="nv-skill__label">' . esc_html( $label ) . '</span>';
			echo '<span class="nv-skill__track"><span class="nv-skill__bar" data-nv-percent="' . esc_attr( (string) $percent ) . '" style="width:0"></span></span>';
			echo '<span class="nv-skill__value">' . esc_html( (string) round( $percent ) . '%' ) . '</span>';
			echo '</div>';
		}

		echo '</div>';
	}

	/**
	 * رندر جدول زمانی (سوابق کاری / تحصیلات).
	 *
	 * @param array<int, array<int, string>> $rows سطرها.
	 * @param string                         $type نوع.
	 * @return void
	 */
	protected function render_timeline( $rows, $type = 'experience' ) {
		if ( empty( $rows ) ) {
			return;
		}

		echo '<ol class="nv-timeline nv-timeline--' . esc_attr( $type ) . '">';

		foreach ( $rows as $row ) {
			$period = isset( $row[0] ) ? $row[0] : '';
			$title  = isset( $row[1] ) ? $row[1] : '';
			$place  = isset( $row[2] ) ? $row[2] : '';

			if ( ! $title && ! $period ) {
				continue;
			}

			echo '<li class="nv-timeline__item">';
			echo '<span class="nv-timeline__dot" aria-hidden="true"></span>';
			echo '<span class="nv-timeline__period">' . esc_html( $period ) . '</span>';
			echo '<span class="nv-timeline__title">' . esc_html( $title ) . '</span>';

			if ( $place ) {
				echo '<span class="nv-timeline__place">' . esc_html( $place ) . '</span>';
			}

			echo '</li>';
		}

		echo '</ol>';
	}

	/**
	 * رندر یک بخش عنوان‌دار در پنجره رزومه.
	 *
	 * @param string $title عنوان.
	 * @param string $body  محتوا (HTML آماده).
	 * @return void
	 */
	protected function render_profile_section( $title, $body ) {
		if ( ! trim( wp_strip_all_tags( $body ) ) ) {
			return;
		}

		echo '<section class="nv-profile__section">';
		echo '<h4>' . esc_html( $title ) . '</h4>';
		echo '<div class="nv-profile__section-body">' . $body . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</section>';
	}

	/**
	 * رندر بدنه رزومه (مشترک بین پاپ‌آپ هاور و پنجره رزومه).
	 *
	 * @param array<string, mixed> $member  داده‌ها.
	 * @param array<string, mixed> $settings تنظیمات.
	 * @return void
	 */
	protected function render_resume_body( $member, $settings, $with_contact = false ) {
		if ( ! empty( $member['quote'] ) ) {
			?>
			<p class="nv-profile__quote">
				<?php echo novin_ai_icon( 'quote' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span><?php echo esc_html( $member['quote'] ); ?></span>
			</p>
			<?php
		}

		if ( 'yes' === $settings['modal_about'] && ! empty( $member['bio'] ) ) {
			$this->render_profile_section(
				esc_html__( 'درباره من', 'novin-ai' ),
				'<p>' . esc_html( $member['bio'] ) . '</p>'
			);
		}

		if ( 'yes' === $settings['modal_skills'] && ! empty( $member['skills'] ) ) {
			ob_start();
			$this->render_skills( $member['skills'], 0 );
			$this->render_profile_section( esc_html__( 'مهارت‌های تخصصی', 'novin-ai' ), (string) ob_get_clean() );
		}

		if ( 'yes' === $settings['modal_experience'] && ! empty( $member['experience_list'] ) ) {
			ob_start();
			$this->render_timeline( $member['experience_list'], 'experience' );
			$this->render_profile_section( esc_html__( 'سوابق کاری', 'novin-ai' ), (string) ob_get_clean() );
		}

		if ( 'yes' === $settings['modal_education'] && ! empty( $member['education'] ) ) {
			ob_start();
			$this->render_timeline( $member['education'], 'education' );
			$this->render_profile_section( esc_html__( 'تحصیلات', 'novin-ai' ), (string) ob_get_clean() );
		}

		if ( 'yes' === $settings['modal_certifications'] && ! empty( $member['certifications'] ) ) {
			$list = '<ul class="nv-profile__list">';

			foreach ( $member['certifications'] as $certificate ) {
				$list .= '<li><span>' . esc_html( $certificate[0] ) . '</span>';

				if ( ! empty( $certificate[1] ) ) {
					$list .= '<em>' . esc_html( $certificate[1] ) . '</em>';
				}

				$list .= '</li>';
			}

			$list .= '</ul>';

			$this->render_profile_section( esc_html__( 'گواهینامه‌ها', 'novin-ai' ), $list );
		}

		if ( 'yes' === $settings['modal_languages'] && ! empty( $member['languages'] ) ) {
			$list = '<ul class="nv-profile__list nv-profile__list--inline">';

			foreach ( $member['languages'] as $language ) {
				$list .= '<li><span>' . esc_html( $language[0] ) . '</span><em>' . esc_html( isset( $language[1] ) ? $language[1] : '' ) . '</em></li>';
			}

			$list .= '</ul>';

			$this->render_profile_section( esc_html__( 'زبان‌ها', 'novin-ai' ), $list );
		}

		if ( $with_contact && 'yes' === $settings['modal_contact'] ) {
			$contact  = '<ul class="nv-profile__contact">';
			$has_item = false;

			$rows = array(
				esc_html__( 'محل کار', 'novin-ai' ) => 'location',
				esc_html__( 'پروژه‌ها', 'novin-ai' ) => 'projects',
				esc_html__( 'تلفن', 'novin-ai' )    => 'phone',
				esc_html__( 'ایمیل', 'novin-ai' )   => 'email',
			);

			foreach ( $rows as $label => $key ) {
				if ( empty( $member[ $key ] ) ) {
					continue;
				}

				$has_item = true;
				$contact .= '<li><span>' . esc_html( $label ) . '</span><strong' . ( in_array( $key, array( 'phone', 'email' ), true ) ? ' dir="ltr"' : '' ) . '>' . esc_html( $member[ $key ] ) . '</strong></li>';
			}

			$contact .= '</ul>';

			if ( $has_item ) {
				$this->render_profile_section( esc_html__( 'راه‌های ارتباطی', 'novin-ai' ), $contact );
			}
		}
	}

	/**
	 * رندر پاپ‌آپ رزومه روی کارت (نمایش در هاور).
	 *
	 * @param array<string, mixed> $member  داده‌ها.
	 * @param array<string, mixed> $settings تنظیمات.
	 * @return void
	 */
	protected function render_popup( $member, $settings ) {
		?>
		<div class="nv-member__popup">
			<div class="nv-member__popup-inner nv-glass">
				<?php if ( 'yes' === $settings['hover_show_card'] ) : ?>
					<header class="nv-member__popup-head">
						<?php if ( ! empty( $member['image'] ) ) : ?>
							<span class="nv-member__popup-avatar"><img src="<?php echo esc_url( $member['image'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy"></span>
						<?php else : ?>
							<span class="nv-member__popup-avatar nv-member__popup-avatar--empty" aria-hidden="true"><?php echo novin_ai_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php endif; ?>

						<span class="nv-member__popup-id">
							<strong><?php echo esc_html( $member['name'] ); ?></strong>

							<?php if ( ! empty( $member['role'] ) ) : ?>
								<em><?php echo esc_html( $member['role'] ); ?></em>
							<?php endif; ?>
						</span>

						<?php if ( ! empty( $member['experience'] ) ) : ?>
							<span class="nv-badge nv-badge--glow"><?php echo esc_html( $member['experience'] ); ?></span>
						<?php endif; ?>
					</header>
				<?php endif; ?>

				<div class="nv-member__popup-body">
					<?php $this->render_resume_body( $member, $settings, true ); ?>
				</div>

				<footer class="nv-member__popup-foot">
					<?php $this->render_socials( $member ); ?>

					<?php if ( 'yes' === $settings['show_resume_btn'] && ! empty( $member['resume'] ) ) : ?>
						<?php $this->nv_link_open( $member, 'resume', 'nv-btn nv-btn--primary nv-btn--sm' ); ?>
						<span><?php echo esc_html( $settings['resume_text'] ); ?></span>
						<?php $this->nv_link_close( $member, 'resume' ); ?>
					<?php endif; ?>
				</footer>
			</div>
		</div>
		<?php
	}

	/**
	 * رندر کارت یک عضو.
	 *
	 * @param array<string, mixed> $member  داده‌ها.
	 * @param array<string, mixed> $settings تنظیمات.
	 * @param int                  $index   شماره.
	 * @return void
	 */
	protected function render_card( $member, $settings, $index ) {
		$widget_id = $this->get_id();
		$target    = 'nv-profile-' . $widget_id . '-' . $index;

		$classes = array( 'nv-card', 'nv-member', 'nv-member--' . sanitize_html_class( $settings['layout'] ) );
		$classes[] = $this->nv_classes( $settings, array() );
		$classes[] = 'nv-member--shape-' . sanitize_html_class( $settings['avatar_shape'] );

		if ( ! empty( $member['featured'] ) && 'yes' === $member['featured'] ) {
			$classes[] = 'nv-member--featured';
		}

		if ( 'yes' === $settings['enable_shine'] ) {
			$classes[] = 'nv-member--shine';
		}

		if ( 'yes' === $settings['enable_ring'] ) {
			$classes[] = 'nv-member--ring';
		}

		if ( 'yes' === $settings['enable_aura'] ) {
			$classes[] = 'nv-member--aura';
		}

		// حالت نمایش رزومه: پاپ‌آپ در هاور / دکمه / هر دو.
		$trigger = ! empty( $settings['resume_trigger'] ) ? $settings['resume_trigger'] : 'hover';
		$hover   = in_array( $trigger, array( 'hover', 'both' ), true );

		if ( $hover ) {
			$classes[] = 'nv-member--hover';
			$classes[] = 'nv-member--hover-' . sanitize_html_class( ! empty( $settings['hover_effect'] ) ? $settings['hover_effect'] : 'depth' );
		}

		if ( 'yes' !== ( ! empty( $settings['hover_scroll'] ) ? $settings['hover_scroll'] : 'yes' ) ) {
			$classes[] = 'nv-member--popup-noscroll';
		}

		$delay = ( 'yes' === $settings['enable_reveal'] && ! empty( $settings['stagger']['size'] ) )
			? ( (int) $settings['stagger']['size'] * $index )
			: 0;
		?>
		<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $delay ? ' data-nv-delay="' . esc_attr( (string) $delay ) . '"' : ''; ?><?php echo 'hover' === $trigger ? ' data-nv-profile-open="' . esc_attr( $target ) . '" tabindex="0"' : ''; ?>>

			<div class="nv-member__media nv-media">
				<?php if ( ! empty( $member['image'] ) ) : ?>
					<img class="nv-media__img" src="<?php echo esc_url( $member['image'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy">
				<?php else : ?>
					<span class="nv-media__placeholder" aria-hidden="true"><?php echo novin_ai_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<?php endif; ?>

				<span class="nv-member__shine" aria-hidden="true"></span>
				<span class="nv-member__ring" aria-hidden="true"></span>

				<?php if ( 'yes' === $settings['show_badge'] && ! empty( $member['experience'] ) ) : ?>
					<span class="nv-member__badge nv-badge nv-badge--glow"><?php echo esc_html( $member['experience'] ); ?></span>
				<?php endif; ?>

				<?php if ( ! empty( $member['featured'] ) && 'yes' === $member['featured'] ) : ?>
					<span class="nv-member__crown nv-badge nv-badge--sale"><?php esc_html_e( 'عضو ویژه', 'novin-ai' ); ?></span>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_socials'] ) : ?>
					<div class="nv-member__socials-wrap">
						<?php $this->render_socials( $member ); ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="nv-member__body">
				<h3 class="nv-member__name"><?php echo esc_html( $member['name'] ); ?></h3>

				<?php if ( 'yes' === $settings['show_role'] && ! empty( $member['role'] ) ) : ?>
					<p class="nv-member__role"><?php echo esc_html( $member['role'] ); ?></p>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_quote'] && ! empty( $member['quote'] ) ) : ?>
					<blockquote class="nv-member__quote"><?php echo esc_html( $member['quote'] ); ?></blockquote>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_bio'] && ! empty( $member['bio'] ) ) : ?>
					<p class="nv-member__bio"><?php echo esc_html( $member['bio'] ); ?></p>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_meta'] ) : ?>
					<ul class="nv-member__meta">
						<?php if ( ! empty( $member['location'] ) ) : ?>
							<li><i>📍</i><span><?php echo esc_html( $member['location'] ); ?></span></li>
						<?php endif; ?>

						<?php if ( ! empty( $member['projects'] ) ) : ?>
							<li><i>🚀</i><span><?php echo esc_html( $member['projects'] ); ?></span></li>
						<?php endif; ?>

						<?php if ( ! empty( $member['phone'] ) ) : ?>
							<li><i>☎</i><span dir="ltr"><?php echo esc_html( $member['phone'] ); ?></span></li>
						<?php endif; ?>

						<?php if ( ! empty( $member['email'] ) ) : ?>
							<li><i>✉</i><span dir="ltr"><?php echo esc_html( $member['email'] ); ?></span></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>

				<?php
				if ( 'yes' === $settings['show_skills'] && ! empty( $member['skills'] ) ) {
					$this->render_skills( $member['skills'], (int) $settings['skills_limit'] );
				}
				?>

				<div class="nv-member__actions">
					<?php if ( in_array( $trigger, array( 'click', 'both' ), true ) ) : ?>
						<button class="nv-btn nv-btn--primary nv-btn--sm nv-magnetic" type="button" data-nv-profile-open="<?php echo esc_attr( $target ); ?>">
							<span><?php echo esc_html( $settings['modal_text'] ); ?></span>
							<?php echo novin_ai_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</button>
					<?php endif; ?>

					<?php if ( 'yes' === $settings['show_resume_btn'] && ! empty( $member['resume'] ) ) : ?>
						<?php $this->nv_link_open( $member, 'resume', 'nv-btn nv-btn--outline nv-btn--sm' ); ?>
						<span><?php echo esc_html( $settings['resume_text'] ); ?></span>
						<?php echo novin_ai_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php $this->nv_link_close( $member, 'resume' ); ?>
					<?php endif; ?>
				</div>
			</div>

			<?php
			if ( $hover ) {
				$this->render_popup( $member, $settings );
			}
			?>
		</article>
		<?php
	}

	/**
	 * رندر پنجره رزومه یک عضو.
	 *
	 * @param array<string, mixed> $member  داده‌ها.
	 * @param array<string, mixed> $settings تنظیمات.
	 * @param int                  $index   شماره.
	 * @return void
	 */
	protected function render_modal( $member, $settings, $index ) {
		$widget_id = $this->get_id();
		$target    = 'nv-profile-' . $widget_id . '-' . $index;
		?>
		<div class="nv-profile" id="<?php echo esc_attr( $target ); ?>" data-nv-profile-modal aria-hidden="true">
			<div class="nv-profile__backdrop" data-nv-profile-close></div>

			<div class="nv-profile__panel nv-glass" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( $member['name'] ); ?>">
				<button class="nv-profile__close" type="button" aria-label="<?php esc_attr_e( 'بستن', 'novin-ai' ); ?>" data-nv-profile-close>
					<?php echo novin_ai_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>

				<span class="nv-orb nv-orb--1" aria-hidden="true"></span>

				<div class="nv-profile__grid">
					<aside class="nv-profile__side">
						<div class="nv-profile__avatar nv-media">
							<?php if ( ! empty( $member['image'] ) ) : ?>
								<img src="<?php echo esc_url( $member['image'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy">
							<?php endif; ?>
							<span class="nv-member__ring" aria-hidden="true"></span>
						</div>

						<h3 class="nv-profile__name"><?php echo esc_html( $member['name'] ); ?></h3>

						<?php if ( ! empty( $member['role'] ) ) : ?>
							<p class="nv-profile__role"><?php echo esc_html( $member['role'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $member['experience'] ) ) : ?>
							<span class="nv-badge nv-badge--glow"><?php echo esc_html( $member['experience'] ); ?></span>
						<?php endif; ?>

						<?php $this->render_socials( $member ); ?>

						<?php if ( 'yes' === $settings['modal_contact'] ) : ?>
							<ul class="nv-profile__contact">
								<?php if ( ! empty( $member['location'] ) ) : ?>
									<li><span><?php esc_html_e( 'محل کار', 'novin-ai' ); ?></span><strong><?php echo esc_html( $member['location'] ); ?></strong></li>
								<?php endif; ?>

								<?php if ( ! empty( $member['projects'] ) ) : ?>
									<li><span><?php esc_html_e( 'پروژه‌ها', 'novin-ai' ); ?></span><strong><?php echo esc_html( $member['projects'] ); ?></strong></li>
								<?php endif; ?>

								<?php if ( ! empty( $member['phone'] ) ) : ?>
									<li><span><?php esc_html_e( 'تلفن', 'novin-ai' ); ?></span><strong dir="ltr"><?php echo esc_html( $member['phone'] ); ?></strong></li>
								<?php endif; ?>

								<?php if ( ! empty( $member['email'] ) ) : ?>
									<li><span><?php esc_html_e( 'ایمیل', 'novin-ai' ); ?></span><strong dir="ltr"><?php echo esc_html( $member['email'] ); ?></strong></li>
								<?php endif; ?>
							</ul>
						<?php endif; ?>

						<?php if ( 'yes' === $settings['show_resume_btn'] && ! empty( $member['resume'] ) ) : ?>
							<?php $this->nv_link_open( $member, 'resume', 'nv-btn nv-btn--primary nv-btn--sm' ); ?>
							<span><?php echo esc_html( $settings['resume_text'] ); ?></span>
							<?php $this->nv_link_close( $member, 'resume' ); ?>
						<?php endif; ?>
					</aside>

					<div class="nv-profile__main">
						<?php $this->render_resume_body( $member, $settings ); ?>
					</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * رندر خروجی.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'nv-widget nv-section nv-widget-team nv-team nv-team--' . sanitize_html_class( $settings['layout'] ) );

		$members = array();

		if ( 'cpt' === $settings['source'] ) {
			$settings['post_type'] = 'novin_team';
			$query                 = $this->nv_query( $settings, 'novin_team' );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					$post_id = get_the_ID();
					$content = get_the_content();

					$members[] = array(
						'name'            => get_the_title(),
						'role'            => novin_ai_get_meta( $post_id, 'role' ),
						'image'           => get_the_post_thumbnail_url( $post_id, 'novin-ai-portrait' ),
						'experience'      => novin_ai_get_meta( $post_id, 'experience' ),
						'projects'        => novin_ai_get_meta( $post_id, 'projects' ),
						'quote'           => novin_ai_get_meta( $post_id, 'quote' ),
						'bio'             => novin_ai_excerpt( get_the_excerpt() ? get_the_excerpt() : $content, 60, '' ),
						'skills'          => novin_ai_meta_rows( novin_ai_get_meta( $post_id, 'skills' ), 2 ),
						'experience_list' => novin_ai_meta_rows( novin_ai_get_meta( $post_id, 'experience_list' ), 3 ),
						'education'       => novin_ai_meta_rows( novin_ai_get_meta( $post_id, 'education' ), 3 ),
						'certifications'  => novin_ai_meta_rows( novin_ai_get_meta( $post_id, 'certifications' ), 2 ),
						'languages'       => novin_ai_meta_rows( novin_ai_get_meta( $post_id, 'languages' ), 2 ),
						'location'        => novin_ai_get_meta( $post_id, 'location' ),
						'email'           => novin_ai_get_meta( $post_id, 'email' ),
						'phone'           => novin_ai_get_meta( $post_id, 'phone' ),
						'resume'          => array( 'url' => novin_ai_get_meta( $post_id, 'resume' ) ),
						'featured'        => novin_ai_get_meta( $post_id, 'featured' ) ? 'yes' : '',
						'instagram'       => novin_ai_get_meta( $post_id, 'instagram' ),
						'linkedin'        => novin_ai_get_meta( $post_id, 'linkedin' ),
						'telegram'        => novin_ai_get_meta( $post_id, 'telegram' ),
						'twitter'         => novin_ai_get_meta( $post_id, 'twitter' ),
						'github'          => novin_ai_get_meta( $post_id, 'github' ),
						'dribbble'        => novin_ai_get_meta( $post_id, 'dribbble' ),
						'website'         => novin_ai_get_meta( $post_id, 'website' ),
					);
				}

				wp_reset_postdata();
			}
		} elseif ( ! empty( $settings['items'] ) ) {
			foreach ( $settings['items'] as $item ) {
				$members[] = array(
					'name'            => $item['name'],
					'role'            => $item['role'],
					'image'           => ! empty( $item['image']['url'] ) ? $item['image']['url'] : '',
					'experience'      => $item['experience'],
					'projects'        => $item['projects'],
					'quote'           => $item['quote'],
					'bio'             => $item['bio'],
					'skills'          => novin_ai_meta_rows( $item['skills'], 2 ),
					'experience_list' => novin_ai_meta_rows( $item['experience_list'], 3 ),
					'education'       => novin_ai_meta_rows( $item['education'], 3 ),
					'certifications'  => novin_ai_meta_rows( $item['certifications'], 2 ),
					'languages'       => novin_ai_meta_rows( $item['languages'], 2 ),
					'location'        => $item['location'],
					'email'           => $item['email'],
					'phone'           => $item['phone'],
					'resume'          => $item['resume'],
					'featured'        => $item['featured'],
					'instagram'       => $item['instagram'],
					'linkedin'        => $item['linkedin'],
					'telegram'        => $item['telegram'],
					'twitter'         => $item['twitter'],
					'github'          => $item['github'],
					'dribbble'        => $item['dribbble'],
					'website'         => $item['website'],
				);
			}
		}
		?>
		<section <?php $this->nv_attr( 'wrapper' ); ?>>
			<?php $this->nv_backdrop( $settings ); ?>

			<div class="nv-container">
				<?php $this->render_heading( $settings ); ?>

				<div class="nv-grid nv-team__grid">
					<?php if ( ! empty( $members ) ) : ?>
						<?php
						foreach ( $members as $index => $member ) {
							$this->render_card( $member, $settings, $index );
						}
						?>
					<?php else : ?>
						<p class="nv-widget__empty"><?php esc_html_e( 'عضوی ثبت نشده است. از منوی «تیم ما» در پیشخوان اعضا را اضافه کنید.', 'novin-ai' ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<?php
			// پنجره همیشه رندر می‌شود: در حالت «هاور» برای دستگاه‌های لمسی
			// (که هاور ندارند) و در حالت «کلیک/هر دو» برای دکمه استفاده می‌شود.
			foreach ( $members as $index => $member ) {
				$this->render_modal( $member, $settings, $index );
			}
			?>
		</section>
		<?php
	}
}
