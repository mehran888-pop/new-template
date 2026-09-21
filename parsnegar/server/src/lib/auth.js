'use strict';
/**
 * Auth utils — scrypt password hashing, secure tokens (no external deps).
 */
const crypto = require('crypto');

const scrypt = (password, salt) =>
  crypto.scryptSync(String(password), salt, 32).toString('hex');

function hashPassword(password) {
  const salt = crypto.randomBytes(8).toString('hex');
  return salt + ':' + scrypt(password, salt);
}
function verifyPassword(password, stored) {
  if (!stored || !stored.includes(':')) return false;
  const [salt, hash] = stored.split(':');
  const candidate = scrypt(password, salt);
  const a = Buffer.from(hash, 'hex');
  const b = Buffer.from(candidate, 'hex');
  return a.length === b.length && crypto.timingSafeEqual(a, b);
}

function newToken() {
  return 'pn_' + crypto.randomBytes(24).toString('hex');
}
const hashToken = (token) => crypto.createHash('sha256').update(token).digest('hex');

function randomCode(digits = 5) {
  let out = '';
  for (let i = 0; i < digits; i++) out += Math.floor(Math.random() * 10);
  return out;
}

module.exports = { hashPassword, verifyPassword, newToken, hashToken, randomCode };
