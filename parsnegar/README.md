# هوش یار پارسی نگر 🧠
**دستیار هوشمند کسب‌وکار** — وب‌اپ React + CMS اختصاصی + دیتابیس حرفه‌ای با جداول تفکیک‌شده

همه امکانات قالب نئومورف وردپرس، این بار به‌صورت یک محصول مستقل:

| بخش | امکانات |
|---|---|
| 🌐 وب‌سایت | خانه (Hero/خدمات/درباره/پروژه‌ها/تیم/وبلاگ/محصولات/تماس)، وبلاگ، فروشگاه و صفحه محصول، سبد خرید |
| 🔐 احراز هویت | ورود OTP پیامکی + ورود/ثبت‌نام با رمز (scrypt) |
| 👤 پنل مشتری | داشبورد، خریدها، مالی/فاکتور + لینک پرداخت، باشگاه وفاداری (امتیاز/پاداش)، تیکت‌ها، مشاوره/پشتیبانی، «خدمات من» (ردیابی سرویس‌های انتخابی)، پروفایل |
| 🛠 CMS اختصاصی | داشبورد آماری، مدیریت خدمات/پروژه‌ها/تیم/مطالب/محصولات (فرم‌های کارتی با سوئیچ، سگمنت، اسلایدر و ردیف‌های تکرارشونده)، سفارش‌ها، صدور فاکتور + ارسال به مشتری با لینک پرداخت، تیکت‌ها، مشاوره‌ها، متقاضیان با امتیاز خودکار، موقعیت‌ها + معیارهای امتیازدهی، مشتریان/سگمنت‌بندی CRM، اتوماسیون، صندوق اعلان، تنظیمات قالب (رنگ/شعاع/سایه/فونت زنده) |
| 💼 استخدام چندمرحله‌ای | فرم ۵ مرحله‌ای + امتیازدهی خودکار (وزن معیار × کلیدواژه‌ها)؛ امتیاز ≥ حد نصاب → دعوت خودکار به مصاحبه ویدیویی |
| ⚡ اتوماسیون داخلی | قواعد رویدادمحور (ثبت‌نام/خرید/فاکتور/تیکت/درخواست شغلی/پاداش) با شرط + اقدام: پیامک/بله/تلگرام/ایمیل/افزودن سگمنت/افزودن امتیاز |
| 🔌 یکپارچه‌سازی | sms.ir، ملی‌پیامک، Bale، Telegram (اداپترهای HTTP واقعی + خروجی قابل‌حساب در جدول outbox) |
| 🗄 دیتابیس | ۳۳ جدول تفکیک‌شده با کلید خارجی، ایندکس و تایم‌استمپ (SQLite از `node:sqlite`) |

## ساختار پروژه

```
parsnegar/
├── server/                 # Node.js + Express + SQLite (CMS/API)
│   └── src/
│       ├── db/schema.sql   # اسکیمای حرفه‌ای: ۳۳ جدول تفکیک‌شده
│       ├── db/index.js     # لایه دسترسی به دیتابیس + تنظیمات + امتیازها
│       ├── db/seed.js      # دیتای نمایشی فارسی (کاربران/محتوا/سفارش/تیکت/اتوماسیون)
│       ├── lib/            # auth (scrypt) — notify (sms.ir/ملی‌پیامک/بله/تلگرام) — events (اتوماسیون) — scoring
│       ├── middleware/     # احراز هویت Bearer + نقش‌ها
│       └── routes/         # auth / public / panel / applications / cms
└── client/                 # React 18 + Vite + React Router (RTL/فارسی/نئومورف)
    └── src/
        ├── components/ui.jsx   # کیت رابط: کارت، سوئیچ، سگمنت، اسلایدر، رنگ، جدول، مودال، Repeater
        ├── pages/Home,Content,Shop,Auth,Careers,Pay   # وب‌سایت
        ├── pages/Panel.jsx     # پنل مشتری
        └── pages/Cms.jsx       # CMS اختصاصی
```

## راه‌اندازی سریع

```bash
# ۱) سرور (پورت 4170) — دیتابیس خودکار ساخته و seed می‌شود
cd parsnegar/server && npm install && npm start

# ۲) کلاینت (پورت 5173 — پراکسی /api به سرور)
cd parsnegar/client && npm install && npm run dev
```

**حالت تولید (یک فرآیند):** بعد از `npm run build` در کلاینت، همان `npm start` سرور، فایل‌های build شده را هم سرو می‌کند.

```bash
cd parsnegar/client && npm run build
cd ../server && npm start     # همه‌چیز روی http://localhost:4170
```

## حساب‌های نمایشی

| نقش | موبایل | رمز |
|---|---|---|
| مدیر CMS | 09123456789 | admin1234 |
| مشتری نمونه | 09351112233 | demo1234 |

ورود OTP در «حالت نمایشی» کد را در پاسخ API برمی‌گرداند (بدون نیاز به پنل پیامکی واقعی). با واردکردن توکن‌های sms.ir / ملی‌پیامک / بله / تلگرام در **CMS ← تنظیمات قالب ← یکپارچه‌سازی‌ها**، ارسال‌ها واقعی می‌شوند.

## API سریع

- `POST /api/auth/otp/send` → `{ demo_code }` | `POST /api/auth/otp/verify` → `{ token, user }`
- `GET /api/{meta,services,projects,team,posts,products,jobs,rewards}`
- `POST /api/applications` → امتیازدهی خودکار + تعیین مرحله
- `/api/my/*` (Bearer) → checkout، پرداخت فاکتور، تیکت، مشاوره، وفاداری، خدمات من، پروفایل
- `/api/cms/*` (نقش admin/crm) → CRUD عمومی + `x/stats`، `x/orders`، `x/invoices` (صدور/ارسال)، `x/tickets`، `x/applications`، `x/users`، `settings/all`
- لینک پرداخت عمومی مشتری: `/pay/:token`

## جداول دیتابیس (۳۳)

`users` `user_profiles` `media` `otp_codes` `sessions` ‖ `categories` `posts` `services` `service_features` `projects` `project_highlights` `team_members` ‖ `products` `orders` `order_items` `invoices` `invoice_items` `payments` ‖ `tickets` `ticket_messages` `consultations` ‖ `loyalty_transactions` `loyalty_rewards` ‖ `segments` `user_segments` `user_services` ‖ `jobs` `job_criteria` `applications` ‖ `automation_rules` `automation_logs` `notification_outbox` `settings`

فایل دیتابیس: `server/data/parsnegar.db` (قابل تغییر با متغیر `PARSNegar_DB` — `PARSNegar_DB`).
