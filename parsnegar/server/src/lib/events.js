'use strict';
/**
 * Internal automation engine — evaluates rules per event
 * (user_registered|order_paid|invoice_paid|ticket_created|application_received|loyalty_redeemed)
 * and runs actions: notify channels / add_segment / add_points.
 */
const { get, all, run, addPoints, getSetting } = require('../db');
const { notify, fill } = require('./notify');

function matchCondition(rule, payload) {
  if (!rule.condition_field) return true;
  const actual = payload[rule.condition_field];
  const expected = rule.condition_value;
  const numA = Number(actual);
  const numE = Number(expected);
  switch (rule.condition_op) {
    case 'gte': return Number.isFinite(numA) && numA >= numE;
    case 'lte': return Number.isFinite(numA) && numA <= numE;
    case 'eq': return String(actual) === String(expected);
    case 'contains': return String(actual || '').includes(String(expected || ''));
    default: return true;
  }
}

/**
 * Fire event → evaluate active rules. Never throws.
 */
async function emit(eventName, payload = {}) {
  const rules = all('SELECT * FROM automation_rules WHERE is_active = 1 AND event_name = ?', [eventName]);
  const results = [];
  for (const rule of rules) {
    try {
      if (!matchCondition(rule, payload)) continue;
      let result = 'matched';

      if (rule.channel === 'add_segment' && payload.user_id) {
        const segId = Number(rule.target);
        if (segId) {
          run(
            'INSERT OR IGNORE INTO user_segments (user_id, segment_id, rule_id) VALUES (?,?,?)',
            [payload.user_id, segId, rule.id]
          );
          result = 'segment:' + segId;
        }
      } else if (rule.channel === 'add_points' && payload.user_id) {
        const pts = Number(rule.target) || 0;
        if (pts) {
          addPoints(payload.user_id, pts, 'earn', 'اتوماسیون: ' + rule.title, 'automation_rules', rule.id);
          result = 'points:' + pts;
        }
      } else {
        // Notification channels: resolve recipient.
        let recipient = payload.phone || '';
        if (payload.user_id && !recipient) {
          const u = get('SELECT phone FROM users WHERE id = ?', [payload.user_id]);
          recipient = u ? u.phone : '';
        }
        if (!recipient && rule.channel === 'email') recipient = payload.email || '';
        const vars = {
          name: payload.name || '',
          phone: payload.phone || '',
          amount: payload.amount != null ? Number(payload.amount).toLocaleString('fa-IR') : '',
          invoice: payload.invoice || '',
          title: payload.title || '',
          balance: payload.balance != null ? String(payload.balance) : '',
          site: getSetting('site.name', 'هوش یار پارسی نگر'),
          ...payload,
        };
        const message = fill(rule.template, vars);
        const r = await notify(rule.channel, recipient, message);
        result = r.result && r.result.error ? r.result.error : 'notified';
      }

      run('INSERT INTO automation_logs (rule_id, event_name, payload_json, result) VALUES (?,?,?,?)',
        [rule.id, eventName, JSON.stringify(payload), result]);
      results.push({ rule: rule.id, result });
    } catch (e) {
      run('INSERT INTO automation_logs (rule_id, event_name, payload_json, result) VALUES (?,?,?,?)',
        [rule.id, eventName, JSON.stringify(payload), 'error: ' + (e.message || e)]);
    }
  }
  return results;
}

module.exports = { emit };
