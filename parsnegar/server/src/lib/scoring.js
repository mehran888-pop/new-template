'use strict';
/**
 * Automatic recruitment scoring — weighted criteria × keyword hits.
 * score = Σ weight_i × hitRatio_i  normalized to 0..100
 */
function scoreApplication(jobId, answers, dbAll) {
  const criteria = dbAll('SELECT * FROM job_criteria WHERE job_id = ? ORDER BY sort', [jobId]);
  const haystack = Object.values(answers || {})
    .map((v) => (typeof v === 'object' ? JSON.stringify(v) : String(v)))
    .join(' \n ')
    .toLowerCase();

  if (!criteria.length) return { score: 0, details: [] };

  let totalWeight = 0;
  let got = 0;
  const details = [];

  for (const c of criteria) {
    const weight = Number(c.weight) || 1;
    totalWeight += weight;
    const keywords = String(c.keywords || '')
      .split(',')
      .map((k) => k.trim().toLowerCase())
      .filter(Boolean);
    let ratio = 0;
    if (keywords.length) {
      const hits = keywords.filter((k) => haystack.includes(k)).length;
      ratio = hits / keywords.length;
    } else {
      ratio = haystack.length > 20 ? 1 : 0; // بدون کلیدواژه: فقط وجود پاسخ
    }
    got += weight * ratio;
    details.push({ label: c.label, weight, ratio: Math.round(ratio * 100) / 100, points: Math.round(weight * ratio * 100) / 100 });
  }

  const score = totalWeight ? Math.round((got / totalWeight) * 1000) / 10 : 0;
  return { score, details };
}

module.exports = { scoreApplication };
