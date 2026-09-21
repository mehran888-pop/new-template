'use strict';
/** Auth: OTP (sms) + password login/register. */
const express = require('express');
const { get, run, addPoints, getSetting, setSetting } = require('../db');
const { hashPassword, verifyPassword, newToken, hashToken, randomCode } = require('../lib/auth');
const { notify } = require('../lib/notify');
const { emit } = require('../lib/events');
const { requireAuth } = require('../middleware/auth');

const router = express.Router();
const OTP_TTL_MIN = 2;

function makeSession(userId, req) {
  const token = newToken();
  run(
    `INSERT INTO sessions (user_id, token_hash, user_agent, expires_at)
     VALUES (?,?,?, datetime('now', '+30 days'))`,
    [userId, hashToken(token), String(req.headers['user-agent'] || '').slice(0, 180)]
  );
  return token;
}

function publicUser(u) {
  if (!u) return null;
  return { id: u.id, name: u.name, phone: u.phone, email: u.email, role: u.role, points_balance: u.points_balance };
}

/** POST /api/auth/otp/send */
router.post('/otp/send', async (req, res) => {
 try {
  const phone = String(req.body.phone || '').replace(/\D/g, '');
  if (!/^09\d{9}$/.test(phone)) return res.status(400).json({ error: 'شماره موبایل معتبر نیست (مثال: ۰۹۱۲۱۲۳۴۵۶۷)' });
  const code = randomCode(5);
  const expiresAt = new Date(Date.now() + OTP_TTL_MIN * 60000).toISOString();
  run(
    `INSERT INTO otp_codes (phone, code, channel, expires_at, request_ip) VALUES (?,?,?,?,?)`,
    [phone, code, getSetting('otp.channel', 'sms_ir'), expiresAt, String(req.ip || '')]
  );

  const result = await notify(getSetting('sms_provider', 'sms_ir'), phone, `کد تایید پارسی‌نگر: ${code}`);
  const payload = { ok: true, channel: getSetting('sms_provider', 'sms_ir'), sent: result };
  // Demo mode: return the code so the preview is usable without a real gateway.
  if (getSetting('otp.demo', true)) payload.demo_code = code;
  res.json(payload);
 } catch (e) {
  console.error('otp/send error:', e);
  res.status(500).json({ error: 'خطای داخلی در ارسال کد' });
 }
});

/** POST /api/auth/otp/verify → token (+ register if new) */
router.post('/otp/verify', (req, res) => {
  const phone = String(req.body.phone || '').replace(/\D/g, '');
  const code = String(req.body.code || '').trim();
  const row = get(
    `SELECT * FROM otp_codes WHERE phone = ? AND used_at IS NULL AND expires_at > datetime('now') ORDER BY id DESC LIMIT 1`,
    [phone]
  );
  if (!row) return res.status(400).json({ error: 'کد منقضی شده است؛ دوباره درخواست دهید' });
  if (row.attempts >= 5) return res.status(429).json({ error: 'تعداد تلاش بیش از حد مجاز' });
  run('UPDATE otp_codes SET attempts = attempts + 1 WHERE id = ?', [row.id]);
  if (row.code !== code) return res.status(400).json({ error: 'کد نادرست است' });
  run('UPDATE otp_codes SET used_at = datetime(\'now\') WHERE id = ?', [row.id]);

  let user = get('SELECT * FROM users WHERE phone = ?', [phone]);
  if (!user) {
    const name = String(req.body.name || 'کاربر پارسی‌نگر');
    const ins = run('INSERT INTO users (name, phone) VALUES (?,?)', [name, phone]);
    run('INSERT INTO user_profiles (user_id, phone_verified) VALUES (?, 1)', [ins.id]);
    user = get('SELECT * FROM users WHERE id = ?', [ins.id]);
    addPoints(ins.id, getSetting('loyalty.signup_bonus', 100), 'earn', 'هدیه ثبت‌نام', 'users', ins.id);
    emit('user_registered', { user_id: ins.id, name, phone });
  } else {
    run('UPDATE user_profiles SET phone_verified = 1, last_activity = datetime(\'now\') WHERE user_id = ?', [user.id]);
  }
  res.json({ token: makeSession(user.id, req), user: publicUser(user) });
});

/** POST /api/auth/register (password) */
router.post('/register', (req, res) => {
  const name = String(req.body.name || '').trim();
  const phone = String(req.body.phone || '').replace(/\D/g, '');
  const password = String(req.body.password || '');
  if (!/^09\d{9}$/.test(phone)) return res.status(400).json({ error: 'شماره موبایل معتبر نیست' });
  if (password.length < 6) return res.status(400).json({ error: 'رمز عبور حداقل ۶ کاراکتر' });
  if (get('SELECT id FROM users WHERE phone = ?', [phone])) return res.status(409).json({ error: 'این شماره قبلاً ثبت شده است' });
  const ins = run('INSERT INTO users (name, phone, password_hash) VALUES (?,?,?)', [name, phone, hashPassword(password)]);
  run('INSERT INTO user_profiles (user_id) VALUES (?)', [ins.id]);
  addPoints(ins.id, getSetting('loyalty.signup_bonus', 100), 'earn', 'هدیه ثبت‌نام', 'users', ins.id);
  const user = get('SELECT * FROM users WHERE id = ?', [ins.id]);
  emit('user_registered', { user_id: ins.id, name, phone });
  res.status(201).json({ token: makeSession(ins.id, req), user: publicUser(user) });
});

/** POST /api/auth/login (password) */
router.post('/login', (req, res) => {
  const phone = String(req.body.phone || '').replace(/\D/g, '');
  const password = String(req.body.password || '');
  const user = get('SELECT * FROM users WHERE phone = ?', [phone]);
  if (!user || !verifyPassword(password, user.password_hash)) {
    return res.status(401).json({ error: 'شماره یا رمز عبور نادرست است' });
  }
  if (user.status === 'blocked') return res.status(403).json({ error: 'حساب شما مسدود است' });
  res.json({ token: makeSession(user.id, req), user: publicUser(user) });
});

/** GET /api/auth/me */
router.get('/me', requireAuth, (req, res) => {
  const profile = get('SELECT * FROM user_profiles WHERE user_id = ?', [req.user.id]) || {};
  res.json({ user: publicUser(req.user), profile });
});

/** POST /api/auth/logout */
router.post('/logout', requireAuth, (req, res) => {
  const header = String(req.headers.authorization || '');
  const token = header.startsWith('Bearer ') ? header.slice(7) : '';
  if (token) run('DELETE FROM sessions WHERE token_hash = ?', [hashToken(token)]);
  res.json({ ok: true });
});

module.exports = { router, publicUser, makeSession };
