'use strict';
/** Customer panel: profile, orders, invoices, tickets, loyalty, consultations. */
const express = require('express');
const crypto = require('crypto');
const { get, all, run, addPoints, getSetting, tx } = require('../db');
const { requireAuth } = require('../middleware/auth');
const { emit } = require('../lib/events');

const router = express.Router();
router.use(requireAuth);

const token = () => crypto.randomBytes(16).toString('hex');
const invoiceNumber = () => 'PN-' + (1000 + (get('SELECT COUNT(*) AS c FROM invoices').c || 0) + 1);

/* ── Profile ── */
router.put('/profile', (req, res) => {
  const { name, email, company, birthday, national_code, city, address, bale_chat_id, telegram_chat_id } = req.body;
  if (name !== undefined) run('UPDATE users SET name = ?, updated_at = datetime(\'now\') WHERE id = ?', [String(name), req.user.id]);
  if (email !== undefined) run('UPDATE users SET email = ? WHERE id = ?', [String(email || '') || null, req.user.id]);
  run(
    `INSERT INTO user_profiles (user_id, company, birthday, national_code, city, address, bale_chat_id, telegram_chat_id, last_activity)
     VALUES (?,?,?,?,?,?,?,?, datetime('now'))
     ON CONFLICT(user_id) DO UPDATE SET
       company = excluded.company, birthday = excluded.birthday, national_code = excluded.national_code,
       city = excluded.city, address = excluded.address, bale_chat_id = excluded.bale_chat_id,
       telegram_chat_id = excluded.telegram_chat_id, last_activity = datetime('now')`,
    [
      req.user.id,
      String(company ?? get('SELECT company FROM user_profiles WHERE user_id = ?', [req.user.id])?.company ?? ''),
      String(birthday ?? ''),
      String(national_code ?? ''),
      String(city ?? ''),
      String(address ?? ''),
      String(bale_chat_id ?? ''),
      String(telegram_chat_id ?? ''),
    ]
  );
  res.json({ ok: true });
});

/* ── Services of interest (CRM tracking) ── */
router.get('/services', (req, res) =>
  res.json(all(
    `SELECT us.id, us.status, us.created_at, s.title, s.icon_text
     FROM user_services us JOIN services s ON s.id = us.service_id WHERE us.user_id = ?`,
    [req.user.id]
  ))
);
router.post('/services', (req, res) => {
  const serviceId = Number(req.body.service_id);
  const status = ['interested', 'purchased', 'active'].includes(req.body.status) ? req.body.status : 'interested';
  if (!serviceId) return res.status(400).json({ error: 'service_id لازم است' });
  run('INSERT INTO user_services (user_id, service_id, status) VALUES (?,?,?) ON CONFLICT (user_id, service_id) DO UPDATE SET status = excluded.status',
    [req.user.id, serviceId, status]);
  res.json({ ok: true });
});

/* ── Orders (purchases) ── */
router.get('/orders', (req, res) =>
  res.json(all('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC', [req.user.id]))
);
router.get('/orders/:id', (req, res) => {
  const order = get('SELECT * FROM orders WHERE id = ? AND user_id = ?', [req.params.id, req.user.id]);
  if (!order) return res.status(404).json({ error: 'سفارش یافت نشد' });
  order.items = all('SELECT * FROM order_items WHERE order_id = ?', [order.id]);
  res.json(order);
});

/* ── Checkout: cart [{product_id, qty}] → order + invoice (+loyalty earn) ── */
router.post('/checkout', async (req, res) => {
  const cart = Array.isArray(req.body.cart) ? req.body.cart : [];
  if (!cart.length) return res.status(400).json({ error: 'سبد خالی است' });
  const usePoints = Math.max(0, Number(req.body.use_points || 0));

  try {
    const out = tx(() => {
      let subtotal = 0;
      const lines = [];
      for (const line of cart) {
        const p = get(`SELECT * FROM products WHERE id = ? AND status = 'publish'`, [Number(line.product_id)]);
        if (!p) throw new Error('محصول ناموجود: ' + line.product_id);
        const qty = Math.max(1, Number(line.qty || 1));
        const price = p.sale_price != null ? p.sale_price : p.price;
        lines.push({ p, qty, price, line_total: price * qty });
        subtotal += price * qty;
      }
      let discount = 0;
      if (usePoints > 0) {
        const bal = get('SELECT points_balance FROM users WHERE id = ?', [req.user.id]).points_balance;
        const spend = Math.min(usePoints, bal);
        discount = spend * 100; // هر امتیاز = ۱۰۰ تومان
        addPoints(req.user.id, -spend, 'redeem', 'تخفیف خرید', 'orders', null);
      }
      const total = Math.max(0, subtotal - discount);
      const order = run('INSERT INTO orders (user_id, subtotal, discount, total) VALUES (?,?,?,?)',
        [req.user.id, subtotal, discount, total]);
      for (const l of lines) {
        run('INSERT INTO order_items (order_id, product_id, title, qty, unit_price, line_total) VALUES (?,?,?,?,?,?)',
          [order.id, l.p.id, l.p.title, l.qty, l.price, l.line_total]);
        run('UPDATE products SET stock = stock - ? WHERE id = ?', [l.qty, l.p.id]);
      }
      const inv = run(
        `INSERT INTO invoices (number, user_id, token, status, title, subtotal, discount, total, due_at)
         VALUES (?,?,?,?,?,?,?,?, datetime('now', '+7 days'))`,
        [invoiceNumber(), req.user.id, token(), 'unpaid', 'فاکتور سفارش #' + order.id, subtotal, discount, total]
      );
      for (const l of lines) {
        run('INSERT INTO invoice_items (invoice_id, title, qty, unit_price, line_total) VALUES (?,?,?,?,?)',
          [inv.id, l.p.title, l.qty, l.price, l.line_total]);
      }
      return { orderId: order.id, invoiceId: inv.id, invoiceToken: get('SELECT token FROM invoices WHERE id = ?', [inv.id]).token, total };
    });

    emit('order_created', { user_id: req.user.id, name: req.user.name, phone: req.user.phone, amount: out.total, order_id: out.orderId });
    res.status(201).json(out);
  } catch (e) {
    res.status(400).json({ error: e.message || String(e) });
  }
});

/* ── Pay invoice (wallet/gateway sim) → order paid + earn points ── */
router.post('/invoices/:token/pay', async (req, res) => {
 try {
  const inv = get('SELECT * FROM invoices WHERE token = ? AND user_id = ?', [req.params.token, req.user.id]);
  if (!inv) return res.status(404).json({ error: 'فاکتور یافت نشد' });
  if (inv.status === 'paid') return res.status(400).json({ error: 'قبلاً پرداخت شده' });
  const gateway = ['zarinpal', 'idpay', 'wallet'].includes(req.body.gateway) ? req.body.gateway : 'wallet';
  const ref = 'REF-' + Date.now().toString(36).toUpperCase();

  tx(() => {
    run(`UPDATE invoices SET status = 'paid', paid_at = datetime('now'), gateway = ?, gateway_ref = ? WHERE id = ?`, [gateway, ref, inv.id]);
    run(`INSERT INTO payments (user_id, invoice_id, gateway, amount, ref_id, status) VALUES (?,?,?,?,?, 'ok')`,
      [req.user.id, inv.id, gateway, inv.total, ref]);
    // If invoice backs an order → mark it paid.
    const m = String(inv.title || '').match(/#(\d+)/);
    if (m) {
      const order = get('SELECT * FROM orders WHERE id = ? AND user_id = ?', [Number(m[1]), req.user.id]);
      if (order) {
        run(`UPDATE orders SET status = 'paid', paid_at = datetime('now') WHERE id = ?`, [order.id]);
        emit('order_paid', { user_id: req.user.id, name: req.user.name, phone: req.user.phone, amount: order.total, title: 'سفارش #' + order.id });
      }
    }
    const earn = Math.floor(inv.total / 10000); // هر ۱۰ هزار تومان = ۱ امتیاز
    if (earn > 0) addPoints(req.user.id, earn, 'earn', 'امتیاز خرید فاکتور ' + inv.number, 'invoices', inv.id);
  });

  emit('invoice_paid', {
    user_id: req.user.id, name: req.user.name, phone: req.user.phone,
    amount: inv.total, invoice: inv.number, title: inv.title,
    balance: get('SELECT points_balance FROM users WHERE id = ?', [req.user.id]).points_balance,
  });
  res.json({ ok: true, ref });
 } catch (e) {
  console.error('invoice pay error:', e);
  res.status(500).json({ error: 'خطای داخلی در پرداخت' });
 }
});

/* ── Invoices (finance) ── */
router.get('/invoices', (req, res) =>
  res.json(all('SELECT id, number, title, status, total, created_at, paid_at, token FROM invoices WHERE user_id = ? ORDER BY id DESC', [req.user.id]))
);
router.get('/invoices/:token', (req, res) => {
  const inv = get('SELECT * FROM invoices WHERE token = ? AND user_id = ?', [req.params.token, req.user.id]);
  if (!inv) return res.status(404).json({ error: 'فاکتور یافت نشد' });
  inv.items = all('SELECT * FROM invoice_items WHERE invoice_id = ?', [inv.id]);
  res.json(inv);
});

/** Public invoice by token (payment link). */
router.get('/pay/:token', (req, res) => {
  const inv = get('SELECT number, title, status, total, due_at, created_at FROM invoices WHERE token = ?', [req.params.token]);
  if (!inv) return res.status(404).json({ error: 'لینک نامعتبر' });
  res.json(inv);
});

/* ── Tickets ── */
router.get('/tickets', (req, res) =>
  res.json(all('SELECT * FROM tickets WHERE user_id = ? ORDER BY updated_at DESC', [req.user.id]))
);
router.get('/tickets/:id', (req, res) => {
  const t = get('SELECT * FROM tickets WHERE id = ? AND user_id = ?', [req.params.id, req.user.id]);
  if (!t) return res.status(404).json({ error: 'تیکت یافت نشد' });
  t.messages = all('SELECT tm.*, u.name FROM ticket_messages tm JOIN users u ON u.id = tm.user_id WHERE tm.ticket_id = ? ORDER BY tm.created_at', [t.id]);
  res.json(t);
});
router.post('/tickets', (req, res) => {
  const subject = String(req.body.subject || '').trim();
  const body = String(req.body.body || '').trim();
  if (!subject || !body) return res.status(400).json({ error: 'موضوع و متن لازم است' });
  const dept = ['general', 'sales', 'support', 'finance'].includes(req.body.department) ? req.body.department : 'general';
  const t = run('INSERT INTO tickets (user_id, subject, department) VALUES (?,?,?)', [req.user.id, subject, dept]);
  run('INSERT INTO ticket_messages (ticket_id, user_id, body) VALUES (?,?,?)', [t.id, req.user.id, body]);
  emit('ticket_created', { user_id: req.user.id, name: req.user.name, phone: req.user.phone, title: subject });
  res.status(201).json({ id: t.id });
});
router.post('/tickets/:id/messages', (req, res) => {
  const t = get('SELECT * FROM tickets WHERE id = ? AND user_id = ?', [req.params.id, req.user.id]);
  if (!t) return res.status(404).json({ error: 'تیکت یافت نشد' });
  const body = String(req.body.body || '').trim();
  if (!body) return res.status(400).json({ error: 'متن پیام لازم است' });
  run('INSERT INTO ticket_messages (ticket_id, user_id, body) VALUES (?,?,?)', [t.id, req.user.id, body]);
  run(`UPDATE tickets SET status = 'open', updated_at = datetime('now') WHERE id = ?`, [t.id]);
  res.json({ ok: true });
});

/* ── Consultation / support requests ── */
router.get('/consultations', (req, res) =>
  res.json(all('SELECT * FROM consultations WHERE user_id = ? ORDER BY id DESC', [req.user.id]))
);
router.post('/consultations', (req, res) => {
  const subject = String(req.body.subject || '').trim();
  const body = String(req.body.body || '').trim();
  const type = ['consult', 'support', 'quote'].includes(req.body.type) ? req.body.type : 'consult';
  if (!subject) return res.status(400).json({ error: 'موضوع لازم است' });
  const ins = run('INSERT INTO consultations (user_id, type, subject, body) VALUES (?,?,?,?)', [req.user.id, type, subject, body]);
  emit('consult_created', { user_id: req.user.id, name: req.user.name, phone: req.user.phone, title: subject });
  res.status(201).json({ id: ins.id });
});

/* ── Loyalty club ── */
router.get('/loyalty', (req, res) => {
  const u = get('SELECT points_balance FROM users WHERE id = ?', [req.user.id]);
  const txs = all('SELECT * FROM loyalty_transactions WHERE user_id = ? ORDER BY id DESC LIMIT 50', [req.user.id]);
  const rewards = all('SELECT id, title, cost, kind, value FROM loyalty_rewards WHERE is_active = 1 ORDER BY cost');
  res.json({ balance: u.points_balance, transactions: txs, rewards });
});
router.post('/loyalty/redeem', (req, res) => {
  const reward = get('SELECT * FROM loyalty_rewards WHERE id = ? AND is_active = 1', [Number(req.body.reward_id)]);
  if (!reward) return res.status(404).json({ error: 'پاداش یافت نشد' });
  const u = get('SELECT points_balance, name, phone FROM users WHERE id = ?', [req.user.id]);
  if (u.points_balance < reward.cost) return res.status(400).json({ error: 'امتیاز کافی نیست' });
  addPoints(req.user.id, -reward.cost, 'redeem', 'دریافت پاداش: ' + reward.title, 'loyalty_rewards', reward.id);
  emit('loyalty_redeemed', { user_id: req.user.id, name: u.name, phone: u.phone, title: reward.title, balance: u.points_balance - reward.cost });
  res.json({ ok: true, balance: u.points_balance - reward.cost });
});

/* ── Track chosen services ── */
router.get('/segments', (req, res) =>
  res.json(all(`SELECT s.id, s.title, s.color, us.created_at FROM user_segments us JOIN segments s ON s.id = us.segment_id WHERE us.user_id = ?`, [req.user.id]))
);

module.exports = router;
