'use strict';
/** Auth middleware — Bearer token sessions + role guards. */
const { get } = require('../db');
const { hashToken } = require('../lib/auth');

function currentUser(req) {
  const header = req.headers.authorization || '';
  const token = header.startsWith('Bearer ') ? header.slice(7) : (req.query.token || '');
  if (!token) return null;
  const row = get(
    `SELECT s.user_id, u.* FROM sessions s JOIN users u ON u.id = s.user_id
     WHERE s.token_hash = ? AND s.expires_at > datetime('now')`,
    [hashToken(String(token))]
  );
  return row || null;
}

function requireAuth(req, res, next) {
  const user = currentUser(req);
  if (!user) return res.status(401).json({ error: 'وارد نشده‌اید' });
  req.user = user;
  next();
}

function requireAdmin(req, res, next) {
  const user = currentUser(req);
  if (!user) return res.status(401).json({ error: 'وارد نشده‌اید' });
  if (user.role !== 'admin' && user.role !== 'crm') return res.status(403).json({ error: 'دسترسی غیرمجاز' });
  req.user = user;
  next();
}

module.exports = { currentUser, requireAuth, requireAdmin };
