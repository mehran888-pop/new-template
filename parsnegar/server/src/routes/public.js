'use strict';
/** Public content + theme config. */
const express = require('express');
const { get, all, getSetting } = require('../db');

const router = express.Router();

/** Theme/site config consumed by the React client (like the WP theme options). */
router.get('/meta', (req, res) => {
  res.json({
    site: getSetting('site', { name: 'هوش یار پارسی نگر', tagline: 'دستیار هوشمند کسب‌وکار شما' }),
    theme: getSetting('theme', {}),
    contact: getSetting('contact', {}),
    socials: getSetting('socials', []),
    otp: { demo: getSetting('otp.demo', true) },
  });
});

router.get('/services', (req, res) => {
  const services = all('SELECT * FROM services WHERE is_active = 1 ORDER BY menu_order, id');
  for (const s of services) {
    s.features = all('SELECT title, description FROM service_features WHERE service_id = ? ORDER BY sort', [s.id]);
  }
  res.json(services);
});

router.get('/projects', (req, res) => {
  const projects = all('SELECT * FROM projects ORDER BY featured DESC, menu_order, id');
  for (const p of projects) {
    p.highlights = all('SELECT text FROM project_highlights WHERE project_id = ? ORDER BY sort', [p.id]).map((r) => r.text);
  }
  res.json(projects);
});

router.get('/team', (req, res) => res.json(all('SELECT * FROM team_members ORDER BY sort, id')));

router.get('/posts', (req, res) => {
  const rows = all(
    `SELECT p.id, p.title, p.slug, p.excerpt, p.published_at, p.cover_media_id, c.name AS category
     FROM posts p LEFT JOIN categories c ON c.id = p.category_id
     WHERE p.status = 'published' ORDER BY p.published_at DESC`
  );
  res.json(rows);
});

router.get('/posts/:slug', (req, res) => {
  const post = get(
    `SELECT p.*, c.name AS category FROM posts p LEFT JOIN categories c ON c.id = p.category_id
     WHERE p.slug = ? AND p.status = 'published'`,
    [req.params.slug]
  );
  if (!post) return res.status(404).json({ error: 'پست یافت نشد' });
  res.json(post);
});

router.get('/products', (req, res) =>
  res.json(all(`SELECT * FROM products WHERE status = 'publish' ORDER BY id DESC`))
);

router.get('/products/:slug', (req, res) => {
  const product = get(`SELECT * FROM products WHERE slug = ? AND status = 'publish'`, [req.params.slug]);
  if (!product) return res.status(404).json({ error: 'محصول یافت نشد' });
  res.json(product);
});

router.get('/jobs', (req, res) =>
  res.json(all(`SELECT id, title, department, job_type, location, description, min_score, created_at FROM jobs WHERE status = 'open' ORDER BY id DESC`))
);

router.get('/jobs/:id', (req, res) => {
  const job = get(`SELECT * FROM jobs WHERE id = ? AND status = 'open'`, [req.params.id]);
  if (!job) return res.status(404).json({ error: 'موقعیت شغلی یافت نشد' });
  job.criteria = all('SELECT label, weight FROM job_criteria WHERE job_id = ? ORDER BY sort', [job.id]);
  res.json(job);
});

router.get('/rewards', (req, res) =>
  res.json(all('SELECT id, title, cost, kind, value FROM loyalty_rewards WHERE is_active = 1 ORDER BY cost'))
);

module.exports = router;
