'use strict';
/**
 * هوش یار پارسی نگر — DB layer (node:sqlite, zero native deps)
 */
const fs = require('fs');
const path = require('path');
const { DatabaseSync } = require('node:sqlite');

const DB_FILE = process.env.PARSNEGAR_DB || path.join(__dirname, '..', '..', 'data', 'parsnegar.db');
fs.mkdirSync(path.dirname(DB_FILE), { recursive: true });

const db = new DatabaseSync(DB_FILE);
db.exec('PRAGMA journal_mode = WAL');
db.exec('PRAGMA foreign_keys = ON');

// Schema
const schema = fs.readFileSync(path.join(__dirname, 'schema.sql'), 'utf8');
db.exec(schema);

/** Tiny query helpers */
function get(sql, params = []) {
  return db.prepare(sql).get(...params);
}
function all(sql, params = []) {
  return db.prepare(sql).all(...params);
}
function run(sql, params = []) {
  const info = db.prepare(sql).run(...params);
  return { id: Number(info.lastInsertRowid), changes: info.changes };
}
let txDepth = 0;
function tx(fn) {
  if (txDepth === 0) db.exec('BEGIN');
  txDepth++;
  try {
    const out = fn();
    txDepth--;
    if (txDepth === 0) db.exec('COMMIT');
    return out;
  } catch (e) {
    txDepth--;
    if (txDepth === 0) db.exec('ROLLBACK');
    throw e;
  }
}

/* ── Settings helpers ── */
function getSetting(key, fallback = null) {
  const row = get('SELECT value_json FROM settings WHERE key = ?', [key]);
  if (!row) return fallback;
  try { return JSON.parse(row.value_json); } catch { return fallback; }
}
function setSetting(key, value) {
  run(
    `INSERT INTO settings (key, value_json, updated_at) VALUES (?, ?, datetime('now'))
     ON CONFLICT(key) DO UPDATE SET value_json = excluded.value_json, updated_at = datetime('now')`,
    [key, JSON.stringify(value)]
  );
}

/* ── Loyalty helpers ── */
function addPoints(userId, delta, type, description, refTable = '', refId = null) {
  return tx(() => {
    const u = get('SELECT points_balance FROM users WHERE id = ?', [userId]);
    if (!u) throw new Error('user not found');
    const after = Math.max(0, (u.points_balance || 0) + delta);
    run('UPDATE users SET points_balance = ? WHERE id = ?', [after, userId]);
    return run(
      `INSERT INTO loyalty_transactions (user_id, delta, balance_after, type, ref_table, ref_id, description)
       VALUES (?,?,?,?,?,?,?)`,
      [userId, delta, after, type, refTable, refId, description]
    );
  });
}

module.exports = { db, get, all, run, tx, getSetting, setSetting, addPoints, DB_FILE };
