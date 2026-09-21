'use strict';
/**
 * Demo seed — Persian sample content for every table family.
 * Run: npm run seed   (also auto-runs on empty DB at boot)
 */
const { get, run, addPoints, setSetting, all } = require('./index');
const { hashPassword } = require('../lib/auth');

if (get('SELECT id FROM users LIMIT 1')) {
  console.log('seed: DB already populated — skipping.');
  return;
}

console.log('seed: inserting demo data …');

/* Settings */
setSetting('site', { name: 'هوش یار پارسی نگر', tagline: 'دستیار هوشمند کسب‌وکار شما — اتوماسیون، CRM و فروش در یک پلتفرم' });
setSetting('theme', {
  color_bg: '#e8edf5', color_text: '#2f3542', color_muted: '#8a93a6',
  color_accent: '#6c5ce7', color_accent2: '#00b894',
  radius: 22, distance: 8, softness: 2, font_size_base: 16, container_width: 1180,
  soft_style: 'raised', header_layout: 'sticky', direction: 'rtl',
});
setSetting('contact', { phone: '۰۲۱-۹۱۰۰۲۲۳۳', email: 'hello@parsnegar.ai', address: 'تهران، خیابان ولیعصر، برج پارسی‌نگر' });
setSetting('socials', [
  { label: 'اینستاگرام', url: 'https://instagram.com/parsnegar' },
  { label: 'لینکدین', url: 'https://linkedin.com/company/parsnegar' },
  { label: 'تلگرام', url: 'https://t.me/parsnegar' },
]);
setSetting('integrations', {
  sms_ir: { api_key: '', otp_template_id: '' },
  melipayamak: { username: '', password: '', from: '' },
  bale: { token: '' },
  telegram: { token: '' },
  email: { host: '' },
});
setSetting('otp', { demo: true, channel: 'sms_ir' });
setSetting('sms_provider', 'sms_ir');
setSetting('loyalty', { signup_bonus: 100 });

/* Users */
const admin = run('INSERT INTO users (name, phone, password_hash, role) VALUES (?,?,?,?)',
  ['مدیر پارسی‌نگر', '09123456789', hashPassword('admin1234'), 'admin']);
run('INSERT INTO user_profiles (user_id, phone_verified) VALUES (?, 1)', [admin.id]);

const demo = run('INSERT INTO users (name, phone, password_hash) VALUES (?,?,?)',
  ['سارا محمدی', '09351112233', hashPassword('demo1234')]);
run('INSERT INTO user_profiles (user_id, phone_verified, company, city) VALUES (?,?,?,?)', [demo.id, 1, 'استارتاپ نیوشا', 'تهران']);
addPoints(demo.id, 250, 'earn', 'هدیه ثبت‌نام + فعالیت', 'users', demo.id);

/* Media placeholders */
const m1 = run(`INSERT INTO media (file_name, url, alt) VALUES (?,?,?)`, ['hero-ai.svg', '/assets/hero.svg', 'هوش مصنوعی']);
const m2 = run(`INSERT INTO media (file_name, url, alt) VALUES (?,?,?)`, ['avatar-1.svg', '/assets/avatar1.svg', 'آواتار']);

/* Services + features */
const s1 = run(`INSERT INTO services (title, slug, icon_text, short_desc, body, menu_order) VALUES (?,?,?,?,?,0)`,
  ['دستیار هوش مصنوعی اختصاصی', 'ai-assistant', '🤖', 'چت‌بات فارسی آموزش‌دیده روی دانش کسب‌وکار شما با اتصال به واتساپ و وب‌سایت.', 'مدل زبانی فارسی پارسی‌نگر روی دیتای شما ریزتنظیم می‌شود و در همه کانال‌ها یکصدا پاسخ می‌دهد.']);
run('INSERT INTO service_features (service_id, title, description, sort) VALUES (?,?,?,0)', [s1.id, 'درک فارسی', 'لهجه‌ها و اصطلاحات حوزه شما را می‌فهمد']);
run('INSERT INTO service_features (service_id, title, description, sort) VALUES (?,?,?,1)', [s1.id, 'یکپارچه با CRM', 'هر گفتگو به پروفایل مشتری ثبت می‌شود']);

const s2 = run(`INSERT INTO services (title, slug, icon_text, short_desc, body, menu_order) VALUES (?,?,?,?,?,1)`,
  ['اتوماسیون بازاریابی', 'automation', '⚡', 'کمپین‌های پیامکی، بله و تلگرام با قواعد شرطی و امتیازدهی وفاداری.', 'ماشهای رویدادی مثل ثبت‌نام، خرید و تیکت را به پیام و امتیاز و برچسب CRM تبدیل کنید.']);
const s3 = run(`INSERT INTO services (title, slug, icon_text, short_desc, body, menu_order) VALUES (?,?,?,?,?,2)`,
  ['CRM و باشگاه مشتریان', 'crm-loyalty', '🎯', 'بخش‌بندی مشتریان، ردیابی خدمات انتخابی و باشگاه وفاداری یکپارچه.', 'مشتریان را بر اساس رفتار بخش‌بندی کنید و امتیازها را به پاداش واقعی تبدیل کنید.']);
const s4 = run(`INSERT INTO services (title, slug, icon_text, short_desc, body, menu_order) VALUES (?,?,?,?,?,3)`,
  ['فاکتور و درگاه پرداخت', 'invoicing', '💳', 'فاکتور حرفه‌ای، لینک پرداخت اختصاصی و ارسال خودکار برای مشتری.', 'با یک لینک، مشتری فاکتور را آنلاین پرداخت می‌کند؛ وضعیت خودکار «پرداخت‌شده» می‌شود.']);

/* Projects */
const p1 = run(`INSERT INTO projects (title, slug, client, year, project_url, status, featured, body, menu_order) VALUES (?,?,?,?,?,?,1,?,0)`,
  ['فروشگاه هوشمند نیوشا', 'newsha-ai-shop', 'نیوشا', '۱۴۰۳', 'https://newsha.example', 'completed', 'پیاده‌سازی دستیار خرید و اتوماسیون سبد رهاشده با افزایش ۳ برابری نرخ تبدیل.']);
run('INSERT INTO project_highlights (project_id, text, sort) VALUES (?,?,0)', [p1.id, 'افزایش ۳ برابری نرخ تبدیل']);
run('INSERT INTO project_highlights (project_id, text, sort) VALUES (?,?,1)', [p1.id, 'کاهش ۶۰ درصدی تیکت‌های پشتیبانی']);
const p2 = run(`INSERT INTO projects (title, slug, client, year, project_url, status, featured, body, menu_order) VALUES (?,?,?,?,?,?,1,?,1)`,
  ['پنل مشتریان هلدینگ آریا', 'arya-panel', 'هلدینگ آریا', '۱۴۰۲', '', 'showcase', 'پنل مالی و وفاداری برای ۱۲ هزار مشتری سازمانی.']);
const p3 = run(`INSERT INTO projects (title, slug, client, year, project_url, status, featured, body, menu_order) VALUES (?,?,?,?,?,?,0,?,2)`,
  ['ربات بله فروشگاه کافه‌یار', 'cafe-yar-bot', 'کافه‌یار', '۱۴۰۳', '', 'ongoing', 'سفارش‌گیری و وفاداری در بله با اتصال به صندوق.']);

/* Team */
run('INSERT INTO team_members (name, role_title, bio, photo_media_id, sort) VALUES (?,?,?,?,0)', ['آرش پارسی', 'مدیرعامل و بنیان‌گذار', '۱۲ سال تجربه در هوش مصنوعی و محصولات داده‌محور.', m2.id]);
run('INSERT INTO team_members (name, role_title, bio, photo_media_id, sort) VALUES (?,?,?,?,1)', ['نگار کاظمی', 'مدیر محصول', 'متخصص تجربه کاربری و رشد محصول.', m2.id]);
run('INSERT INTO team_members (name, role_title, bio, photo_media_id, sort) VALUES (?,?,?,?,2)', ['امیر رضایی', 'ML Lead', 'پژوهشگر پردازش زبان فارسی.', m2.id]);

/* Categories + posts */
const c1 = run('INSERT INTO categories (name, slug) VALUES (?,?)', ['هوش مصنوعی', 'ai']);
const c2 = run('INSERT INTO categories (name, slug) VALUES (?,?)', ['رشد کسب‌وکار', 'growth']);
run(`INSERT INTO posts (title, slug, excerpt, body, category_id, cover_media_id, author_id, status, published_at)
 VALUES (?,?,?,?,?,?,?, 'published', datetime('now'))`,
  ['چرا دستیار هوشمند، آینده پشتیبانی مشتریان است', 'ai-support-future', 'پاسخ‌دهی ۲۴ ساعته و کاهش هزینه تا ۷۰ درصد.', 'در این مقاله بررسی می‌کنیم چگونه یک دستیار هوش مصنوعی فارسی می‌تواند بار تیم پشتیبانی را کم کند و رضایت مشتری را بالا ببرد…', c1.id, m1.id, admin.id]);
run(`INSERT INTO posts (title, slug, excerpt, body, category_id, cover_media_id, author_id, status, published_at)
 VALUES (?,?,?,?,?,?,?, 'published', datetime('now', '-3 days'))`,
  ['وفاداری مشتری در اقتصاد ایران: امتیاز به‌جای تخفیف', 'loyalty-iran', 'باشگاه مشتریان چه تاثیری بر فروش تکرارشونده دارد؟', 'تخفیف‌های مداوم برند را تضعیف می‌کنند؛ اما اقتصاد امتیازی…', c2.id, m1.id, admin.id]);

/* Products */
run(`INSERT INTO products (title, slug, price, sale_price, stock, short_desc, body) VALUES (?,?,?,?,?,?,?)`,
  ['اشتراک ماهانه دستیار پارسی‌نگر', 'assistant-monthly', 490000, 390000, 100, 'شامل ۵۰۰۰ گفتگوی هوشمند در ماه + پشتیبانی.', 'دسترسی کامل به دستیار هوشمند، داشبورد تحلیل و یکپارچه‌سازی پیامک/بله.']);
run(`INSERT INTO products (title, slug, price, sale_price, stock, short_desc, body) VALUES (?,?,?,?,?,?,?)`,
  ['بسته راه‌اندازی CRM', 'crm-setup', 2900000, null, 20, 'پیاده‌سازی CRM، بخش‌بندی و اتوماسیون‌های پایه.', 'شامل جلسه مشاوره، دیتامایگریشن و آموزش تیم.']);
run(`INSERT INTO products (title, slug, price, sale_price, stock, short_desc, body) VALUES (?,?,?,?,?,?,?)`,
  ['افزونه فاکتور و لینک پرداخت', 'invoice-addon', 890000, null, 50, 'فاکتور حرفه‌ای با لینک پرداخت اختصاصی.', 'سازگار با زرین‌پال و آیدی‌پی.']);

/* Demo order + invoice for demo user */
const o1 = run('INSERT INTO orders (user_id, subtotal, discount, total, status, paid_at) VALUES (?,?,?,?,?, datetime(\'now\'))', [demo.id, 390000, 0, 390000, 'paid']);
run('INSERT INTO order_items (order_id, product_id, title, qty, unit_price, line_total) VALUES (?,?,?,?,?,?)', [o1.id, 1, 'اشتراک ماهانه دستیار پارسی‌نگر', 1, 390000, 390000]);
const inv1 = run(`INSERT INTO invoices (number, user_id, token, status, title, subtotal, total, paid_at, gateway, gateway_ref)
 VALUES (?,?,?,?,?,?,?,?,?,?)`, ['PN-1001', demo.id, 'demoToken1001', 'paid', 'فاکتور سفارش #' + o1.id, 390000, 390000, new Date().toISOString(), 'zarinpal', 'A00001']);
run('INSERT INTO invoice_items (invoice_id, title, qty, unit_price, line_total) VALUES (?,?,?,?,?)', [inv1.id, 'اشتراک ماهانه دستیار پارسی‌نگر', 1, 390000, 390000]);
const inv2 = run(`INSERT INTO invoices (number, user_id, token, status, title, subtotal, total, note)
 VALUES (?,?,?,?,?,?,?,?)`, ['PN-1002', demo.id, 'demoToken1002', 'unpaid', 'تمدید اشتراک — مهر ۱۴۰۵', 490000, 490000, 'قابل پرداخت تا ۷ روز آینده']);
run('INSERT INTO invoice_items (invoice_id, title, qty, unit_price, line_total) VALUES (?,?,?,?,?)', [inv2.id, 'اشتراک ماهانه (تمدید)', 1, 490000, 490000]);

/* Ticket */
const t1 = run('INSERT INTO tickets (user_id, subject, department, status) VALUES (?,?,?,?)', [demo.id, 'اتصال دستیار به واتساپ', 'support', 'answered']);
run('INSERT INTO ticket_messages (ticket_id, user_id, body) VALUES (?,?,?)', [t1.id, demo.id, 'سلام، چطور دستیار را به واتساپ بیزینس وصل کنم؟']);
run('INSERT INTO ticket_messages (ticket_id, user_id, is_staff, body) VALUES (?,?,1,?)', [t1.id, admin.id, 'سلام، از بخش یکپارچه‌سازی‌ها توکن واتساپ را وارد کنید؛ راهنما در مستندات است.']);

/* Consultation */
run('INSERT INTO consultations (user_id, type, subject, body, status) VALUES (?,?,?,?,?)', [demo.id, 'consult', 'مشاوره اتوماسیون فروش', 'می‌خواهیم سبد رهاشده را خودکار کنیم.', 'new']);

/* Loyalty history */
run(`INSERT INTO loyalty_transactions (user_id, delta, balance_after, type, description) VALUES (?,?,?, 'earn', 'هدیه ثبت‌نام')`, [demo.id, 100, 100]);
run(`INSERT INTO loyalty_transactions (user_id, delta, balance_after, type, description) VALUES (?,?,?, 'earn', 'خرید اشتراک ماهانه')`, [demo.id, 39, 139]);
run(`INSERT INTO loyalty_transactions (user_id, delta, balance_after, type, description) VALUES (?,?,?, 'earn', 'دعوت دوست')`, [demo.id, 111, 250]);

run('INSERT INTO loyalty_rewards (title, cost, kind, value) VALUES (?,?,?,?)', ['۱۰٪ تخفیف خرید', 500, 'percent', 10]);
run('INSERT INTO loyalty_rewards (title, cost, kind, value) VALUES (?,?,?,?)', ['ارسال رایگان', 200, 'shipping', 0]);
run('INSERT INTO loyalty_rewards (title, cost, kind, value) VALUES (?,?,?,?)', ['۵۰۰ هزار تومان اعتبار', 1200, 'fixed', 500000]);

/* Segments + tracking */
const seg1 = run('INSERT INTO segments (title, description, color) VALUES (?,?,?)', ['مشتریان VIP', 'خرید بالای ۲ میلیون', '#6c5ce7']);
const seg2 = run('INSERT INTO segments (title, description, color) VALUES (?,?,?)', ['علاقه‌مند به اتوماسیون', 'سرویس اتوماسیون را انتخاب کرده‌اند', '#00b894']);
run('INSERT INTO user_segments (user_id, segment_id) VALUES (?,?)', [demo.id, seg1.id]);
run('INSERT INTO user_services (user_id, service_id, status) VALUES (?,?,?)', [demo.id, s2.id, 'interested']);
run('INSERT INTO user_services (user_id, service_id, status) VALUES (?,?,?)', [demo.id, s1.id, 'purchased']);

/* Jobs + criteria + sample application */
const j1 = run(`INSERT INTO jobs (title, department, job_type, location, description, min_score) VALUES (?,?,?,?,?,?)`,
  ['مهندس یادگیری ماشین (فارسی)', 'هوش مصنوعی', 'full-time', 'تهران / هیبرید', 'توسعه مدل‌های زبانی فارسی و ریزتنظیم روی دامنه مشتریان.', 70]);
run('INSERT INTO job_criteria (job_id, label, weight, keywords, sort) VALUES (?,?,?,?,0)', [j1.id, 'تجربه Python/ML', 3, 'python,tensorflow,pytorch,یادگیری ماشین,ML']);
run('INSERT INTO job_criteria (job_id, label, weight, keywords, sort) VALUES (?,?,?,?,1)', [j1.id, 'پردازش زبان فارسی', 3, 'nlp,زبان فارسی,transformer,بردار,توکن']);
run('INSERT INTO job_criteria (job_id, label, weight, keywords, sort) VALUES (?,?,?,?,2)', [j1.id, 'کار تیمی و ارتباطات', 1, 'تیم,ارتباط,همکاری,agile']);
run(`INSERT INTO applications (job_id, full_name, phone, email, answers_json, score, score_details_json, stage)
 VALUES (?,?,?,?,?,?,?,?)`,
  [j1.id, 'مهدی رستگار', '09120001122', 'mehdi@example.com',
   JSON.stringify({ experience: '۴ سال Python و PyTorch، NLP فارسی', motivation: 'علاقه‌مند به زبان فارسی' }),
   85, JSON.stringify([{ label: 'تجربه Python/ML', weight: 3, ratio: 1, points: 3 }]), 'video']);

const j2 = run(`INSERT INTO jobs (title, department, job_type, location, description, min_score) VALUES (?,?,?,?,?,?)`,
  ['کارشناس موفقیت مشتری', 'فروش', 'full-time', 'اصفهان', 'همراهی مشتریان از آنبوردینگ تا تمدید.', 60]);
run('INSERT INTO job_criteria (job_id, label, weight, keywords, sort) VALUES (?,?,?,?,0)', [j2.id, 'تجربه فروش/پشتیبانی', 2, 'فروش,پشتیبانی,مشتری,CRM']);

/* Automation rules */
run(`INSERT INTO automation_rules (title, event_name, condition_field, condition_op, condition_value, channel, target, template) VALUES (?,?,?,?,?,?,?,?)`,
  ['خوش‌آمدگویی ثبت‌نام (پیامک)', 'user_registered', '', 'gte', '', 'sms_ir', '', 'سلام {name} عزیز 👋 به {site} خوش آمدید! ۱۰۰ امتیاز هدیه به حسابتان اضافه شد.']);
run(`INSERT INTO automation_rules (title, event_name, condition_field, condition_op, condition_value, channel, target, template) VALUES (?,?,?,?,?,?,?,?)`,
  ['اعلان تلگرام خرید بالای ۵۰۰ هزار', 'order_paid', 'amount', 'gte', '500000', 'telegram', '', '🛍 خرید جدید: {title} — مبلغ {amount} تومان از {name} ({phone})']);
run(`INSERT INTO automation_rules (title, event_name, condition_field, condition_op, condition_value, channel, target, template) VALUES (?,?,?,?,?,?,?,?)`,
  ['برچسب VIP بعد از خرید بالای ۲ میلیون', 'invoice_paid', 'amount', 'gte', '2000000', 'add_segment', String(seg1.id), '']);
run(`INSERT INTO automation_rules (title, event_name, condition_field, condition_op, condition_value, channel, target, template) VALUES (?,?,?,?,?,?,?,?)`,
  ['۱۰ امتیاز وفاداری بابت هر فاکتور پرداخت‌شده', 'invoice_paid', '', 'gte', '', 'add_points', '10', '']);
run(`INSERT INTO automation_rules (title, event_name, condition_field, condition_op, condition_value, channel, target, template) VALUES (?,?,?,?,?,?,?,?)`,
  ['پیامک یادآوری فاکتور به بل (بله)', 'application_received', 'amount', 'gte', '70', 'bale', '', '✅ درخواست‌کننده امتیاز بالا: {name} با امتیاز {amount} برای {title}']);

console.log('seed: done ✓');
console.log('seed: admin → 09123456789 / admin1234  |  customer → 09351112233 / demo1234');
