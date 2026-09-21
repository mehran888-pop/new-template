'use strict';
/**
 * CMS API — full CRUD for content, commerce, CRM, automation, settings.
 * Admin/CRM role required. Generic entity registry keeps it tidy.
 */
const express = require('express');
const { get, all, run, getSetting, setSetting } = require('../db');
const { requireAdmin } = require('../middleware/auth');
const { hashPassword } = require('../lib/auth');

const router = express.Router();
router.use(requireAdmin);

/**
 * Entity registry: table → { fields (writable columns), search, order }
 * Nested children (features/highlights/criteria/messages) have dedicated endpoints.
 */
const ENTITIES = {
  services: {
    table: 'services',
    fields: ['title', 'slug', 'icon_text', 'short_desc', 'body', 'link', 'menu_order', 'is_active'],
    order: 'menu_order, id',
  },
  service_features: {
    table: 'service_features',
    fields: ['service_id', 'title', 'description', 'sort'],
    order: 'sort, id',
  },
  projects: {
    table: 'projects',
    fields: ['title', 'slug', 'client', 'year', 'project_url', 'status', 'featured', 'body', 'excerpt', 'menu_order'],
    order: 'featured DESC, menu_order, id',
  },
  project_highlights: {
    table: 'project_highlights',
    fields: ['project_id', 'text', 'sort'],
    order: 'sort, id',
  },
  team: {
    table: 'team_members',
    fields: ['name', 'role_title', 'bio', 'socials_json', 'sort'],
    order: 'sort, id',
  },
  posts: {
    table: 'posts',
    fields: ['title', 'slug', 'excerpt', 'body', 'category_id', 'status', 'published_at'],
    order: 'id DESC',
  },
  categories: {
    table: 'categories',
    fields: ['name', 'slug', 'parent_id'],
    order: 'id',
  },
  products: {
    table: 'products',
    fields: ['title', 'slug', 'price', 'sale_price', 'stock', 'short_desc', 'body', 'status'],
    order: 'id DESC',
  },
  jobs: {
    table: 'jobs',
    fields: ['title', 'department', 'job_type', 'location', 'description', 'min_score', 'status'],
    order: 'id DESC',
  },
  job_criteria: {
    table: 'job_criteria',
    fields: ['job_id', 'label', 'weight', 'keywords', 'sort'],
    order: 'sort, id',
  },
  rewards: {
    table: 'loyalty_rewards',
    fields: ['title', 'cost', 'kind', 'value', 'is_active'],
    order: 'cost',
  },
  segments: {
    table: 'segments',
    fields: ['title', 'description', 'color'],
    order: 'id',
  },
  automation: {
    table: 'automation_rules',
    fields: ['title', 'event_name', 'condition_field', 'condition_op', 'condition_value', 'channel', 'target', 'template', 'is_active'],
    order: 'id DESC',
  },
};

function entityCfg(req) {
  return ENTITIES[req.params.entity] || null;
}

/** LIST */
router.get('/:entity', (req, res, next) => {
  const cfg = entityCfg(req);
  if (!cfg) return next('route');
  let sql = `SELECT * FROM ${cfg.table} ORDER BY ${cfg.order}`;
  const params = [];
  if (req.query.limit) {
    sql += ' LIMIT ?';
    params.push(Math.min(500, Number(req.query.limit) || 100));
  }
  res.json(all(sql, params));
});

/** READ */
router.get('/:entity/:id', (req, res, next) => {
  const cfg = entityCfg(req);
  if (!cfg) return next('route');
  const row = get(`SELECT * FROM ${cfg.table} WHERE id = ?`, [req.params.id]);
  if (!row) return res.status(404).json({ error: 'یافت نشد' });
  // Attach children for edit forms.
  if (cfg.table === 'services') row.features = all('SELECT * FROM service_features WHERE service_id = ? ORDER BY sort', [row.id]);
  if (cfg.table === 'projects') row.highlights = all('SELECT * FROM project_highlights WHERE project_id = ? ORDER BY sort', [row.id]);
  if (cfg.table === 'jobs') row.criteria = all('SELECT * FROM job_criteria WHERE job_id = ? ORDER BY sort', [row.id]);
  res.json(row);
});

/** CREATE */
router.post('/:entity', (req, res, next) => {
  const cfg = entityCfg(req);
  if (!cfg) return next('route');
  const data = {};
  for (const f of cfg.fields) if (req.body[f] !== undefined) data[f] = req.body[f];
  if (!Object.keys(data).length) return res.status(400).json({ error: 'فیلدی ارسال نشده' });
  const cols = Object.keys(data);
  const ins = run(
    `INSERT INTO ${cfg.table} (${cols.join(',')}) VALUES (${cols.map(() => '?').join(',')})`,
    cols.map((c) => data[c])
  );
  res.status(201).json({ id: ins.id, ...data });
});

/** UPDATE */
router.put('/:entity/:id', (req, res, next) => {
  const cfg = entityCfg(req);
  if (!cfg) return next('route');
  const data = {};
  for (const f of cfg.fields) if (req.body[f] !== undefined) data[f] = req.body[f];
  const cols = Object.keys(data);
  if (cols.length) {
    run(
      `UPDATE ${cfg.table} SET ${cols.map((c) => c + ' = ?').join(', ')} WHERE id = ?`,
      [...cols.map((c) => data[c]), req.params.id]
    );
  }
  res.json({ ok: true });
});

/** DELETE */
router.delete('/:entity/:id', (req, res, next) => {
  const cfg = entityCfg(req);
  if (!cfg) return next('route');
  run(`DELETE FROM ${cfg.table} WHERE id = ?`, [req.params.id]);
  res.json({ ok: true });
});

/* ── Nested: replace children sets in one call ── */
router.post('/services/:id/features', (req, res) => {
  const rows = Array.isArray(req.body.rows) ? req.body.rows : [];
  run('DELETE FROM service_features WHERE service_id = ?', [req.params.id]);
  rows.forEach((r, i) =>
    run('INSERT INTO service_features (service_id, title, description, sort) VALUES (?,?,?,?)',
      [req.params.id, String(r.title || ''), String(r.description || ''), i])
  );
  res.json({ ok: true });
});
router.post('/projects/:id/highlights', (req, res) => {
  const rows = Array.isArray(req.body.rows) ? req.body.rows : [];
  run('DELETE FROM project_highlights WHERE project_id = ?', [req.params.id]);
  rows.forEach((r, i) =>
    run('INSERT INTO project_highlights (project_id, text, sort) VALUES (?,?,?)', [req.params.id, String(r.text || ''), i])
  );
  res.json({ ok: true });
});
router.post('/jobs/:id/criteria', (req, res) => {
  const rows = Array.isArray(req.body.rows) ? req.body.rows : [];
  run('DELETE FROM job_criteria WHERE job_id = ?', [req.params.id]);
  rows.forEach((r, i) =>
    run('INSERT INTO job_criteria (job_id, label, weight, keywords, sort) VALUES (?,?,?,?,?)',
      [req.params.id, String(r.label || ''), Number(r.weight) || 1, String(r.keywords || ''), i])
  );
  res.json({ ok: true });
});

/* ── Read models (lists with joins) ── */
router.get('/x/orders', (req, res) =>
  res.json(all(`SELECT o.*, u.name AS user_name, u.phone FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.id DESC`))
);
router.get('/x/orders/:id', (req, res) => {
  const order = get(`SELECT o.*, u.name AS user_name, u.phone FROM orders o JOIN users u ON u.id = o.user_id WHERE o.id = ?`, [req.params.id]);
  if (!order) return res.status(404).json({ error: 'سفارش یافت نشد' });
  order.items = all('SELECT * FROM order_items WHERE order_id = ?', [order.id]);
  res.json(order);
});
router.post('/x/orders/:id/status', (req, res) => {
  const status = ['pending', 'paid', 'shipped', 'completed', 'cancelled'].includes(req.body.status) ? req.body.status : 'pending';
  run('UPDATE orders SET status = ? WHERE id = ?', [status, req.params.id]);
  res.json({ ok: true });
});

router.get('/x/invoices', (req, res) =>
  res.json(all('SELECT i.*, u.name AS user_name, u.phone FROM invoices i JOIN users u ON u.id = i.user_id ORDER BY i.id DESC'))
);
router.post('/x/invoices', (req, res) => {
  const userId = Number(req.body.user_id);
  const items = Array.isArray(req.body.items) ? req.body.items : [];
  const u = get('SELECT id FROM users WHERE id = ?', [userId]);
  if (!u || !items.length) return res.status(400).json({ error: 'کاربر و اقلام فاکتور لازم است' });
  const subtotal = items.reduce((s, it) => s + (Number(it.unit_price) || 0) * (Number(it.qty) || 1), 0);
  const discount = Number(req.body.discount || 0);
  const tax = Number(req.body.tax || 0);
  const total = Math.max(0, subtotal - discount + tax);
  const count = get('SELECT COUNT(*) AS c FROM invoices').c || 0;
  const number = 'PN-' + (1000 + count + 1);
  const tok = require('crypto').randomBytes(16).toString('hex');
  const inv = run(
    `INSERT INTO invoices (number, user_id, token, status, title, subtotal, discount, tax, total, note, due_at)
     VALUES (?,?,?,'unpaid',?,?,?,?,?,?, datetime('now','+7 days'))`,
    [number, userId, tok, String(req.body.title || 'فاکتور خدمات'), subtotal, discount, tax, total, String(req.body.note || '')]
  );
  for (const it of items) {
    const qty = Math.max(1, Number(it.qty) || 1);
    const price = Number(it.unit_price) || 0;
    run('INSERT INTO invoice_items (invoice_id, title, qty, unit_price, line_total) VALUES (?,?,?,?,?)',
      [inv.id, String(it.title || ''), qty, price, price * qty]);
  }
  res.status(201).json({ id: inv.id, number, token: tok, total });
});
router.post('/x/invoices/:id/send', (req, res) => {
  const inv = get('SELECT i.*, u.name, u.phone FROM invoices i JOIN users u ON u.id = i.user_id WHERE i.id = ?', [req.params.id]);
  if (!inv) return res.status(404).json({ error: 'فاکتور یافت نشد' });
  const { notifyUser } = require('../lib/notify');
  notifyUser(inv, `فاکتور ${inv.number} به مبلغ ${Number(inv.total).toLocaleString('fa-IR')} تومان آماده پرداخت است.\nلینک پرداخت: /pay/${inv.token}`);
  res.json({ ok: true, link: '/pay/' + inv.token });
});
router.post('/x/invoices/:id/status', (req, res) => {
  const status = ['draft', 'unpaid', 'paid', 'cancelled', 'expired'].includes(req.body.status) ? req.body.status : 'unpaid';
  run('UPDATE invoices SET status = ? WHERE id = ?', [status, req.params.id]);
  res.json({ ok: true });
});

router.get('/x/tickets', (req, res) =>
  res.json(all('SELECT t.*, u.name AS user_name, u.phone FROM tickets t JOIN users u ON u.id = t.user_id ORDER BY t.updated_at DESC'))
);
router.get('/x/tickets/:id', (req, res) => {
  const t = get('SELECT t.*, u.name AS user_name, u.phone FROM tickets t JOIN users u ON u.id = t.user_id WHERE t.id = ?', [req.params.id]);
  if (!t) return res.status(404).json({ error: 'تیکت یافت نشد' });
  t.messages = all('SELECT tm.*, u.name FROM ticket_messages tm JOIN users u ON u.id = tm.user_id WHERE tm.ticket_id = ? ORDER BY tm.created_at', [t.id]);
  res.json(t);
});
router.post('/x/tickets/:id/reply', (req, res) => {
  const t = get('SELECT * FROM tickets WHERE id = ?', [req.params.id]);
  if (!t) return res.status(404).json({ error: 'تیکت یافت نشد' });
  run('INSERT INTO ticket_messages (ticket_id, user_id, is_staff, body) VALUES (?,?,1,?)', [t.id, req.user.id, String(req.body.body || '')]);
  run(`UPDATE tickets SET status = 'answered', updated_at = datetime('now') WHERE id = ?`, [t.id]);
  res.json({ ok: true });
});

router.get('/x/applications', (req, res) =>
  res.json(all('SELECT a.*, j.title AS job_title FROM applications a JOIN jobs j ON j.id = a.job_id ORDER BY a.score DESC, a.id DESC'))
);
router.post('/x/applications/:id/stage', (req, res) => {
  const stage = ['applied', 'screening', 'video', 'test', 'interview', 'hired', 'rejected'].includes(req.body.stage) ? req.body.stage : 'applied';
  run('UPDATE applications SET stage = ? WHERE id = ?', [stage, req.params.id]);
  res.json({ ok: true });
});

router.get('/x/users', (req, res) =>
  res.json(all(`SELECT u.id, u.name, u.phone, u.email, u.role, u.points_balance, u.status, u.created_at,
    (SELECT group_concat(s.title) FROM user_segments us JOIN segments s ON s.id = us.segment_id WHERE us.user_id = u.id) AS segments
    FROM users u ORDER BY u.id DESC`))
);
router.post('/x/users/:id/segments', (req, res) => {
  const segId = Number(req.body.segment_id);
  run('INSERT OR IGNORE INTO user_segments (user_id, segment_id) VALUES (?,?)', [req.params.id, segId]);
  res.json({ ok: true });
});
router.delete('/x/users/:id/segments/:segId', (req, res) => {
  run('DELETE FROM user_segments WHERE user_id = ? AND segment_id = ?', [req.params.id, req.params.segId]);
  res.json({ ok: true });
});
router.post('/x/users', (req, res) => {
  const name = String(req.body.name || '').trim();
  const phone = String(req.body.phone || '').replace(/\D/g, '');
  const role = ['customer', 'crm', 'admin'].includes(req.body.role) ? req.body.role : 'customer';
  if (!/^09\d{9}$/.test(phone)) return res.status(400).json({ error: 'شماره معتبر نیست' });
  if (get('SELECT id FROM users WHERE phone = ?', [phone])) return res.status(409).json({ error: 'شماره تکراری است' });
  const ins = run('INSERT INTO users (name, phone, role, password_hash) VALUES (?,?,?,?)',
    [name, phone, role, hashPassword(String(req.body.password || '123456'))]);
  run('INSERT INTO user_profiles (user_id) VALUES (?)', [ins.id]);
  res.status(201).json({ id: ins.id });
});

router.get('/x/consultations', (req, res) =>
  res.json(all('SELECT c.*, u.name AS user_name, u.phone FROM consultations c JOIN users u ON u.id = c.user_id ORDER BY c.id DESC'))
);
router.post('/x/consultations/:id/answer', (req, res) => {
  run(`UPDATE consultations SET answer = ?, status = 'answered', answered_at = datetime('now') WHERE id = ?`,
    [String(req.body.answer || ''), req.params.id]);
  res.json({ ok: true });
});

router.get('/x/automation-logs', (req, res) =>
  res.json(all('SELECT al.*, r.title AS rule_title FROM automation_logs al LEFT JOIN automation_rules r ON r.id = al.rule_id ORDER BY al.id DESC LIMIT 200'))
);
router.get('/x/outbox', (req, res) =>
  res.json(all('SELECT * FROM notification_outbox ORDER BY id DESC LIMIT 200'))
);

/* ── Dashboard stats ── */
router.get('/x/stats', (req, res) => {
  res.json({
    users: get('SELECT COUNT(*) c FROM users').c,
    posts: get('SELECT COUNT(*) c FROM posts').c,
    products: get('SELECT COUNT(*) c FROM products').c,
    orders: get('SELECT COUNT(*) c FROM orders').c,
    paidOrders: get(`SELECT COUNT(*) c FROM orders WHERE status != 'pending'`).c,
    revenue: get(`SELECT COALESCE(SUM(total),0) c FROM invoices WHERE status = 'paid'`).c,
    openTickets: get(`SELECT COUNT(*) c FROM tickets WHERE status != 'closed'`).c,
    applications: get('SELECT COUNT(*) c FROM applications').c,
    services: get('SELECT COUNT(*) c FROM services').c,
    projects: get('SELECT COUNT(*) c FROM projects').c,
    unpaidInvoices: get(`SELECT COUNT(*) c FROM invoices WHERE status = 'unpaid'`).c,
  });
});

/* ── Settings & integrations ── */
router.get('/settings/all', (req, res) => {
  res.json({
    site: getSetting('site', {}),
    theme: getSetting('theme', {}),
    contact: getSetting('contact', {}),
    socials: getSetting('socials', []),
    integrations: getSetting('integrations', {}),
    otp: getSetting('otp', { demo: true, channel: 'sms_ir' }),
    sms_provider: getSetting('sms_provider', 'sms_ir'),
    loyalty: getSetting('loyalty', { signup_bonus: 100 }),
  });
});
router.put('/settings', (req, res) => {
  for (const key of ['site', 'theme', 'contact', 'socials', 'integrations', 'otp', 'sms_provider', 'loyalty']) {
    if (req.body[key] !== undefined) setSetting(key, req.body[key]);
  }
  res.json({ ok: true });
});

// Unknown entity fallback (generic CRUD let it pass via next('route')).
router.all(/.*/, (req, res) => res.status(404).json({ error: 'entity نامعتبر' }));

module.exports = router;
