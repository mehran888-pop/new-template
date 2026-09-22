<?php
/**
 * وارد کردن اطلاعات دمو (یک‌باره).
 *
 * این ماژول یک صفحه در پیشخوان (نمایش » وارد کردن اطلاعات دمو) اضافه می‌کند که
 * با یک کلیک محتوای نمونه را ایجاد می‌کند:
 *
 * - خدمات، پروژه‌ها، اعضای تیم (با رزومه کامل)، پکیج‌ها و نظرات مشتریان
 * - چند نوشته وبلاگی
 * - چهار برگه آماده که با المان‌های اختصاصی المنتور ساخته شده‌اند
 * - منوی اصلی و تنظیم برگه ثابت صفحه اصلی
 *
 * پس از اجرا، دکمه غیرفعال می‌شود و امکان «حذف اطلاعات دمو» هم در اختیار است.
 *
 * @package Novin_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'novin_ai_demo_option_key' ) ) {
	/**
	 * کلید گزینه‌ای که وضعیت import را نگه می‌دارد.
	 *
	 * @return string
	 */
	function novin_ai_demo_option_key() {
		return 'novin_ai_demo_imported';
	}
}

if ( ! function_exists( 'novin_ai_demo_status' ) ) {
	/**
	 * وضعیت فعلی اطلاعات دمو.
	 *
	 * @return array<string, mixed>
	 */
	function novin_ai_demo_status() {
		$status = get_option( novin_ai_demo_option_key(), array() );

		return is_array( $status ) ? $status : array();
	}
}

if ( ! function_exists( 'novin_ai_demo_data' ) ) {
	/**
	 * داده‌های نمونه.
	 *
	 * @return array<string, array<int, array<string, mixed>>>
	 */
	function novin_ai_demo_data() {
		$services = array(
			array(
				'title'    => 'توسعه مدل‌های هوش مصنوعی',
				'content'  => 'طراحی، آموزش و استقرار مدل‌های یادگیری ماشین متناسب با داده‌ها و فرآیندهای سازمان شما؛ از ایده تا اجرا در محیط واقعی.',
				'term'     => 'مشاوره و توسعه',
				'icon'     => 'eicon-cogs',
				'badge'    => 'محبوب‌ترین',
				'price'    => 'از ۴۵,۰۰۰,۰۰۰ تومان',
				'features' => "تحلیل و آماده‌سازی داده‌ها\nآموزش مدل اختصاصی\nاستقرار، مانیتورینگ و بهبود مستمر\nمستندسازی و انتقال دانش به تیم شما",
			),
			array(
				'title'    => 'طراحی و توسعه وب‌سایت',
				'content'  => 'وب‌سایت‌های مدرن، سریع و واکنش‌گرا با رابط کاربری اختصاصی و تجربه کاربری مبتنی بر داده.',
				'term'     => 'مشاوره و توسعه',
				'icon'     => 'eicon-code',
				'badge'    => 'پرتقاضا',
				'price'    => 'از ۱۸,۰۰۰,۰۰۰ تومان',
				'features' => "طراحی رابط و تجربه کاربری\nپیاده‌سازی واکنش‌گرا و سبک\nبهینه‌سازی سرعت و سئو\nاتصال به سامانه‌های سازمانی",
			),
			array(
				'title'    => 'توسعه اپلیکیشن موبایل',
				'content'  => 'اپلیکیشن‌های بومی و چندسکویی برای اندروید و iOS با معماری مقیاس‌پذیر و تجربه‌ای روان.',
				'term'     => 'مشاوره و توسعه',
				'icon'     => 'eicon-bullet-list',
				'price'    => 'از ۳۵,۰۰۰,۰۰۰ تومان',
				'features' => "اندروید و iOS\nمعماری مقیاس‌پذیر\nاعلان‌ها و تحلیل رفتار کاربر\nانتشار در مارکت‌ها و به‌روزرسانی",
			),
			array(
				'title'    => 'فروش نرم‌افزار و لایسنس',
				'content'  => 'فروش آنلاین نرم‌افزار، اشتراک و لایسنس با درگاه پرداخت، صدور خودکار لایسنس و پنل کاربری.',
				'term'     => 'فروش و پشتیبانی',
				'icon'     => 'eicon-star',
				'badge'    => 'فروش ویژه',
				'price'    => 'از ۱۲,۰۰۰,۰۰۰ تومان',
				'features' => "درگاه پرداخت و صدور خودکار لایسنس\nاشتراک ماهانه و سالانه\nپنل کاربری و دانلود اختصاصی\nگزارش فروش و مدیریت مشتریان",
			),
			array(
				'title'    => 'خدمات پشتیبانی و نگهداری',
				'content'  => 'پشتیبانی فنی، مانیتورینگ و نگهداری پیشگیرانه سامانه‌های نرم‌افزاری شما با زمان پاسخگویی مشخص.',
				'term'     => 'فروش و پشتیبانی',
				'icon'     => 'eicon-clock',
				'price'    => 'از ۸,۰۰۰,۰۰۰ تومان',
				'features' => "پاسخگویی در کمتر از یک روز کاری\nمانیتورینگ و هشدار خودکار\nبه‌روزرسانی و رفع خطا\nگزارش ماهانه وضعیت سامانه",
			),
			array(
				'title'    => 'زیرساخت ابری و امنیت',
				'content'  => 'طراحی زیرساخت ابری، پشتیبان‌گیری، مانیتورینگ و ارتقای امنیت سامانه‌های حساس سازمانی.',
				'term'     => 'زیرساخت و امنیت',
				'icon'     => 'eicon-info-circle',
				'price'    => 'از ۲۲,۰۰۰,۰۰۰ تومان',
				'features' => "معماری ابری مقیاس‌پذیر\nپشتیبان‌گیری و بازیابی\nمانیتورینگ و هشدار\nممیزی امنیتی دوره‌ای",
			),
		);

		$projects = array(
			array(
				'title'   => 'دستیار هوشمند پشتیبانی',
				'content' => 'دستیاری مبتنی بر مدل زبانی که بیش از ۷۰٪ تیکت‌های پشتیبانی را به صورت خودکار و دقیق پاسخ می‌دهد.',
				'term'    => 'هوش مصنوعی',
				'client'  => 'شرکت داده‌پردازان نوین',
				'date'    => '۱۴۰۳',
				'tech'    => "Python\nLangChain\nپایگاه داده برداری\nReact",
			),
			array(
				'title'   => 'سامانه تحلیل رفتار مشتری',
				'content' => 'پلتفرمی برای پیش‌بینی ریزش مشتری و پیشنهاد محصول که نرخ تبدیل فروشگاه را ۲۳٪ افزایش داد.',
				'term'    => 'هوش مصنوعی',
				'client'  => 'فروشگاه آنلاین رایکا',
				'date'    => '۱۴۰۲',
				'tech'    => "TensorFlow\nFastAPI\nPostgreSQL\nDocker",
			),
			array(
				'title'   => 'پلتفرم فروش لایسنس نرم‌افزار',
				'content' => 'فروشگاه آنلاین لایسنس با صدور خودکار، اشتراک ماهانه و پنل دانلود اختصاصی مشتریان.',
				'term'    => 'وب و فروشگاه',
				'client'  => 'گروه نرم‌افزاری آوا',
				'date'    => '۱۴۰۳',
				'tech'    => "WooCommerce\nPHP\nRedis\nVue",
			),
			array(
				'title'   => 'اپلیکیشن موبایل بانکداری',
				'content' => 'اپلیکیشن امن بانکی با احراز هویت بیومتریک، انتقال وجه و اعلان‌های لحظه‌ای برای بیش از ۵۰۰ هزار کاربر.',
				'term'    => 'موبایل',
				'client'  => 'مؤسسه مالی مهر',
				'date'    => '۱۴۰۱',
				'tech'    => "Flutter\nNode.js\nKubernetes\nPostgreSQL",
			),
			array(
				'title'   => 'درگاه پرداخت هوشمند',
				'content' => 'زیرساخت پرداخت با مدیریت ریسک لحظه‌ای و تشخیص تقلب مبتنی بر یادگیری ماشین.',
				'term'    => 'زیرساخت',
				'client'  => 'پرداخت‌یار پیشرو',
				'date'    => '۱۴۰۲',
				'tech'    => "Go\ngRPC\nKafka\nKubernetes",
			),
			array(
				'title'   => 'داشبورد مدیریت داده سازمانی',
				'content' => 'داشبورد تحلیلی یکپارچه برای پایش شاخص‌های کلیدی هلدینگ با به‌روزرسانی لحظه‌ای.',
				'term'    => 'وب و فروشگاه',
				'client'  => 'هلدینگ صنعتی پارس',
				'date'    => '۱۴۰۳',
				'tech'    => "Next.js\nGraphQL\nClickHouse\nDocker",
			),
		);

		$team = array(
			array(
				'title'     => 'سارا احمدی',
				'content'   => 'بیش از دوازده سال تجربه در طراحی و استقرار سامانه‌های یادگیری ماشین در مقیاس سازمانی. تمرکز اصلی من تبدیل مدل‌های پژوهشی به محصولاتی پایدار، قابل اندازه‌گیری و سودآور برای کسب‌وکار است.',
				'term'      => 'هوش مصنوعی',
				'role'      => 'مدیر هوش مصنوعی',
				'featured'  => true,
				'meta'      => array(
					'experience'      => '۱۲ سال تجربه',
					'projects'        => '۶۰ پروژه موفق',
					'quote'           => 'هوش مصنوعی زمانی ارزشمند است که مسئله‌ای واقعی را حل کند، نه اینکه فقط جذاب به نظر برسد.',
					'skills'          => "یادگیری ماشین|95\nپایتون|92\nپردازش زبان طبیعی|88\nمعماری مدل|85",
					'experience_list' => "۱۴۰۰ - اکنون|مدیر هوش مصنوعی|نوین ای‌آی\n۱۳۹۶ - ۱۴۰۰|دانشمند داده ارشد|داده‌پردازان برتر\n۱۳۹۳ - ۱۳۹۶|مهندس یادگیری ماشین|همراه‌داده",
					'education'       => "دکتری|هوش مصنوعی|دانشگاه صنعتی شریف\nکارشناسی ارشد|مهندسی کامپیوتر|دانشگاه تهران",
					'certifications'  => "AWS Machine Learning Specialty|۱۴۰۲\nTensorFlow Developer|۱۴۰۰\nDeep Learning Specialization|۱۳۹۸",
					'languages'       => "فارسی|زبان مادری\nانگلیسی|تسلط کامل\nآلمانی|متوسط",
					'location'        => 'نورنبرگ، آلمان',
					'email'           => 'sara@example.com',
					'phone'           => '+49 911 123 4567',
					'linkedin'        => 'https://www.linkedin.com/',
					'github'          => 'https://github.com/',
					'featured'        => '1',
				),
			),
			array(
				'title'     => 'علی رضایی',
				'content'   => 'طراحی سامانه‌های توزیع‌شده و میکروسرویس با رویکرد ابری، تخصص اصلی من است. در این سال‌ها زیرساخت چندین پلتفرم پردازش آنلاین با میلیون‌ها درخواست روزانه را طراحی کرده‌ام.',
				'term'      => 'مهندسی نرم‌افزار',
				'role'      => 'معمار نرم‌افزار',
				'featured'  => false,
				'meta'      => array(
					'experience'      => '۹ سال تجربه',
					'projects'        => '۴۲ پروژه موفق',
					'quote'           => 'معماری خوب یعنی تصمیم‌های سخت را طوری بگیری که تغییرشان بعداً ارزان باشد.',
					'skills'          => "معماری میکروسرویس|93\nKubernetes|88\nGo / Node.js|85\nپایگاه داده|82",
					'experience_list' => "۱۳۹۹ - اکنون|معمار نرم‌افزار|نوین ای‌آی\n۱۳۹۵ - ۱۳۹۹|تیم‌لید بک‌اند|فناوری اطلاعات پارسه\n۱۳۹۲ - ۱۳۹۵|برنامه‌نویس ارشد|نرم‌افزار مهر",
					'education'       => "کارشناسی ارشد|مهندسی نرم‌افزار|دانشگاه صنعتی امیرکبیر",
					'certifications'  => "CKA — Kubernetes Administrator|۱۴۰۱\nAWS Solutions Architect|۱۳۹۹",
					'languages'       => "فارسی|زبان مادری\nانگلیسی|تسلط کامل",
					'location'        => 'تهران، ایران',
					'email'           => 'ali@example.com',
					'phone'           => '+98 21 8877 1122',
					'linkedin'        => 'https://www.linkedin.com/',
					'github'          => 'https://github.com/',
				),
			),
			array(
				'title'     => 'نگار محمدی',
				'content'   => 'مسیر محصول را از ایده تا عرضه و رشد هدایت می‌کنم. علاقه‌ام پیدا کردن نقطه تلاقی نیاز واقعی کاربر با توان فنی تیم است.',
				'term'      => 'مدیریت محصول',
				'role'      => 'مدیر محصول',
				'featured'  => false,
				'meta'      => array(
					'experience'      => '۷ سال تجربه',
					'projects'        => '۳۵ پروژه موفق',
					'quote'           => 'محصول موفق از گوش دادن به کاربر ساخته می‌شود، نه از فهرست امکانات.',
					'skills'          => "مدیریت محصول|94\nتحلیل داده|88\nطراحی تجربه کاربر|84\nاسکرام و فرآیند|90",
					'experience_list' => "۱۴۰۰ - اکنون|مدیر محصول|نوین ای‌آی\n۱۳۹۷ - ۱۴۰۰|مالک محصول|دیجیتال‌مارکتینگ رایکا\n۱۳۹۵ - ۱۳۹۷|تحلیلگر کسب‌وکار|گروه نرم‌افزاری آوا",
					'education'       => "کارشناسی ارشد|مدیریت فناوری اطلاعات|دانشگاه تربیت مدرس",
					'certifications'  => "PSPO — Professional Scrum Product Owner|۱۴۰۱\nGoogle UX Design|۱۳۹۹",
					'languages'       => "فارسی|زبان مادری\nانگلیسی|تسلط کامل",
					'location'        => 'تهران، ایران',
					'email'           => 'negar@example.com',
					'phone'           => '+98 21 8877 1133',
					'linkedin'        => 'https://www.linkedin.com/',
					'instagram'       => 'https://www.instagram.com/',
				),
			),
			array(
				'title'     => 'مهدی کریمی',
				'content'   => 'پانزده سال تجربه در طراحی زیرساخت ابری، شبکه و امنیت سامانه‌های حیاتی. هدفم این است که سامانه در بدترین روزها هم در دسترس بماند.',
				'term'      => 'زیرساخت و امنیت',
				'role'      => 'مدیر زیرساخت و امنیت',
				'featured'  => false,
				'meta'      => array(
					'experience'      => '۱۵ سال تجربه',
					'projects'        => '۸۰ پروژه موفق',
					'quote'           => 'امنیت یک ویژگی نیست؛ پایه‌ای است که اگر نباشد، هیچ ویژگی دیگری ارزش ندارد.',
					'skills'          => "DevOps و زیرساخت|96\nامنیت شبکه|92\nلینوکس|94\nمانیتورینگ|88",
					'experience_list' => "۱۳۹۸ - اکنون|مدیر زیرساخت و امنیت|نوین ای‌آی\n۱۳۹۲ - ۱۳۹۸|مهندس ارشد زیرساخت|داده‌گستر پیشرو\n۱۳۸۷ - ۱۳۹۲|مدیر سیستم|فناوری افرا",
					'education'       => "کارشناسی ارشد|امنیت اطلاعات|دانشگاه صنعتی شریف",
					'certifications'  => "CISSP|۱۴۰۰\nAWS DevOps Engineer|۱۴۰۲\nCEH|۱۳۹۷",
					'languages'       => "فارسی|زبان مادری\nانگلیسی|تسلط کامل",
					'location'        => 'تهران، ایران',
					'email'           => 'mahdi@example.com',
					'phone'           => '+98 21 8877 1144',
					'linkedin'        => 'https://www.linkedin.com/',
					'github'          => 'https://github.com/',
				),
			),
		);

		$packages = array(
			array(
				'title'    => 'پکیج پایه',
				'content'  => 'مناسب استارتاپ‌ها و تیم‌های کوچک که می‌خواهند سریع و کم‌هزینه شروع کنند.',
				'term'     => 'ماهانه',
				'price'    => '4,500,000',
				'yearly'   => '45,000,000',
				'features' => "تحلیل نیازها و مشاوره تخصصی\nیک سامانه سبک یا وب‌سایت\nپشتیبانی ایمیلی\nبه‌روزرسانی ماهانه",
			),
			array(
				'title'    => 'پکیج حرفه‌ای',
				'content'  => 'انتخاب اکثر مشتریان ما؛ تیم کامل در کنار شما از ایده تا استقرار و رشد.',
				'term'     => 'ماهانه',
				'price'    => '12,900,000',
				'yearly'   => '129,000,000',
				'badge'    => 'پیشنهاد ویژه',
				'featured' => '1',
				'features' => "معماری و طراحی اختصاصی\nتیم کامل (فنی، محصول، طراحی)\nپشتیبانی تلفنی و تیکتی\nمانیتورینگ و گزارش ماهانه\nدو مرحله بازنگری رایگان",
			),
			array(
				'title'    => 'پکیج سازمانی',
				'content'  => 'مناسب سازمان‌ها و زیرساخت‌های حساس با نیاز به پاسخگویی و امنیت سطح بالا.',
				'term'     => 'سازمانی',
				'price'    => '29,000,000',
				'yearly'   => '290,000,000',
				'features' => "تیم اختصاصی و مدیر پروژه ویژه\nSLA سی دقیقه‌ای\nامنیت و ممیزی مستمر\nاستقرار ابری اختصاصی\nپشتیبانی ۲۴ ساعته",
			),
		);

		$testimonials = array(
			array(
				'title'   => 'مریم حسینی',
				'content' => 'تیم نوین در کمتر از سه ماه مدل توصیه‌گر ما را بازطراحی کرد؛ دقت پیش‌بینی ۳۴٪ بهتر شد و هزینه زیرساخت هم کاهش پیدا کرد.',
				'role'    => 'مدیر فناوری، داده‌پردازان نوین',
				'rating'  => '5',
				'project' => 'دستیار هوشمند پشتیبانی',
			),
			array(
				'title'   => 'کیانوش راد',
				'content' => 'وب‌سایت جدید ما در دو ماه آماده شد و نرخ تبدیل فروشگاه نسبت به قبل نزدیک به دو برابر شده است. پشتیبانی‌شان واقعاً در دسترس است.',
				'role'    => 'مدیرعامل، فروشگاه آنلاین رایکا',
				'rating'  => '5',
				'project' => 'سامانه تحلیل رفتار مشتری',
			),
			array(
				'title'   => 'الهام نوری',
				'content' => 'از جلسه کشف نیازها تا استقرار، همه چیز شفاف و مرحله‌به‌مرحله بود. مستندسازی دقیق‌شان کار تیم داخلی ما را بسیار ساده‌تر کرد.',
				'role'    => 'مدیر محصول، گروه نرم‌افزاری آوا',
				'rating'  => '5',
				'project' => 'پلتفرم فروش لایسنس',
			),
		);

		$posts = array(
			array(
				'title'   => 'هوش مصنوعی در خدمات مشتریان: از چت‌بات تا دستیار هوشمند',
				'content' => "چت‌بات‌های سنتی فقط پاسخ‌های از پیش تعریف‌شده داشتند؛ اما مدل‌های زبانی امروزی می‌توانند مکالمه را بفهمند، از دانش سازمانی شما استفاده کنند و پاسخ‌های دقیق و شخصی‌سازی‌شده بدهند.\n\nدر این مقاله بررسی می‌کنیم که چطور می‌توان با ترکیب مدل زبانی و پایگاه دانش داخلی، یک دستیار هوشمند ساخت که بیش از نیمی از تیکت‌های پشتیبانی را بدون دخالت انسان پاسخ دهد و در موارد پیچیده، گفت‌گو را با جزئیات کامل به کارشناس انسانی تحویل بدهد.\n\nنکته مهم این است که موفقیت این پروژه‌ها بیش از انتخاب مدل، به کیفیت داده‌ها، طراحی سناریو و اندازه‌گیری مستمر بستگی دارد.",
				'excerpt' => 'مدل‌های زبانی امروزی خدمات مشتریان را متحول کرده‌اند؛ اما موفقیت آن‌ها بیش از انتخاب مدل، به داده‌ها و طراحی سناریو بستگی دارد.',
				'term'    => 'هوش مصنوعی',
			),
			array(
				'title'   => 'چرا سرعت سایت مستقیماً روی فروش اثر می‌گذارد؟',
				'content' => "هر ثانیه تأخیر در بارگذاری صفحه می‌تواند نرخ تبدیل را به شکل محسوسی کاهش دهد. کاربر منتظر نمی‌ماند و موتورهای جستجو هم سایت‌های کند را جریمه می‌کنند.\n\nدر این مطلب، مهم‌ترین عوامل کندی سایت را مرور می‌کنیم: تصاویر بهینه‌نشده، درخواست‌های زیاد، نبود کش مناسب و کدهای سنگین سمت کاربر.\n\nسپس یک چک‌لیست عملی ارائه می‌دهیم که با اجرای آن می‌توانید زمان بارگذاری را بدون بازنویسی کامل سایت به نصف برسانید.",
				'excerpt' => 'هر ثانیه تأخیر، نرخ تبدیل را کاهش می‌دهد. چک‌لیستی عملی برای دوبرابر سریع‌تر شدن سایت بدون بازنویسی کامل.',
				'term'    => 'توسعه وب',
			),
			array(
				'title'   => 'نقشه راه یادگیری ماشین برای تیم‌های نرم‌افزاری',
				'content' => "بسیاری از تیم‌های نرم‌افزاری می‌خواهند هوش مصنوعی را به محصول خود اضافه کنند، اما نمی‌دانند از کجا شروع کنند.\n\nدر این مقاله یک مسیر چهارمرحله‌ای پیشنهاد می‌دهیم: تعریف دقیق مسئله و معیار موفقیت، جمع‌آوری و آماده‌سازی داده، ساخت نمونه اولیه کوچک، و در نهایت استقرار و پایش مدل در محیط واقعی.\n\nهمچنین توضیح می‌دهیم چه زمانی استفاده از مدل آماده بهتر از آموزش مدل اختصاصی است و چه زمانی باید روی زیرساخت داده سرمایه‌گذاری کرد.",
				'excerpt' => 'یک مسیر چهارمرحله‌ای عملی برای افزودن هوش مصنوعی به محصول، از تعریف مسئله تا استقرار و پایش.',
				'term'    => 'مهندسی نرم‌افزار',
			),
		);

		return array(
			'services'     => $services,
			'projects'     => $projects,
			'team'         => $team,
			'packages'     => $packages,
			'testimonials' => $testimonials,
			'posts'        => $posts,
		);
	}
}

if ( ! function_exists( 'novin_ai_demo_pages' ) ) {
	/**
	 * برگه‌هایی که با المنتور ساخته می‌شوند.
	 *
	 * هر برگه فهرستی از المان‌های اختصاصی (widgetType + تنظیمات) دارد.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function novin_ai_demo_pages() {
		return array(
			array(
				'title'    => 'صفحه اصلی',
				'slug'     => 'home',
				'is_front' => true,
				'widgets'  => array(
					array( 'novin-hero' ),
					array( 'novin-services' ),
					array( 'novin-projects' ),
					array( 'novin-stats' ),
					array( 'novin-team' ),
					array( 'novin-packages', array( 'source' => 'cpt' ) ),
					array( 'novin-testimonials', array( 'source' => 'cpt' ) ),
					array( 'novin-posts' ),
					array( 'novin-faq' ),
					array( 'novin-brands' ),
					array( 'novin-cta' ),
				),
			),
			array(
				'title'   => 'خدمات',
				'slug'    => 'services-page',
				'widgets' => array(
					array( 'novin-services' ),
					array( 'novin-packages', array( 'source' => 'cpt' ) ),
					array( 'novin-faq' ),
					array( 'novin-cta' ),
				),
			),
			array(
				'title'   => 'درباره ما',
				'slug'    => 'about-us',
				'widgets' => array(
					array( 'novin-stats' ),
					array( 'novin-team' ),
					array( 'novin-testimonials', array( 'source' => 'cpt' ) ),
					array( 'novin-brands' ),
				),
			),
			array(
				'title'   => 'نمونه‌کارها',
				'slug'    => 'portfolio',
				'widgets' => array(
					array( 'novin-projects' ),
					array( 'novin-cta' ),
				),
			),
			array(
				'title'   => 'تماس با ما',
				'slug'    => 'contact-us',
				'widgets' => array(
					array( 'novin-cta' ),
					array( 'novin-faq' ),
				),
			),
		);
	}
}

/* --------------------------------------------------------------------
 * ابزارها
 * ------------------------------------------------------------------ */

if ( ! function_exists( 'novin_ai_demo_element_id' ) ) {
	/**
	 * شناسه یکتا برای المان‌های المنتور.
	 *
	 * @return string
	 */
	function novin_ai_demo_element_id() {
		return substr( md5( uniqid( 'nv', true ) ), 0, 7 );
	}
}

if ( ! function_exists( 'novin_ai_demo_elementor_data' ) ) {
	/**
	 * ساخت داده المنتور از فهرست المان‌ها.
	 *
	 * @param array<int, array<int, mixed>> $widgets المان‌ها.
	 * @param array<string, mixed>          $padding فاصله داخلی سکشن.
	 * @return string
	 */
	function novin_ai_demo_elementor_data( $widgets, $padding = array() ) {
		$sections = array();

		foreach ( $widgets as $widget ) {
			$type     = is_array( $widget ) ? $widget[0] : $widget;
			$settings = ( is_array( $widget ) && isset( $widget[1] ) ) ? $widget[1] : array();

			$sections[] = array(
				'id'       => novin_ai_demo_element_id(),
				'elType'   => 'section',
				'settings' => array(
					'layout'  => 'full_width',
					'gap'     => 'no',
					'padding' => $padding,
				),
				'elements' => array(
					array(
						'id'       => novin_ai_demo_element_id(),
						'elType'   => 'column',
						'settings' => array(
							'_column_size' => 100,
							'_inline_size' => 100,
						),
						'elements' => array(
							array(
								'id'         => novin_ai_demo_element_id(),
								'elType'     => 'widget',
								'widgetType' => $type,
								'settings'   => $settings,
								'elements'   => array(),
							),
						),
						'isInner'    => false,
					),
				),
				'isInner'  => false,
			);
		}

		return wp_slash( wp_json_encode( $sections ) );
	}
}

if ( ! function_exists( 'novin_ai_demo_make_image' ) ) {
	/**
	 * ساخت تصویر نمونه با کتابخانه GD.
	 *
	 * @param string $title عنوان (برای نام فایل).
	 * @param int    $width عرض.
	 * @param int    $height ارتفاع.
	 * @param array<int, int> $from رنگ شروع.
	 * @param array<int, int> $to   رنگ پایان.
	 * @return int شناسه پیوست یا ۰ در صورت نبود GD.
	 */
	function novin_ai_demo_make_image( $title, $width, $height, $from, $to ) {
		if ( ! function_exists( 'imagecreatetruecolor' ) || ! function_exists( 'imagepng' ) ) {
			return 0;
		}

		$sw = 120;
		$sh = max( 1, (int) round( 120 * ( $height / max( 1, $width ) ) ) );

		$small = imagecreatetruecolor( $sw, $sh );

		if ( ! $small ) {
			return 0;
		}

		for ( $x = 0; $x < $sw; $x++ ) {
			for ( $y = 0; $y < $sh; $y++ ) {
				$ratio = ( $x + $y ) / max( 1, ( $sw + $sh - 2 ) );
				$r     = (int) round( $from[0] + ( $to[0] - $from[0] ) * $ratio );
				$g     = (int) round( $from[1] + ( $to[1] - $from[1] ) * $ratio );
				$b     = (int) round( $from[2] + ( $to[2] - $from[2] ) * $ratio );

				imagesetpixel( $small, $x, $y, imagecolorallocate( $small, $r, $g, $b ) );
			}
		}

		$image = imagecreatetruecolor( $width, $height );

		if ( ! $image ) {
			imagedestroy( $small );
			return 0;
		}

		if ( function_exists( 'imagescale' ) ) {
			$scaled = imagescale( $small, $width, $height, IMG_BILINEAR_FIXED );

			if ( $scaled ) {
				imagecopy( $image, $scaled, 0, 0, 0, 0, $width, $height );
				imagedestroy( $scaled );
			}
		} else {
			imagecopyresampled( $image, $small, 0, 0, 0, 0, $width, $height, $sw, $sh );
		}

		imagedestroy( $small );

		// دایره‌های نوری برای جلوه بهتر.
		$glow_a = imagecolorallocatealpha( $image, 255, 255, 255, 108 );
		$glow_b = imagecolorallocatealpha( $image, 255, 255, 255, 118 );
		imagefilledellipse( $image, (int) ( $width * 0.24 ), (int) ( $height * 0.28 ), (int) ( $width * 0.42 ), (int) ( $width * 0.42 ), $glow_a );
		imagefilledellipse( $image, (int) ( $width * 0.78 ), (int) ( $height * 0.74 ), (int) ( $width * 0.52 ), (int) ( $width * 0.52 ), $glow_b );

		$upload = wp_upload_dir();

		if ( ! empty( $upload['error'] ) ) {
			imagedestroy( $image );
			return 0;
		}

		// نام فایل فقط از کاراکترهای امن ساخته می‌شود (عنوان ممکن است فارسی باشد).
		$name = 'novin-ai-demo-' . substr( md5( $title . '|' . $width . 'x' . $height ), 0, 10 ) . '-' . (int) $width . 'x' . (int) $height . '.png';
		$file = trailingslashit( $upload['path'] ) . $name;

		if ( ! imagepng( $image, $file ) ) {
			imagedestroy( $image );
			return 0;
		}

		imagedestroy( $image );

		$url = trailingslashit( $upload['url'] ) . $name;

		$attachment_id = wp_insert_attachment(
			array(
				'guid'           => $url,
				'post_mime_type' => 'image/png',
				'post_title'     => $title,
				'post_status'    => 'inherit',
			),
			$file
		);

		if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
			return 0;
		}

		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		$meta = wp_generate_attachment_metadata( $attachment_id, $file );
		wp_update_attachment_metadata( $attachment_id, $meta );

		return (int) $attachment_id;
	}
}

if ( ! function_exists( 'novin_ai_demo_palettes' ) ) {
	/**
	 * پالت‌های رنگی تصاویر نمونه.
	 *
	 * @return array<int, array<int, array<int, int>>>
	 */
	function novin_ai_demo_palettes() {
		return array(
			array( array( 109, 94, 252 ), array( 34, 211, 238 ) ),
			array( array( 34, 211, 238 ), array( 139, 124, 255 ) ),
			array( array( 255, 78, 205 ), array( 109, 94, 252 ) ),
			array( array( 22, 214, 178 ), array( 34, 211, 238 ) ),
			array( array( 139, 124, 255 ), array( 255, 78, 205 ) ),
			array( array( 12, 16, 34 ), array( 109, 94, 252 ) ),
		);
	}
}

/* --------------------------------------------------------------------
 * اجرای import
 * ------------------------------------------------------------------ */

if ( ! function_exists( 'novin_ai_import_demo_content' ) ) {
	/**
	 * ایجاد محتوای نمونه.
	 *
	 * @return array<string, mixed>
	 */
	function novin_ai_import_demo_content() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return array( 'error' => 'forbidden' );
		}

		if ( ! empty( novin_ai_demo_status() ) ) {
			return array( 'error' => 'already' );
		}

		if ( ! function_exists( 'wp_insert_post' ) ) {
			require_once ABSPATH . 'wp-includes/post.php';
		}

		$data        = novin_ai_demo_data();
		$palettes    = novin_ai_demo_palettes();
		$zero        = array(
			'unit'     => 'px',
			'top'      => '0',
			'right'    => '0',
			'bottom'   => '0',
			'left'     => '0',
			'isLinked' => true,
		);

		$post_ids    = array();
		$attachments = array();
		$terms       = array();

		/* --- خدمات --- */
		foreach ( $data['services'] as $service ) {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'novin_service',
					'post_title'   => $service['title'],
					'post_content' => $service['content'],
					'post_status'  => 'publish',
					'menu_order'   => count( $post_ids ),
				),
				true
			);

			if ( is_wp_error( $post_id ) ) {
				continue;
			}

			update_post_meta( $post_id, '_novin_icon', isset( $service['icon'] ) ? $service['icon'] : '' );
			update_post_meta( $post_id, '_novin_badge', isset( $service['badge'] ) ? $service['badge'] : '' );
			update_post_meta( $post_id, '_novin_price', isset( $service['price'] ) ? $service['price'] : '' );
			update_post_meta( $post_id, '_novin_features', isset( $service['features'] ) ? $service['features'] : '' );

			if ( ! empty( $service['term'] ) ) {
				$term_id = novin_ai_demo_term( 'novin_service_cat', $service['term'], $terms );

				if ( $term_id ) {
					wp_set_object_terms( $post_id, array( $term_id ), 'novin_service_cat' );
				}
			}

			$post_ids[] = $post_id;
		}

		/* --- پروژه‌ها --- */
		foreach ( $data['projects'] as $index => $project ) {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'novin_project',
					'post_title'   => $project['title'],
					'post_content' => $project['content'],
					'post_status'  => 'publish',
					'menu_order'   => $index,
				),
				true
			);

			if ( is_wp_error( $post_id ) ) {
				continue;
			}

			update_post_meta( $post_id, '_novin_client', $project['client'] );
			update_post_meta( $post_id, '_novin_date', $project['date'] );
			update_post_meta( $post_id, '_novin_tech', $project['tech'] );

			$palette  = $palettes[ $index % count( $palettes ) ];
			$image_id = novin_ai_demo_make_image( $project['title'], 1200, 800, $palette[0], $palette[1] );

			if ( $image_id ) {
				set_post_thumbnail( $post_id, $image_id );
				$attachments[] = $image_id;
			}

			$term_id = novin_ai_demo_term( 'novin_project_cat', $project['term'], $terms );

			if ( $term_id ) {
				wp_set_object_terms( $post_id, array( $term_id ), 'novin_project_cat' );
			}

			$post_ids[] = $post_id;
		}

		/* --- تیم --- */
		foreach ( $data['team'] as $index => $member ) {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'novin_team',
					'post_title'   => $member['title'],
					'post_content' => $member['content'],
					'post_status'  => 'publish',
					'menu_order'   => $index,
				),
				true
			);

			if ( is_wp_error( $post_id ) ) {
				continue;
			}

			foreach ( $member['meta'] as $key => $value ) {
				update_post_meta( $post_id, '_novin_' . $key, $value );
			}

			$palette  = $palettes[ ( $index + 2 ) % count( $palettes ) ];
			$image_id = novin_ai_demo_make_image( $member['title'], 900, 1200, $palette[0], $palette[1] );

			if ( $image_id ) {
				set_post_thumbnail( $post_id, $image_id );
				$attachments[] = $image_id;
			}

			$term_id = novin_ai_demo_term( 'novin_team_group', $member['term'], $terms );

			if ( $term_id ) {
				wp_set_object_terms( $post_id, array( $term_id ), 'novin_team_group' );
			}

			$post_ids[] = $post_id;
		}

		/* --- پکیج‌ها --- */
		foreach ( $data['packages'] as $index => $package ) {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'novin_package',
					'post_title'   => $package['title'],
					'post_content' => $package['content'],
					'post_status'  => 'publish',
					'menu_order'   => $index,
				),
				true
			);

			if ( is_wp_error( $post_id ) ) {
				continue;
			}

			update_post_meta( $post_id, '_novin_price', $package['price'] );
			update_post_meta( $post_id, '_novin_price_yearly', $package['yearly'] );
			update_post_meta( $post_id, '_novin_currency', 'تومان' );
			update_post_meta( $post_id, '_novin_period', '/ ماهانه' );
			update_post_meta( $post_id, '_novin_features', $package['features'] );
			update_post_meta( $post_id, '_novin_badge', isset( $package['badge'] ) ? $package['badge'] : '' );
			update_post_meta( $post_id, '_novin_button_text', 'سفارش پکیج' );
			update_post_meta( $post_id, '_novin_featured', isset( $package['featured'] ) ? $package['featured'] : '' );

			$term_id = novin_ai_demo_term( 'novin_package_cat', $package['term'], $terms );

			if ( $term_id ) {
				wp_set_object_terms( $post_id, array( $term_id ), 'novin_package_cat' );
			}

			$post_ids[] = $post_id;
		}

		/* --- نظرات مشتریان --- */
		foreach ( $data['testimonials'] as $index => $testimonial ) {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'novin_testimonial',
					'post_title'   => $testimonial['title'],
					'post_content' => $testimonial['content'],
					'post_status'  => 'publish',
					'menu_order'   => $index,
				),
				true
			);

			if ( is_wp_error( $post_id ) ) {
				continue;
			}

			update_post_meta( $post_id, '_novin_role', $testimonial['role'] );
			update_post_meta( $post_id, '_novin_rating', $testimonial['rating'] );
			update_post_meta( $post_id, '_novin_project', $testimonial['project'] );

			$post_ids[] = $post_id;
		}

		/* --- نوشته‌ها --- */
		foreach ( $data['posts'] as $post_index => $post_item ) {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'post',
					'post_title'   => $post_item['title'],
					'post_content' => $post_item['content'],
					'post_excerpt' => $post_item['excerpt'],
					'post_status'  => 'publish',
				),
				true
			);

			if ( is_wp_error( $post_id ) ) {
				continue;
			}

			$term_id = novin_ai_demo_term( 'category', $post_item['term'], $terms );

			if ( $term_id ) {
				wp_set_object_terms( $post_id, array( $term_id ), 'category' );
			}

			$palette  = $palettes[ ( $post_index + 4 ) % count( $palettes ) ];
			$image_id = novin_ai_demo_make_image( $post_item['title'], 1200, 675, $palette[0], $palette[1] );

			if ( $image_id ) {
				set_post_thumbnail( $post_id, $image_id );
				$attachments[] = $image_id;
			}

			$post_ids[] = $post_id;
		}

		/* --- برگه‌ها با المنتور --- */
		$pages      = array();
		$front_id   = 0;
		$elementor  = defined( 'ELEMENTOR_VERSION' ) && ELEMENTOR_VERSION;

		foreach ( novin_ai_demo_pages() as $page ) {
			$page_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_title'   => $page['title'],
					'post_name'    => $page['slug'],
					'post_status'  => 'publish',
					'post_content' => '',
				),
				true
			);

			if ( is_wp_error( $page_id ) ) {
				continue;
			}

			$pages[ $page['slug'] ] = $page_id;
			$post_ids[]             = $page_id;

			if ( $elementor ) {
				update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
				update_post_meta( $page_id, '_elementor_data', novin_ai_demo_elementor_data( $page['widgets'], $zero ) );
				update_post_meta( $page_id, '_elementor_version', ELEMENTOR_VERSION );
				update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
				update_post_meta( $page_id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
				update_post_meta( $page_id, '_wp_page_template', 'page-templates/template-fullwidth.php' );
			} else {
				update_post_meta( $page_id, '_wp_page_template', 'page-templates/template-fullwidth.php' );

				// فالبک ساده برای زمانی که المنتور فعال نیست.
				wp_update_post(
					array(
						'ID'           => $page_id,
						'post_content' => '<!-- wp:paragraph --><p>' . esc_html__( 'این برگه برای نمایش کامل به افزونه المنتور نیاز دارد. پس از نصب و فعال‌سازی المنتور، برگه را با المان‌های اختصاصی Novin AI بسازید.', 'novin-ai' ) . '</p><!-- /wp:paragraph -->',
					)
				);
			}

			if ( ! empty( $page['is_front'] ) ) {
				$front_id = $page_id;
			}
		}

		/* --- منو --- */
		$menu_id = 0;

		if ( function_exists( 'wp_create_nav_menu' ) ) {
			$menu_name = 'منوی اصلی — Novin AI';
			$menu_id   = wp_create_nav_menu( $menu_name );

			if ( ! is_wp_error( $menu_id ) ) {
				$items = array(
					array( 'title' => 'صفحه اصلی', 'type' => 'page', 'object' => 'home' ),
					array( 'title' => 'خدمات', 'type' => 'page', 'object' => 'services-page' ),
					array( 'title' => 'نمونه‌کارها', 'type' => 'page', 'object' => 'portfolio' ),
					array( 'title' => 'درباره ما', 'type' => 'page', 'object' => 'about-us' ),
					array( 'title' => 'تماس با ما', 'type' => 'page', 'object' => 'contact-us' ),
				);

				foreach ( $items as $item ) {
					if ( empty( $pages[ $item['object'] ] ) ) {
						continue;
					}

					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-title'     => $item['title'],
							'menu-item-object'    => 'page',
							'menu-item-object-id' => $pages[ $item['object'] ],
							'menu-item-type'      => 'post_type',
							'menu-item-status'    => 'publish',
						)
					);
				}

				$locations = get_theme_mod( 'nav_menu_locations' );
				$locations = is_array( $locations ) ? $locations : array();

				$locations['primary'] = $menu_id;
				$locations['mobile']  = $menu_id;

				set_theme_mod( 'nav_menu_locations', $locations );
			} else {
				$menu_id = 0;
			}
		}

		/* --- برگه ثابت صفحه اصلی --- */
		$prev_front = (int) get_option( 'page_on_front' );
		$prev_show  = get_option( 'show_on_front' );

		if ( $front_id ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $front_id );
		}

		$status = array(
			'time'        => time(),
			'posts'       => $post_ids,
			'attachments' => $attachments,
			'terms'       => $terms,
			'menu'        => $menu_id,
			'pages'       => $pages,
			'front'       => $front_id,
			'restore'     => array(
				'show_on_front' => $prev_show,
				'page_on_front' => $prev_front,
			),
			'elementor'   => $elementor ? 1 : 0,
		);

		update_option( novin_ai_demo_option_key(), $status, false );

		flush_rewrite_rules();

		return $status;
	}
}

if ( ! function_exists( 'novin_ai_demo_term' ) ) {
	/**
	 * ایجاد یا دریافت یک ترم.
	 *
	 * @param string               $taxonomy طبقه‌بندی.
	 * @param string               $name     نام ترم.
	 * @param array<int, int>      $terms    فهرست ترم‌های ایجادشده (ارجاعی).
	 * @return int
	 */
	function novin_ai_demo_term( $taxonomy, $name, &$terms ) {
		$term = get_term_by( 'name', $name, $taxonomy );

		if ( $term && ! is_wp_error( $term ) ) {
			return (int) $term->term_id;
		}

		$new = wp_insert_term( $name, $taxonomy );

		if ( is_wp_error( $new ) || empty( $new['term_id'] ) ) {
			return 0;
		}

		$terms[] = (int) $new['term_id'];

		return (int) $new['term_id'];
	}
}

if ( ! function_exists( 'novin_ai_delete_demo_content' ) ) {
	/**
	 * حذف محتوای دمو.
	 *
	 * @return bool
	 */
	function novin_ai_delete_demo_content() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$status = novin_ai_demo_status();

		if ( empty( $status ) ) {
			return false;
		}

		if ( ! empty( $status['posts'] ) ) {
			foreach ( $status['posts'] as $post_id ) {
				wp_delete_post( (int) $post_id, true );
			}
		}

		if ( ! empty( $status['attachments'] ) ) {
			foreach ( $status['attachments'] as $attachment_id ) {
				wp_delete_attachment( (int) $attachment_id, true );
			}
		}

		if ( ! empty( $status['terms'] ) ) {
			foreach ( $status['terms'] as $term_id ) {
				$term = get_term( (int) $term_id );

				if ( $term && ! is_wp_error( $term ) ) {
					wp_delete_term( (int) $term_id, $term->taxonomy );
				}
			}
		}

		if ( ! empty( $status['menu'] ) ) {
			wp_delete_nav_menu( (int) $status['menu'] );
		}

		if ( ! empty( $status['restore'] ) ) {
			update_option( 'show_on_front', $status['restore']['show_on_front'] );
			update_option( 'page_on_front', (int) $status['restore']['page_on_front'] );
		}

		delete_option( novin_ai_demo_option_key() );

		flush_rewrite_rules();

		return true;
	}
}

/* --------------------------------------------------------------------
 * صفحه مدیریت و پردازش فرم
 * ------------------------------------------------------------------ */

if ( ! function_exists( 'novin_ai_demo_import_menu' ) ) {
	/**
	 * افزودن صفحه وارد کردن اطلاعات دمو.
	 *
	 * @return void
	 */
	function novin_ai_demo_import_menu() {
		add_theme_page(
			esc_html__( 'وارد کردن اطلاعات دمو', 'novin-ai' ),
			esc_html__( 'وارد کردن اطلاعات دمو', 'novin-ai' ),
			'manage_options',
			'novin-ai-demo-import',
			'novin_ai_demo_import_render'
		);
	}
}
add_action( 'admin_menu', 'novin_ai_demo_import_menu' );

if ( ! function_exists( 'novin_ai_demo_import_render' ) ) {
	/**
	 * نمایش صفحه.
	 *
	 * @return void
	 */
	function novin_ai_demo_import_render() {
		$status   = novin_ai_demo_status();
		$imported = ! empty( $status );
		$element  = defined( 'ELEMENTOR_VERSION' ) && ELEMENTOR_VERSION;
		$edit_url = '';

		if ( $imported && ! empty( $status['front'] ) ) {
			$edit_url = $element
				? admin_url( 'post.php?post=' . (int) $status['front'] . '&action=elementor' )
				: admin_url( 'post.php?post=' . (int) $status['front'] . '&action=edit' );
		}
		?>
		<div class="wrap novin-ai-demo">
			<h1><?php esc_html_e( 'وارد کردن اطلاعات دمو — Novin AI', 'novin-ai' ); ?></h1>

			<p class="description">
				<?php esc_html_e( 'با یک کلیک، محتوای نمونه (خدمات، پروژه‌ها، تیم با رزومه کامل، پکیج‌ها، نظرات و مقالات) به همراه پنج برگه آماده که با المان‌های اختصاصی المنتور ساخته شده‌اند ایجاد می‌شود. پس از آن می‌توانید هر بخش را با المنتور ویرایش کنید.', 'novin-ai' ); ?>
			</p>

			<?php if ( isset( $_GET['novin-demo'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<?php if ( 'imported' === $_GET['novin-demo'] ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
					<div class="notice notice-success"><p><?php esc_html_e( 'اطلاعات دمو با موفقیت وارد شد. حالا می‌توانید صفحه اصلی را با المنتور ویرایش کنید.', 'novin-ai' ); ?></p></div>
				<?php elseif ( 'deleted' === $_GET['novin-demo'] ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
					<div class="notice notice-success"><p><?php esc_html_e( 'اطلاعات دمو حذف شد.', 'novin-ai' ); ?></p></div>
				<?php elseif ( 'error' === $_GET['novin-demo'] ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
					<div class="notice notice-error"><p><?php esc_html_e( 'عملیات انجام نشد. لطفاً دوباره تلاش کنید.', 'novin-ai' ); ?></p></div>
				<?php endif; ?>
			<?php endif; ?>

			<div class="novin-ai-card">
				<h2><?php esc_html_e( 'محتوای ایجاد شده', 'novin-ai' ); ?></h2>
				<table class="widefat striped">
					<tbody>
						<tr><td><strong><?php esc_html_e( 'برگه‌ها', 'novin-ai' ); ?></strong></td><td><?php esc_html_e( 'صفحه اصلی، خدمات، نمونه‌کارها، درباره ما، تماس با ما', 'novin-ai' ); ?></td></tr>
						<tr><td><strong><?php esc_html_e( 'المان‌های المنتور', 'novin-ai' ); ?></strong></td><td><?php esc_html_e( 'هیرو، خدمات، پروژه‌ها، آمار، تیم، پکیج‌ها، نظرات، مقالات، سوالات متداول، برندها و فراخوان', 'novin-ai' ); ?></td></tr>
						<tr><td><strong><?php esc_html_e( 'خدمات', 'novin-ai' ); ?></strong></td><td><?php echo esc_html( count( novin_ai_demo_data()['services'] ) . ' مورد' ); ?></td></tr>
						<tr><td><strong><?php esc_html_e( 'پروژه‌ها', 'novin-ai' ); ?></strong></td><td><?php echo esc_html( count( novin_ai_demo_data()['projects'] ) . ' مورد' ); ?></td></tr>
						<tr><td><strong><?php esc_html_e( 'اعضای تیم', 'novin-ai' ); ?></strong></td><td><?php echo esc_html( count( novin_ai_demo_data()['team'] ) . ' نفر با رزومه کامل' ); ?></td></tr>
						<tr><td><strong><?php esc_html_e( 'پکیج‌ها و نظرات', 'novin-ai' ); ?></strong></td><td><?php echo esc_html( count( novin_ai_demo_data()['packages'] ) . ' پکیج و ' . count( novin_ai_demo_data()['testimonials'] ) . ' نظر' ); ?></td></tr>
						<tr><td><strong><?php esc_html_e( 'مقالات', 'novin-ai' ); ?></strong></td><td><?php echo esc_html( count( novin_ai_demo_data()['posts'] ) . ' نوشته' ); ?></td></tr>
						<tr><td><strong><?php esc_html_e( 'منو', 'novin-ai' ); ?></strong></td><td><?php esc_html_e( 'منوی اصلی و موبایل به صورت خودکار تنظیم می‌شود', 'novin-ai' ); ?></td></tr>
					</tbody>
				</table>
			</div>

			<div class="novin-ai-card">
				<h2><?php esc_html_e( 'اجرا', 'novin-ai' ); ?></h2>

				<?php if ( ! $element ) : ?>
					<div class="notice notice-warning inline"><p><?php esc_html_e( 'المنتور فعال نیست. محتوا ایجاد می‌شود اما برگه‌ها با المان‌های اختصاصی ساخته نمی‌شوند؛ بهتر است ابتدا المنتور را نصب و فعال کنید.', 'novin-ai' ); ?></p></div>
				<?php endif; ?>

				<?php if ( $imported ) : ?>
					<p><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'اطلاعات دمو قبلاً وارد شده است (فقط یک بار).', 'novin-ai' ); ?></p>
					<p class="description"><?php esc_html_e( 'اگر بخشی از محتوا را به صورت دستی حذف کرده‌اید، ابتدا «حذف اطلاعات دمو» را بزنید و سپس دوباره وارد کنید.', 'novin-ai' ); ?></p>

					<p>
						<a class="button button-primary button-hero" href="<?php echo esc_url( $edit_url ); ?>">
							<?php esc_html_e( 'ویرایش صفحه اصلی با المنتور', 'novin-ai' ); ?>
						</a>
					</p>

					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( esc_html__( 'اطلاعات دمو حذف شود؟', 'novin-ai' ) ); ?>');">
						<input type="hidden" name="action" value="novin_ai_delete_demo">
						<?php wp_nonce_field( 'novin_ai_delete_demo', 'novin_ai_demo_nonce' ); ?>
						<?php submit_button( esc_html__( 'حذف اطلاعات دمو', 'novin-ai' ), 'delete', 'submit', false ); ?>
					</form>
				<?php else : ?>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="novin_ai_import_demo">
						<?php wp_nonce_field( 'novin_ai_import_demo', 'novin_ai_demo_nonce' ); ?>
						<?php submit_button( esc_html__( 'وارد کردن اطلاعات دمو (یک بار)', 'novin-ai' ), 'primary', 'submit', false ); ?>
					</form>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'novin_ai_handle_demo_import' ) ) {
	/**
	 * پردازش درخواست import.
	 *
	 * @return void
	 */
	function novin_ai_handle_demo_import() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز.', 'novin-ai' ) );
		}

		check_admin_referer( 'novin_ai_import_demo', 'novin_ai_demo_nonce' );

		$result = novin_ai_import_demo_content();
		$state  = ( is_array( $result ) && empty( $result['error'] ) ) ? 'imported' : 'error';

		wp_safe_redirect( admin_url( 'themes.php?page=novin-ai-demo-import&novin-demo=' . $state ) );
		exit;
	}
}
add_action( 'admin_post_novin_ai_import_demo', 'novin_ai_handle_demo_import' );

if ( ! function_exists( 'novin_ai_handle_demo_delete' ) ) {
	/**
	 * پردازش درخواست حذف.
	 *
	 * @return void
	 */
	function novin_ai_handle_demo_delete() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز.', 'novin-ai' ) );
		}

		check_admin_referer( 'novin_ai_delete_demo', 'novin_ai_demo_nonce' );

		$done = novin_ai_delete_demo_content();

		wp_safe_redirect( admin_url( 'themes.php?page=novin-ai-demo-import&novin-demo=' . ( $done ? 'deleted' : 'error' ) ) );
		exit;
	}
}
add_action( 'admin_post_novin_ai_delete_demo', 'novin_ai_handle_demo_delete' );
