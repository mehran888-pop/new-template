# راهنمای ماژول‌ها

## قالب `neomorph`

### پنل تنظیمات (`inc/options/`)
- ثبت با Settings API، یک آرایه `neomorph_options` با `defaults()` مرکزی.
- sanitizer اختصاصی برای رنگ/ایمیل/اعداد/صفحات.
- تکرارکننده شبکه‌های اجتماعی در تب تماس.
- آپلود فونت (`class-custom-fonts.php`): `wp_handle_upload` برای woff2/woff/ttf، تولید `@font-face` این‌لاین.

### CSS پویا (`class-dynamic-css.php`)
همه تنظیمات به متغیرهای CSS (`--neo-shadow`, `--neo-radius`, `--neo-accent`…) تبدیل می‌شوند؛ یعنی کل سیستم نئومورفیسم با ۴ لغزنده (گردی، فاصله سایه، نرمی، رنگ) کنترل می‌شود.

### ابزارک‌های المنتور (`inc/elementor/`)
- `Neo_Widget_Base` کنترل‌های مشترک را فراهم می‌کند (`add_neo_style_controls`, `neo_widget_class`).
- چهار حالت سطح: `neo-surface` (برآمده)، `neo-inset` (فرورفته)، `neo-flat`، `neo-pill`.
- هدر/فوتر ابزارک‌محور، سازگار با Theme Builder پرو.

### دمو (`inc/demo/`)
JSON های استاندارد المنتور (`elType: section/column/widget`) در `inc/demo/data/`. درون‌ریز:
صفحه می‌سازد → `_elementor_data` را ست می‌کند → تمپلیت را مپ می‌کند → صفحه اصلی/بلاگ را ست می‌کند → منوها را می‌سازد.

---

## افزونه `neomorph-core`

### Auth (OTP)
- کد ۵ رقمی `wp_rand`، ذخیره ترنزینت ۵ دقیقه‌ای، کول‌داون ۱۲۰ ثانیه.
- مقایسه `hash_equals` (ضد timing attack).
- نرمال‌سازی شماره ایران (`+98`, `98`, `09`).
- ثبت‌نام خودکار با نقش `nmc_customer` (قابل غیرفعال‌سازی).

### Panel
شورت‌کد `[neomorph_panel]` با ۷ تب:
- **داشبورد**: خوش‌آمد + آمار (امتیاز/سطح/فاکتور/تیکت)
- **خریدها**: `[woocommerce_my_account]`
- **مالی**: جدول فاکتورها + LTV + دکمه پرداخت/مشاهده
- **تیکت**: لیست + ثبت + گفتگوی رشته‌ای (AJAX)
- **باشگاه**: کارت امتیاز/سطح/پیشرفت + جوایز
- **مشاوره**: فرم (نوع/خدمت/توضیح) + تاریخچه
- **پروفایل**: نام/ایمیل/موبایل/چت‌آیدی بله و تلگرام/رمز

### Finance
- `Invoice::create()` — آیتم‌ها، تخفیف، مالیات، شماره سکانسیال (`NMC-1001`)، توکن ۴۰ کاراکتری.
- `Invoice::payment_url()` — لینک عمومی `?nmc-invoice=TOKEN`.
- ارسال فاکتور: پیامک + بله/تلگرام شخصی مشتری (در صورت تنظیم).
- درگاه‌ها: الگوی Strategy (`AbstractGateway`)؛ زرین‌پال v4 (درخواست/تأیید)، آی‌دی‌پی؛ بازگشت `?nmc-gw=…&invoice=…` → تأیید → `mark_paid` → رویداد `invoice_paid`.

### Loyalty
- جدول `wp_nmc_points` (dbDelta) با دفتر کل append-only.
- `add_points` (spend با چک موجودی)، `balance`، `history`.
- سطوح ۴گانه + جوایز (قابل بازنویسی از ادمین یا فیلتر `nmc_loyalty_rewards`).
- خرید: هر ۱۰هزار تومان = N امتیاز (تنظیم‌شدنی).

### Tickets
رشته پیام‌ها در meta؛ نقش `user`/`staff`؛ اعلان به ادمین/مشتری؛ وضعیت open/answered/closed.

### CRM
- مشتریان = کاربران (همگام‌سازی خودکار) + متادیتای سرنخ/خدمات/یادداشت/LTV.
- پرونده (`nmc_case`) با ۶ مرحله pipeline + پیگیری بعدی + خدمات انتخابی.
- تغییر مرحله → رویداد `case_stage_changed` برای اتوماسیون.

### Automation
- قوانین در `neomorph_core_automation_rules`: `{event, conditions, actions[], enabled}`.
- شرط: برابری یا `>=,<=,>,<` روی کلیدهای context.
- اقدام‌ها: sms/bale/telegram/email (مشتری یا ادمین)، افزودن سرنخ/امتیاز، ساخت کار پیگیری.
- توکن‌های پیام: `{name} {phone} {amount} {invoice} {title} {balance} {site}`.

### Recruitment
- ۵ مرحله ویزارد (قابل فیلتر `nmc_apply_steps`) با نوار پیشرفت.
- موتور امتیازدهی: برای هر معیار، نسبت کلیدواژه‌های یافت‌شده × وزن؛ نرمال‌سازی به ۱۰۰.
- جزئیات امتیاز هر معیار ذخیره می‌شود (شفافیت در مصاحبه).
- ستون‌های ادمین: موقعیت/موبایل/امتیاز/مرحله.

### Integrations
- `Provider` interface → `SmsIr`, `MeliPayamak`, `Bale`, `Telegram`.
- `Notifier::send('sms|bale|telegram|admin|email', …)` + رویداد audit `nmc_notification_sent`.
- انتخاب سامانه پیامکی با فیلتر `nmc_sms_provider` (قابل جایگزینی با سامانه سفارشی).

### Gravity Forms Glue
- کلاس نئو روی فیلدها، دکمه ارسال نئو.
- بعد از هر ثبت: استخراج موبایل/ایمیل/نام → ساخت/همگام مشتری → ساخت پرونده CRM (اختیاری per-form) → رویداد.
- آینه اعلان‌های گراویتی به بله/تلگرام (per-form).
