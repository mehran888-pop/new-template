'use strict';
/**
 * Notification hub — sms.ir / ملی‌پیامک / Bale / Telegram / Email adapters.
 * Provider keys live in settings['integrations']; without keys messages go to
 * the outbox as "simulated" (auditable, professional).
 */
const { get, run, getSetting } = require('../db');

const CHANNELS = ['sms_ir', 'melipayamak', 'bale', 'telegram', 'email'];

function fill(template, vars) {
  return String(template).replace(/\{(\w+)\}/g, (_, k) => (vars[k] != null ? String(vars[k]) : ''));
}

/* ── Adapters (real HTTP when keys exist) ── */
async function sendSmsIr(to, message) {
  const cfg = getSetting('integrations.sms_ir', {});
  if (!cfg.api_key) return { simulated: true };
  const r = await fetch('https://api.sms.ir/v1/send/verify', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'x-api-key': cfg.api_key },
    body: JSON.stringify({ mobile: to, templateId: Number(cfg.otp_template_id || 0), parameters: [{ name: 'code', value: message }] }),
  }).catch(() => null);
  return r && r.ok ? { ref: 'sms_ir' } : { error: 'sms_ir failed' };
}

async function sendMeliPayamak(to, message) {
  const cfg = getSetting('integrations.melipayamak', {});
  if (!cfg.username) return { simulated: true };
  const r = await fetch('https://api.payamak-panel.com/post/Send.ashx', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ username: cfg.username, password: cfg.password || '', from: cfg.from || '', to, text: message }),
  }).catch(() => null);
  return r && r.ok ? { ref: 'melipayamak' } : { error: 'melipayamak failed' };
}

async function sendBale(chatId, message) {
  const cfg = getSetting('integrations.bale', {});
  if (!cfg.token || !chatId) return { simulated: true };
  const r = await fetch(`https://api.bale.ai/bot${cfg.token}/sendMessage`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ chat_id: chatId, text: message }),
  }).catch(() => null);
  return r && r.ok ? { ref: 'bale' } : { error: 'bale failed' };
}

async function sendTelegram(chatId, message) {
  const cfg = getSetting('integrations.telegram', {});
  if (!cfg.token || !chatId) return { simulated: true };
  const r = await fetch(`https://api.telegram.org/bot${cfg.token}/sendMessage`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ chat_id: chatId, text: message }),
  }).catch(() => null);
  return r && r.ok ? { ref: 'telegram' } : { error: 'telegram failed' };
}

async function sendEmail(to, message) {
  const cfg = getSetting('integrations.email', {});
  if (!cfg.host) return { simulated: true }; // SMTP wiring point
  return { simulated: true };
}

/**
 * Queue + dispatch one message.
 * @returns outbox row id + dispatch result
 */
async function notify(channel, recipient, message) {
  const row = run(
    'INSERT INTO notification_outbox (channel, recipient, message, status) VALUES (?,?,?,?)',
    [channel, String(recipient || ''), message, 'pending']
  );
  let result = { simulated: true };
  try {
    if (channel === 'sms_ir') result = await sendSmsIr(recipient, message);
    else if (channel === 'melipayamak') result = await sendMeliPayamak(recipient, message);
    else if (channel === 'bale') result = await sendBale(recipient, message);
    else if (channel === 'telegram') result = await sendTelegram(recipient, message);
    else if (channel === 'email') result = await sendEmail(recipient, message);
  } catch (e) {
    result = { error: String(e.message || e) };
  }
  const ok = !result.error;
  run('UPDATE notification_outbox SET status = ?, provider_ref = ?, error = ?, sent_at = datetime(\'now\') WHERE id = ?', [
    ok ? 'sent' : 'failed',
    result.ref || (result.simulated ? 'simulated' : ''),
    result.error || '',
    row.id,
  ]);
  return { id: row.id, result };
}

/** Notify a user on all their bound channels (bale/telegram first, then SMS). */
async function notifyUser(user, message) {
  const out = [];
  const profile = get('SELECT bale_chat_id, telegram_chat_id FROM user_profiles WHERE user_id = ?', [user.id]) || {};
  if (profile.bale_chat_id) out.push(await notify('bale', profile.bale_chat_id, message));
  if (profile.telegram_chat_id) out.push(await notify('telegram', profile.telegram_chat_id, message));
  out.push(await notify(getSetting('sms_provider', 'sms_ir'), user.phone, message));
  return out;
}

module.exports = { CHANNELS, notify, notifyUser, fill };
