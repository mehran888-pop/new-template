/**
 * هوش یار پارسی نگر — API client (relative URLs; Vite/Express serve the API).
 */
const TOKEN_KEY = 'pn_token';

export const getToken = () => localStorage.getItem(TOKEN_KEY) || '';
export const setToken = (t) => (t ? localStorage.setItem(TOKEN_KEY, t) : localStorage.removeItem(TOKEN_KEY));

export async function api(path, { method = 'GET', body, raw } = {}) {
  const headers = { 'Content-Type': 'application/json' };
  const token = getToken();
  if (token) headers.Authorization = 'Bearer ' + token;
  const res = await fetch('/api' + path, {
    method,
    headers,
    body: body !== undefined ? JSON.stringify(body) : undefined,
  });
  if (raw) return res;
  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(data.error || 'خطای سرور (' + res.status + ')');
  return data;
}

export const faMoney = (n) => (Number(n) || 0).toLocaleString('fa-IR') + ' تومان';
export const faDate = (s) => {
  if (!s) return '—';
  try { return new Date(s.replace(' ', 'T') + (String(s).includes('T') || String(s).includes('Z') ? '' : 'Z')).toLocaleDateString('fa-IR', { year: 'numeric', month: 'long', day: 'numeric' }); }
  catch { return String(s).slice(0, 10); }
};
export const faStatus = {
  pending: 'در انتظار', paid: 'پرداخت‌شده', shipped: 'ارسال‌شده', completed: 'تکمیل‌شده', cancelled: 'لغوشده',
  draft: 'پیش‌نویس', unpaid: 'پرداخت‌نشده', expired: 'منقضی', open: 'باز', answered: 'پاسخ‌داده‌شده', closed: 'بسته',
  new: 'جدید', applied: 'ثبت‌شده', screening: 'غربالگری', video: 'مصاحبه ویدیویی', test: 'تست', interview: 'مصاحبه', hired: 'استخدام', rejected: 'ردشده',
  interested: 'علاقه‌مند', purchased: 'خریداری‌شده', active: 'فعال',
};
