'use strict';
/** Careers: multi-step applications + automatic scoring. */
const express = require('express');
const { get, all, run } = require('../db');
const { scoreApplication } = require('../lib/scoring');
const { emit } = require('../lib/events');

const router = express.Router();

/** POST /api/applications — payload: { job_id, full_name, phone, email, answers: {...} } */
router.post('/', async (req, res) => {
  const jobId = Number(req.body.job_id);
  const job = get(`SELECT * FROM jobs WHERE id = ? AND status = 'open'`, [jobId]);
  if (!job) return res.status(404).json({ error: 'موقعیت شغلی یافت نشد' });

  const full_name = String(req.body.full_name || '').trim();
  const phone = String(req.body.phone || '').replace(/\D/g, '');
  const email = String(req.body.email || '').trim();
  const answers = req.body.answers && typeof req.body.answers === 'object' ? req.body.answers : {};
  if (!full_name || !/^09\d{9}$/.test(phone)) return res.status(400).json({ error: 'نام و شماره معتبر لازم است' });

  const { score, details } = scoreApplication(jobId, answers, all);
  const stage = score >= Number(job.min_score || 70) ? 'video' : 'screening';

  const ins = run(
    `INSERT INTO applications (job_id, full_name, phone, email, answers_json, score, score_details_json, stage)
     VALUES (?,?,?,?,?,?,?,?)`,
    [jobId, full_name, phone, email, JSON.stringify(answers), score, JSON.stringify(details), stage]
  );

  emit('application_received', {
    name: full_name, phone, email, title: job.title,
    amount: score, // برای شرط امتیاز در اتوماسیون
    user_id: null,
  });

  res.status(201).json({
    id: ins.id,
    score,
    stage,
    details,
    pass: stage === 'video',
    message: stage === 'video'
      ? '🎉 تبریک! امتیاز شما از حد نصاب گذشت — به مرحله مصاحبه ویدیویی دعوت می‌شوید.'
      : 'درخواست شما ثبت شد و توسط تیم استخدام بررسی می‌شود.',
  });
});

module.exports = router;
