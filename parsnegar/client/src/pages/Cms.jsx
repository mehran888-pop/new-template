import React, { useEffect, useState, useCallback } from 'react';
import { Routes, Route, NavLink, Navigate, Link } from 'react-router-dom';
import { api, getToken, faMoney, faDate, faStatus } from '../api';
import {
  Card, Btn, Field, Input, TextArea, Select, Toggle, Segmented, Slider, Color,
  Alert, Badge, Table, Modal, Repeater,
} from '../components/ui';
import { useApp } from '../App';

/** CMS اختصاصی هوش یار پارسی نگر — مدیریت محتوا، فروش، CRM، اتوماسیون و تنظیمات */
export default function Cms() {
  const { user } = useApp();
  if (!getToken()) return <Navigate to="/login" replace />;
  if (!user) return <div className="page center muted">در حال بارگذاری…</div>;
  if (user.role !== 'admin' && user.role !== 'crm') return <Navigate to="/panel" replace />;

  return (
    <div className="page">
      <div className="spread mb">
        <h2>🛠 CMS پارسی‌نگر</h2>
        <Badge kind="accent">مدیر: {user.name || user.phone}</Badge>
      </div>
      <div className="neo-shell">
        <nav className="neo-side neo-card" style={{ padding: 14 }}>
          <NavLink to="/cms" end>📊 داشبورد</NavLink>
          <NavLink to="/cms/services">🧩 خدمات</NavLink>
          <NavLink to="/cms/projects">📁 پروژه‌ها</NavLink>
          <NavLink to="/cms/team">👥 تیم</NavLink>
          <NavLink to="/cms/posts">📰 مطالب</NavLink>
          <NavLink to="/cms/products">🛍 محصولات</NavLink>
          <NavLink to="/cms/orders">🧾 سفارش‌ها</NavLink>
          <NavLink to="/cms/invoices">💳 فاکتورها</NavLink>
          <NavLink to="/cms/tickets">🎫 تیکت‌ها</NavLink>
          <NavLink to="/cms/consults">💬 مشاوره‌ها</NavLink>
          <NavLink to="/cms/applications">💼 متقاضیان</NavLink>
          <NavLink to="/cms/jobs">📌 موقعیت‌ها</NavLink>
          <NavLink to="/cms/users">👤 مشتریان / CRM</NavLink>
          <NavLink to="/cms/automation">⚡ اتوماسیون</NavLink>
          <NavLink to="/cms/outbox">📤 صندوق اعلان</NavLink>
          <NavLink to="/cms/settings">🎨 تنظیمات قالب</NavLink>
        </nav>
        <div>
          <Routes>
            <Route index element={<Dash />} />
            <Route path="services" element={<ServicesAdmin />} />
            <Route path="projects" element={<ProjectsAdmin />} />
            <Route path="team" element={<EntityCrud {...TEAM_CFG} />} />
            <Route path="posts" element={<EntityCrud {...POSTS_CFG} />} />
            <Route path="products" element={<EntityCrud {...PRODUCTS_CFG} />} />
            <Route path="jobs" element={<JobsAdmin />} />
            <Route path="orders" element={<OrdersAdmin />} />
            <Route path="invoices" element={<InvoicesAdmin />} />
            <Route path="tickets" element={<TicketsAdmin />} />
            <Route path="consults" element={<ConsultsAdmin />} />
            <Route path="applications" element={<ApplicationsAdmin />} />
            <Route path="users" element={<UsersAdmin />} />
            <Route path="automation" element={<EntityCrud {...RULES_CFG} />} />
            <Route path="outbox" element={<OutboxAdmin />} />
            <Route path="settings" element={<SettingsAdmin />} />
            <Route path="*" element={<Navigate to="/cms" replace />} />
          </Routes>
        </div>
      </div>
    </div>
  );
}

/* ══════════ Generic CRUD (card forms) ══════════ */
function EntityCrud({ entity, title, columns, fields, transformRow }) {
  const [rows, setRows] = useState([]);
  const [edit, setEdit] = useState(null);
  const [err, setErr] = useState('');

  const load = useCallback(() => {
    api('/cms/' + entity).then((r) => setRows(transformRow ? r.map(transformRow) : r)).catch((e) => setErr(e.message));
  }, [entity, transformRow]);
  useEffect(() => { load(); }, [load]);

  const save = async () => {
    setErr('');
    try {
      const body = {};
      for (const f of fields) body[f.key] = edit['_'] !== undefined && edit[f.key] === undefined ? '' : edit[f.key];
      // coerce number fields
      for (const f of fields) if (f.type === 'number' || f.type === 'toggle') body[f.key] = Number(body[f.key] || 0);
      if (edit.id) await api(`/cms/${entity}/${edit.id}`, { method: 'PUT', body });
      else await api('/cms/' + entity, { method: 'POST', body });
      setEdit(null);
      load();
    } catch (e) { setErr(e.message); }
  };
  const del = async (row) => {
    if (!confirm('حذف شود؟')) return;
    try { await api(`/cms/${entity}/${row.id}`, { method: 'DELETE' }); load(); } catch (e) { alert(e.message); }
  };

  return (
    <Card>
      <div className="spread mb">
        <h3 style={{ margin: 0 }}>{title}</h3>
        <Btn variant="primary" size="sm" onClick={() => setEdit({ _new: 1 })}>＋ افزودن</Btn>
      </div>
      <Alert kind="err">{err}</Alert>
      <Table columns={columns} rows={rows} renderActions={(row) => (
        <div className="row">
          <Btn size="sm" onClick={() => setEdit({ ...row })}>ویرایش</Btn>
          <Btn size="sm" variant="danger" onClick={() => del(row)}>✕</Btn>
        </div>
      )} />
      {edit && (
        <Modal title={edit.id ? 'ویرایش' : 'افزودن'} onClose={() => setEdit(null)}>
          <div className="neo-grid neo-grid--2">
            {fields.map((f) => (
              <Field key={f.key} label={f.label} hint={f.hint} wide={f.wide || f.type === 'textarea'}>
                {f.type === 'textarea' ? (
                  <TextArea value={edit[f.key] ?? ''} onChange={(e) => setEdit({ ...edit, [f.key]: e.target.value })} />
                ) : f.type === 'select' ? (
                  <Select options={f.options} value={edit[f.key] ?? f.default ?? ''} onChange={(e) => setEdit({ ...edit, [f.key]: e.target.value })} />
                ) : f.type === 'toggle' ? (
                  <Toggle value={Number(edit[f.key] ?? f.default ?? 1)} onChange={(v) => setEdit({ ...edit, [f.key]: v })} />
                ) : (
                  <Input className={f.ltr ? 'ltr' : ''} type={f.type === 'number' ? 'number' : 'text'} value={edit[f.key] ?? ''} onChange={(e) => setEdit({ ...edit, [f.key]: e.target.value })} />
                )}
              </Field>
            ))}
          </div>
          <Btn variant="primary" onClick={save}>💾 ذخیره</Btn>
        </Modal>
      )}
    </Card>
  );
}

/* ══════════ Dashboard ══════════ */
function Dash() {
  const [s, setS] = useState(null);
  useEffect(() => { api('/cms/x/stats').then(setS).catch(() => {}); }, []);
  if (!s) return <div className="center muted">در حال بارگذاری…</div>;
  const stats = [
    ['مشتریان', s.users], ['درآمد (تومان)', Number(s.revenue).toLocaleString('fa-IR')],
    ['سفارش‌ها', s.orders], ['فاکتورهای باز', s.unpaidInvoices],
    ['تیکت‌های باز', s.openTickets], ['متقاضیان', s.applications],
    ['مطالب', s.posts], ['محصولات', s.products],
    ['خدمات', s.services], ['پروژه‌ها', s.projects],
  ];
  return (
    <div className="neo-grid neo-grid--4">
      {stats.map(([label, value]) => <div key={label} className="neo-card neo-stat"><b>{value}</b><span>{label}</span></div>)}
    </div>
  );
}

/* ══════════ Services with feature repeater ══════════ */
function ServicesAdmin() {
  const [rows, setRows] = useState([]);
  const [edit, setEdit] = useState(null);
  const load = () => api('/cms/services').then(setRows).catch(() => {});
  useEffect(() => { load(); }, []);

  const open = async (row) => {
    const full = row ? await api('/cms/services/' + row.id) : { _new: 1, is_active: 1, features: [] };
    setEdit({ ...full, features: full.features || [] });
  };
  const save = async () => {
    const body = {
      title: edit.title, slug: edit.slug || ('s-' + Date.now()), icon_text: edit.icon_text,
      short_desc: edit.short_desc, body: edit.body, link: edit.link,
      menu_order: Number(edit.menu_order || 0), is_active: Number(edit.is_active ?? 1),
    };
    let id = edit.id;
    if (id) await api('/cms/services/' + id, { method: 'PUT', body });
    else { const r = await api('/cms/services', { method: 'POST', body }); id = r.id; }
    await api(`/cms/services/${id}/features`, { method: 'POST', body: { rows: edit.features } });
    setEdit(null);
    load();
  };
  const del = async (row) => {
    if (!confirm('حذف شود؟')) return;
    await api('/cms/services/' + row.id, { method: 'DELETE' });
    load();
  };

  return (
    <Card>
      <div className="spread mb">
        <h3 style={{ margin: 0 }}>🧩 خدمات (المان‌ها)</h3>
        <Btn variant="primary" size="sm" onClick={() => open(null)}>＋ خدمت جدید</Btn>
      </div>
      <Table
        columns={[
          { key: 'icon_text', label: 'آیکون' },
          { key: 'title', label: 'عنوان' },
          { key: 'short_desc', label: 'توضیح کوتاه', render: (r) => <span className="muted">{String(r.short_desc || '').slice(0, 40)}…</span> },
          { key: 'is_active', label: 'وضعیت', render: (r) => <Badge kind={r.is_active ? 'ok' : 'warn'}>{r.is_active ? 'فعال' : 'غیرفعال'}</Badge> },
        ]}
        rows={rows}
        renderActions={(row) => (
          <div className="row">
            <Btn size="sm" onClick={() => open(row)}>ویرایش</Btn>
            <Btn size="sm" variant="danger" onClick={() => del(row)}>✕</Btn>
          </div>
        )}
      />
      {edit && (
        <Modal title={edit.id ? 'ویرایش خدمت' : 'خدمت جدید'} onClose={() => setEdit(null)}>
          <div className="neo-grid neo-grid--2">
            <Field label="عنوان"><Input value={edit.title || ''} onChange={(e) => setEdit({ ...edit, title: e.target.value })} /></Field>
            <Field label="نامک (slug)"><Input className="ltr" value={edit.slug || ''} onChange={(e) => setEdit({ ...edit, slug: e.target.value })} /></Field>
            <Field label="آیکون (ایموجی)"><Input value={edit.icon_text || ''} onChange={(e) => setEdit({ ...edit, icon_text: e.target.value })} /></Field>
            <Field label="ترتیب نمایش"><Input type="number" value={edit.menu_order ?? 0} onChange={(e) => setEdit({ ...edit, menu_order: e.target.value })} /></Field>
            <Field label="توضیح کوتاه" wide><TextArea value={edit.short_desc || ''} onChange={(e) => setEdit({ ...edit, short_desc: e.target.value })} /></Field>
            <Field label="متن کامل" wide><TextArea value={edit.body || ''} onChange={(e) => setEdit({ ...edit, body: e.target.value })} /></Field>
            <Field label="لینک بیشتر"><Input className="ltr" value={edit.link || ''} onChange={(e) => setEdit({ ...edit, link: e.target.value })} /></Field>
            <Field label="نمایش در سایت"><Toggle value={Number(edit.is_active ?? 1)} onChange={(v) => setEdit({ ...edit, is_active: v })} /></Field>
          </div>
          <Field label="ویژگی‌های خدمت (ردیف‌های تکرارشونده)">
            <Repeater
              rows={edit.features}
              onChange={(rows) => setEdit({ ...edit, features: rows })}
              fields={[{ key: 'title', label: 'عنوان' }, { key: 'description', label: 'توضیح', wide: true }]}
              blank={{ title: '', description: '' }}
            />
          </Field>
          <Btn variant="primary" onClick={save}>💾 ذخیره</Btn>
        </Modal>
      )}
    </Card>
  );
}

/* ══════════ Projects with highlights ══════════ */
function ProjectsAdmin() {
  const [rows, setRows] = useState([]);
  const [edit, setEdit] = useState(null);
  const load = () => api('/cms/projects').then(setRows).catch(() => {});
  useEffect(() => { load(); }, []);
  const open = async (row) => {
    const full = row ? await api('/cms/projects/' + row.id) : { _new: 1, status: 'completed', featured: 0, highlights: [] };
    setEdit({ ...full, highlights: (full.highlights || []).map((h) => ({ text: h.text })) });
  };
  const save = async () => {
    const body = {
      title: edit.title, slug: edit.slug || ('p-' + Date.now()), client: edit.client, year: edit.year,
      project_url: edit.project_url, status: edit.status, featured: Number(edit.featured ?? 0),
      body: edit.body, excerpt: edit.excerpt, menu_order: Number(edit.menu_order || 0),
    };
    let id = edit.id;
    if (id) await api('/cms/projects/' + id, { method: 'PUT', body });
    else { const r = await api('/cms/projects', { method: 'POST', body }); id = r.id; }
    await api(`/cms/projects/${id}/highlights`, { method: 'POST', body: { rows: edit.highlights } });
    setEdit(null);
    load();
  };
  const del = async (row) => {
    if (!confirm('حذف شود؟')) return;
    await api('/cms/projects/' + row.id, { method: 'DELETE' });
    load();
  };
  return (
    <Card>
      <div className="spread mb">
        <h3 style={{ margin: 0 }}>📁 پروژه‌ها (المان‌ها)</h3>
        <Btn variant="primary" size="sm" onClick={() => open(null)}>＋ پروژه جدید</Btn>
      </div>
      <Table
        columns={[
          { key: 'title', label: 'عنوان' },
          { key: 'client', label: 'کارفرما' },
          { key: 'year', label: 'سال' },
          { key: 'status', label: 'وضعیت', render: (r) => <Badge kind={r.status === 'completed' ? 'ok' : 'accent'}>{faStatus[r.status]}</Badge> },
          { key: 'featured', label: 'صفحه اصلی', render: (r) => (r.featured ? '⭐' : '—') },
        ]}
        rows={rows}
        renderActions={(row) => (
          <div className="row">
            <Btn size="sm" onClick={() => open(row)}>ویرایش</Btn>
            <Btn size="sm" variant="danger" onClick={() => del(row)}>✕</Btn>
          </div>
        )}
      />
      {edit && (
        <Modal title={edit.id ? 'ویرایش پروژه' : 'پروژه جدید'} onClose={() => setEdit(null)}>
          <div className="neo-grid neo-grid--2">
            <Field label="عنوان"><Input value={edit.title || ''} onChange={(e) => setEdit({ ...edit, title: e.target.value })} /></Field>
            <Field label="نامک (slug)"><Input className="ltr" value={edit.slug || ''} onChange={(e) => setEdit({ ...edit, slug: e.target.value })} /></Field>
            <Field label="کارفرما / مشتری"><Input value={edit.client || ''} onChange={(e) => setEdit({ ...edit, client: e.target.value })} /></Field>
            <Field label="سال انجام"><Input value={edit.year || ''} onChange={(e) => setEdit({ ...edit, year: e.target.value })} placeholder="۱۴۰۳" /></Field>
            <Field label="لینک پروژه"><Input className="ltr" value={edit.project_url || ''} onChange={(e) => setEdit({ ...edit, project_url: e.target.value })} /></Field>
            <Field label="ترتیب"><Input type="number" value={edit.menu_order ?? 0} onChange={(e) => setEdit({ ...edit, menu_order: e.target.value })} /></Field>
            <Field label="وضعیت"><Segmented value={edit.status || 'completed'} onChange={(v) => setEdit({ ...edit, status: v })} options={{ completed: 'تکمیل‌شده', ongoing: 'در حال اجرا', showcase: 'نمونه‌کار' }} /></Field>
            <Field label="نمایش در صفحه اصلی"><Toggle value={Number(edit.featured ?? 0)} onChange={(v) => setEdit({ ...edit, featured: v })} labels={['بله', 'خیر']} /></Field>
            <Field label="خلاصه" wide><TextArea value={edit.excerpt || ''} onChange={(e) => setEdit({ ...edit, excerpt: e.target.value })} /></Field>
            <Field label="متن کامل" wide><TextArea value={edit.body || ''} onChange={(e) => setEdit({ ...edit, body: e.target.value })} /></Field>
          </div>
          <Field label="ویژگی‌های برجسته">
            <Repeater rows={edit.highlights} onChange={(rows) => setEdit({ ...edit, highlights: rows })} fields={[{ key: 'text', label: 'متن ویژگی', wide: true }]} blank={{ text: '' }} />
          </Field>
          <Btn variant="primary" onClick={save}>💾 ذخیره</Btn>
        </Modal>
      )}
    </Card>
  );
}

/* ══════════ Jobs with criteria ══════════ */
function JobsAdmin() {
  const [rows, setRows] = useState([]);
  const [edit, setEdit] = useState(null);
  const load = () => api('/cms/jobs').then(setRows).catch(() => {});
  useEffect(() => { load(); }, []);
  const open = async (row) => {
    const full = row ? await api('/cms/jobs/' + row.id) : { _new: 1, status: 'open', job_type: 'full-time', min_score: 70, criteria: [] };
    setEdit({ ...full, criteria: full.criteria || [] });
  };
  const save = async () => {
    const body = {
      title: edit.title, department: edit.department, job_type: edit.job_type, location: edit.location,
      description: edit.description, min_score: Number(edit.min_score || 70), status: edit.status,
    };
    let id = edit.id;
    if (id) await api('/cms/jobs/' + id, { method: 'PUT', body });
    else { const r = await api('/cms/jobs', { method: 'POST', body }); id = r.id; }
    await api(`/cms/jobs/${id}/criteria`, { method: 'POST', body: { rows: edit.criteria } });
    setEdit(null);
    load();
  };
  return (
    <Card>
      <div className="spread mb">
        <h3 style={{ margin: 0 }}>📌 موقعیت‌های شغلی</h3>
        <Btn variant="primary" size="sm" onClick={() => open(null)}>＋ موقعیت جدید</Btn>
      </div>
      <Table
        columns={[
          { key: 'title', label: 'عنوان' },
          { key: 'department', label: 'واحد' },
          { key: 'location', label: 'محل' },
          { key: 'min_score', label: 'حد نصاب' },
          { key: 'status', label: 'وضعیت', render: (r) => <Badge kind={r.status === 'open' ? 'ok' : ''}>{r.status === 'open' ? 'باز' : 'بسته'}</Badge> },
        ]}
        rows={rows}
        renderActions={(row) => <Btn size="sm" onClick={() => open(row)}>ویرایش + معیارها</Btn>}
      />
      {edit && (
        <Modal title={edit.id ? 'ویرایش موقعیت' : 'موقعیت جدید'} onClose={() => setEdit(null)}>
          <div className="neo-grid neo-grid--2">
            <Field label="عنوان شغل"><Input value={edit.title || ''} onChange={(e) => setEdit({ ...edit, title: e.target.value })} /></Field>
            <Field label="واحد"><Input value={edit.department || ''} onChange={(e) => setEdit({ ...edit, department: e.target.value })} /></Field>
            <Field label="نوع همکاری"><Segmented value={edit.job_type || 'full-time'} onChange={(v) => setEdit({ ...edit, job_type: v })} options={{ 'full-time': 'تمام‌وقت', 'part-time': 'پاره‌وقت', remote: 'دورکاری' }} /></Field>
            <Field label="محل"><Input value={edit.location || ''} onChange={(e) => setEdit({ ...edit, location: e.target.value })} /></Field>
            <Field label="حد نصاب امتیاز (ارسال خودکار به مصاحبه ویدیویی)" wide>
              <Slider min={0} max={100} value={Number(edit.min_score ?? 70)} onChange={(v) => setEdit({ ...edit, min_score: v })} unit="از ۱۰۰" />
            </Field>
            <Field label="شرح شغل" wide><TextArea value={edit.description || ''} onChange={(e) => setEdit({ ...edit, description: e.target.value })} /></Field>
          </div>
          <Field label="معیارهای امتیازدهی خودکار" hint="وزن × نسبت کلیدواژه‌های پیداشده در پاسخ‌ها">
            <Repeater
              rows={edit.criteria}
              onChange={(rows) => setEdit({ ...edit, criteria: rows })}
              fields={[
                { key: 'label', label: 'عنوان معیار' },
                { key: 'weight', label: 'وزن (۱-۵)' },
                { key: 'keywords', label: 'کلیدواژه‌ها با کاما', wide: true },
              ]}
              blank={{ label: '', weight: 1, keywords: '' }}
            />
          </Field>
          <Btn variant="primary" onClick={save}>💾 ذخیره</Btn>
        </Modal>
      )}
    </Card>
  );
}

/* ══════════ Configs for EntityCrud ══════════ */
const TEAM_CFG = {
  entity: 'team', title: '👥 تیم ما',
  columns: [
    { key: 'name', label: 'نام' }, { key: 'role_title', label: 'سمت' },
    { key: 'bio', label: 'بیو', render: (r) => <span className="muted">{String(r.bio || '').slice(0, 36)}…</span> },
  ],
  fields: [
    { key: 'name', label: 'نام' }, { key: 'role_title', label: 'سمت' },
    { key: 'bio', label: 'بیوگرافی', type: 'textarea', wide: true },
    { key: 'sort', label: 'ترتیب', type: 'number' },
  ],
  transformRow: (r) => ({ ...r, socials_json: r.socials_json || '[]' }),
};

const POSTS_CFG = {
  entity: 'posts', title: '📰 مطالب وبلاگ',
  columns: [
    { key: 'title', label: 'عنوان' },
    { key: 'status', label: 'وضعیت', render: (r) => <Badge kind={r.status === 'published' ? 'ok' : 'warn'}>{r.status === 'published' ? 'منتشرشده' : 'پیش‌نویس'}</Badge> },
    { key: 'published_at', label: 'تاریخ', render: (r) => faDate(r.published_at) },
  ],
  fields: [
    { key: 'title', label: 'عنوان', wide: true }, { key: 'slug', label: 'نامک (slug)', ltr: true },
    { key: 'excerpt', label: 'خلاصه', type: 'textarea', wide: true },
    { key: 'body', label: 'متن', type: 'textarea', wide: true },
    { key: 'status', label: 'وضعیت', type: 'select', options: { draft: 'پیش‌نویس', published: 'منتشرشده' }, default: 'published' },
    { key: 'published_at', label: 'تاریخ انتشار (خالی = اکنون)', ltr: true },
  ],
};

const PRODUCTS_CFG = {
  entity: 'products', title: '🛍 محصولات فروشگاه',
  columns: [
    { key: 'title', label: 'عنوان' },
    { key: 'price', label: 'قیمت', render: (r) => faMoney(r.price) },
    { key: 'sale_price', label: 'حراجی', render: (r) => (r.sale_price != null ? faMoney(r.sale_price) : '—') },
    { key: 'stock', label: 'موجودی' },
    { key: 'status', label: 'وضعیت', render: (r) => <Badge kind={r.status === 'publish' ? 'ok' : 'warn'}>{r.status === 'publish' ? 'منتشر' : 'پیش‌نویس'}</Badge> },
  ],
  fields: [
    { key: 'title', label: 'عنوان', wide: true }, { key: 'slug', label: 'نامک (slug)', ltr: true },
    { key: 'price', label: 'قیمت (تومان)', type: 'number' }, { key: 'sale_price', label: 'قیمت حراجی (خالی = بدون حراج)', type: 'number' },
    { key: 'stock', label: 'موجودی', type: 'number' }, { key: 'status', label: 'وضعیت', type: 'select', options: { publish: 'منتشر', draft: 'پیش‌نویس' }, default: 'publish' },
    { key: 'short_desc', label: 'توضیح کوتاه', type: 'textarea', wide: true },
    { key: 'body', label: 'متن کامل', type: 'textarea', wide: true },
  ],
};

const RULES_CFG = {
  entity: 'automation', title: '⚡ قواعد اتوماسیون',
  columns: [
    { key: 'title', label: 'عنوان' },
    { key: 'event_name', label: 'رویداد', render: (r) => <code>{r.event_name}</code> },
    { key: 'channel', label: 'اقدام', render: (r) => <Badge kind="accent">{r.channel}</Badge> },
    { key: 'is_active', label: 'فعال', render: (r) => (r.is_active ? '✅' : '⛔') },
  ],
  fields: [
    { key: 'title', label: 'عنوان قانون', wide: true },
    { key: 'event_name', label: 'رویداد', type: 'select', options: {
      user_registered: 'ثبت‌نام کاربر', order_paid: 'پرداخت سفارش', invoice_paid: 'پرداخت فاکتور',
      ticket_created: 'تیکت جدید', application_received: 'درخواست شغلی', loyalty_redeemed: 'دریافت پاداش', consult_created: 'درخواست مشاوره', order_created: 'ایجاد سفارش',
    } },
    { key: 'channel', label: 'اقدام', type: 'select', options: {
      sms_ir: 'پیامک sms.ir', melipayamak: 'پیامک ملی‌پیامک', bale: 'بله', telegram: 'تلگرام', email: 'ایمیل', add_segment: 'افزودن به بخش CRM', add_points: 'افزودن امتیاز',
    } },
    { key: 'condition_field', label: 'فیلد شرط (مثلاً amount)', hint: 'amount برای مبالغ و امتیاز، خالی = بدون شرط' },
    { key: 'condition_op', label: 'عملگر شرط', type: 'select', options: { gte: 'بزرگتر/مساوی', lte: 'کوچکتر/مساوی', eq: 'مساوی', contains: 'شامل' }, default: 'gte' },
    { key: 'condition_value', label: 'مقدار شرط' },
    { key: 'target', label: 'هدف (شناسه بخش یا تعداد امتیاز)' },
    { key: 'template', label: 'متن پیام — توکن‌ها: {name} {phone} {amount} {invoice} {title} {balance} {site}', type: 'textarea', wide: true },
    { key: 'is_active', label: 'فعال', type: 'toggle', default: 1 },
  ],
};

/* ══════════ Orders / Invoices / Tickets / Consults / Applications / Users / Outbox / Settings ══════════ */
function OrdersAdmin() {
  const [rows, setRows] = useState([]);
  const load = () => api('/cms/x/orders').then(setRows).catch(() => {});
  useEffect(() => { load(); }, []);
  const setStatus = async (id, status) => {
    await api(`/cms/x/orders/${id}/status`, { method: 'POST', body: { status } });
    load();
  };
  return (
    <Card>
      <h3 className="mb">🧾 سفارش‌ها</h3>
      <Table
        columns={[
          { key: 'id', label: 'شماره', render: (r) => '#' + r.id },
          { key: 'user_name', label: 'مشتری', render: (r) => `${r.user_name} (${r.phone})` },
          { key: 'total', label: 'مبلغ', render: (r) => faMoney(r.total) },
          { key: 'created_at', label: 'تاریخ', render: (r) => faDate(r.created_at) },
          { key: 'status', label: 'وضعیت', render: (r) => (
            <Select value={r.status} onChange={(e) => setStatus(r.id, e.target.value)} options={{ pending: 'در انتظار', paid: 'پرداخت‌شده', shipped: 'ارسال‌شده', completed: 'تکمیل‌شده', cancelled: 'لغوشده' }} />
          ) },
        ]}
        rows={rows}
      />
    </Card>
  );
}

function InvoicesAdmin() {
  const [rows, setRows] = useState([]);
  const [form, setForm] = useState(null);
  const load = () => api('/cms/x/invoices').then(setRows).catch(() => {});
  useEffect(() => { load(); }, []);
  const create = async () => {
    try {
      const r = await api('/cms/x/invoices', { method: 'POST', body: form });
      alert('فاکتور ' + r.number + ' صادر شد — لینک پرداخت: /pay/' + r.token);
      setForm(null);
      load();
    } catch (e) { alert(e.message); }
  };
  const send = async (id) => {
    const r = await api(`/cms/x/invoices/${id}/send`, { method: 'POST' });
    alert('📨 فاکتور برای مشتری ارسال شد — ' + r.link);
  };
  return (
    <Card>
      <div className="spread mb">
        <h3 style={{ margin: 0 }}>💳 فاکتورها و لینک پرداخت</h3>
        <Btn variant="primary" size="sm" onClick={() => setForm({ user_id: '', title: 'فاکتور خدمات', items: [{ title: '', qty: 1, unit_price: 0 }], discount: 0, tax: 0 })}>＋ صدور فاکتور</Btn>
      </div>
      <Table
        columns={[
          { key: 'number', label: 'شماره' },
          { key: 'user_name', label: 'مشتری', render: (r) => `${r.user_name} (${r.phone})` },
          { key: 'title', label: 'عنوان' },
          { key: 'total', label: 'مبلغ', render: (r) => faMoney(r.total) },
          { key: 'status', label: 'وضعیت', render: (r) => <Badge kind={r.status === 'paid' ? 'ok' : 'warn'}>{faStatus[r.status]}</Badge> },
          { key: 'link', label: 'لینک پرداخت', render: (r) => <Link to={'/pay/' + r.token}>/pay/{String(r.token).slice(0, 8)}…</Link> },
        ]}
        rows={rows}
        renderActions={(r) => <Btn size="sm" onClick={() => send(r.id)}>📨 ارسال به مشتری</Btn>}
      />
      {form && (
        <Modal title="صدور فاکتور جدید" onClose={() => setForm(null)}>
          <Field label="شناسه مشتری"><Input type="number" value={form.user_id} onChange={(e) => setForm({ ...form, user_id: e.target.value })} /></Field>
          <Field label="عنوان فاکتور"><Input value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })} /></Field>
          <Field label="اقلام">
            <Repeater
              rows={form.items}
              onChange={(items) => setForm({ ...form, items })}
              fields={[
                { key: 'title', label: 'عنوان قلم', wide: true },
                { key: 'qty', label: 'تعداد' },
                { key: 'unit_price', label: 'قیمت واحد' },
              ]}
              blank={{ title: '', qty: 1, unit_price: 0 }}
            />
          </Field>
          <div className="neo-grid neo-grid--2">
            <Field label="تخفیف (تومان)"><Input type="number" value={form.discount} onChange={(e) => setForm({ ...form, discount: e.target.value })} /></Field>
            <Field label="مالیات (تومان)"><Input type="number" value={form.tax} onChange={(e) => setForm({ ...form, tax: e.target.value })} /></Field>
          </div>
          <Btn variant="primary" onClick={create}>صدور و ساخت لینک پرداخت</Btn>
        </Modal>
      )}
    </Card>
  );
}

function TicketsAdmin() {
  const [rows, setRows] = useState([]);
  const [open, setOpen] = useState(null);
  const [reply, setReply] = useState('');
  const load = () => api('/cms/x/tickets').then(setRows).catch(() => {});
  useEffect(() => { load(); }, []);
  const openT = async (id) => setOpen(await api('/cms/x/tickets/' + id));
  const send = async () => {
    await api(`/cms/x/tickets/${open.id}/reply`, { method: 'POST', body: { body: reply } });
    setReply('');
    openT(open.id);
    load();
  };
  return (
    <Card>
      <h3 className="mb">🎫 تیکت‌های پشتیبانی</h3>
      <Table
        columns={[
          { key: 'subject', label: 'موضوع' },
          { key: 'user_name', label: 'مشتری', render: (r) => `${r.user_name} (${r.phone})` },
          { key: 'status', label: 'وضعیت', render: (r) => <Badge kind={r.status === 'answered' ? 'ok' : 'accent'}>{faStatus[r.status]}</Badge> },
          { key: 'updated_at', label: 'آخرین فعالیت', render: (r) => faDate(r.updated_at) },
        ]}
        rows={rows}
        renderActions={(r) => <Btn size="sm" onClick={() => openT(r.id)}>پاسخ</Btn>}
      />
      {open && (
        <Modal title={open.subject} onClose={() => setOpen(null)}>
          {(open.messages || []).map((m) => (
            <div key={m.id} className="neo-inset mb" style={{ marginBottom: 10 }}>
              <b style={{ fontSize: 12.5 }}>{m.name} {m.is_staff ? '— تیم' : '— مشتری'}</b>
              <div style={{ whiteSpace: 'pre-line', fontSize: 13.5 }}>{m.body}</div>
            </div>
          ))}
          <TextArea value={reply} onChange={(e) => setReply(e.target.value)} placeholder="پاسخ تیم پشتیبانی…" />
          <div className="mt"><Btn variant="primary" onClick={send}>ارسال پاسخ</Btn></div>
        </Modal>
      )}
    </Card>
  );
}

function ConsultsAdmin() {
  const [rows, setRows] = useState([]);
  const [edit, setEdit] = useState(null);
  const [answer, setAnswer] = useState('');
  const load = () => api('/cms/x/consultations').then(setRows).catch(() => {});
  useEffect(() => { load(); }, []);
  const send = async () => {
    await api(`/cms/x/consultations/${edit.id}/answer`, { method: 'POST', body: { answer } });
    setEdit(null);
    load();
  };
  return (
    <Card>
      <h3 className="mb">💬 درخواست‌های مشاوره و پشتیبانی</h3>
      <Table
        columns={[
          { key: 'subject', label: 'موضوع' },
          { key: 'user_name', label: 'مشتری', render: (r) => `${r.user_name} (${r.phone})` },
          { key: 'type', label: 'نوع', render: (r) => ({ consult: 'مشاوره', support: 'پشتیبانی', quote: 'پیشنهاد قیمت' }[r.type]) },
          { key: 'status', label: 'وضعیت', render: (r) => <Badge kind={r.status === 'answered' ? 'ok' : 'accent'}>{faStatus[r.status]}</Badge> },
        ]}
        rows={rows}
        renderActions={(r) => <Btn size="sm" onClick={() => { setEdit(r); setAnswer(r.answer || ''); }}>پاسخ</Btn>}
      />
      {edit && (
        <Modal title={edit.subject} onClose={() => setEdit(null)}>
          <div className="neo-inset mb">{edit.body}</div>
          <TextArea value={answer} onChange={(e) => setAnswer(e.target.value)} placeholder="پاسخ مشاور…" />
          <div className="mt"><Btn variant="primary" onClick={send}>ثبت پاسخ</Btn></div>
        </Modal>
      )}
    </Card>
  );
}

function ApplicationsAdmin() {
  const [rows, setRows] = useState([]);
  const load = () => api('/cms/x/applications').then(setRows).catch(() => {});
  useEffect(() => { load(); }, []);
  const setStage = async (id, stage) => {
    await api(`/cms/x/applications/${id}/stage`, { method: 'POST', body: { stage } });
    load();
  };
  return (
    <Card>
      <h3 className="mb">💼 متقاضیان استخدام (امتیازدهی خودکار)</h3>
      <Table
        columns={[
          { key: 'full_name', label: 'نام' },
          { key: 'job_title', label: 'موقعیت' },
          { key: 'phone', label: 'تماس', render: (r) => <span className="ltr">{r.phone}</span> },
          { key: 'score', label: 'امتیاز', render: (r) => <Badge kind={r.score >= 70 ? 'ok' : r.score >= 40 ? 'accent' : 'warn'}>{r.score}</Badge> },
          { key: 'stage', label: 'مرحله', render: (r) => (
            <Select value={r.stage} onChange={(e) => setStage(r.id, e.target.value)} options={{ applied: 'ثبت‌شده', screening: 'غربالگری', video: 'مصاحبه ویدیویی', test: 'تست', interview: 'مصاحبه', hired: 'استخدام', rejected: 'ردشده' }} />
          ) },
        ]}
        rows={rows}
      />
    </Card>
  );
}

function UsersAdmin() {
  const [rows, setRows] = useState([]);
  const [segments, setSegments] = useState([]);
  const [add, setAdd] = useState(null);
  const load = () => {
    api('/cms/x/users').then(setRows).catch(() => {});
    api('/cms/segments').then(setSegments).catch(() => {});
  };
  useEffect(() => { load(); }, []);
  const create = async () => {
    try {
      await api('/cms/x/users', { method: 'POST', body: add });
      setAdd(null);
      load();
    } catch (e) { alert(e.message); }
  };
  return (
    <div className="neo-grid">
      <Card>
        <div className="spread mb">
          <h3 style={{ margin: 0 }}>👤 مشتریان و CRM</h3>
          <Btn size="sm" variant="primary" onClick={() => setAdd({ name: '', phone: '', role: 'customer', password: '' })}>＋ مشتری جدید</Btn>
        </div>
        <Table
          columns={[
            { key: 'name', label: 'نام' },
            { key: 'phone', label: 'موبایل', render: (r) => <span className="ltr">{r.phone}</span> },
            { key: 'points_balance', label: 'امتیاز' },
            { key: 'role', label: 'نقش', render: (r) => <Badge kind={r.role === 'admin' ? 'accent' : ''}>{r.role === 'admin' ? 'مدیر' : r.role === 'crm' ? 'CRM' : 'مشتری'}</Badge> },
            { key: 'segments', label: 'بخش‌ها (سگمنت)', render: (r) => r.segments ? <span className="muted">{r.segments}</span> : '—' },
          ]}
          rows={rows}
        />
      </Card>
      <Card>
        <b>🏷 بخش‌بندی مشتریان (سگمنت‌ها)</b>
        <Table
          columns={[
            { key: 'title', label: 'بخش' },
            { key: 'description', label: 'توضیح', render: (r) => <span className="muted">{r.description}</span> },
          ]}
          rows={segments}
        />
        <p className="muted mt" style={{ fontSize: 12 }}>
          سگمنت‌ها به‌صورت خودکار با قواعد اتوماسیون (اقدام «افزودن به بخش CRM») پر می‌شوند؛ همچنین از جدول «خدمات من» در پنل مشتری، علایق ثبت می‌شود.
        </p>
      </Card>
      {add && (
        <Modal title="مشتری جدید" onClose={() => setAdd(null)}>
          <Field label="نام"><Input value={add.name} onChange={(e) => setAdd({ ...add, name: e.target.value })} /></Field>
          <Field label="موبایل"><Input className="ltr" value={add.phone} onChange={(e) => setAdd({ ...add, phone: e.target.value })} /></Field>
          <Field label="نقش"><Select value={add.role} onChange={(e) => setAdd({ ...add, role: e.target.value })} options={{ customer: 'مشتری', crm: 'کارشناس CRM', admin: 'مدیر' }} /></Field>
          <Field label="رمز عبور موقت"><Input className="ltr" value={add.password} onChange={(e) => setAdd({ ...add, password: e.target.value })} /></Field>
          <Btn variant="primary" onClick={create}>ایجاد کاربر</Btn>
        </Modal>
      )}
    </div>
  );
}

function OutboxAdmin() {
  const [rows, setRows] = useState([]);
  const [logs, setLogs] = useState([]);
  useEffect(() => {
    api('/cms/x/outbox').then(setRows).catch(() => {});
    api('/cms/x/automation-logs').then(setLogs).catch(() => {});
  }, []);
  return (
    <div className="neo-grid">
      <Card>
        <h3 className="mb">📤 صندوق اعلان (SMS / بله / تلگرام / ایمیل)</h3>
        <Table
          columns={[
            { key: 'channel', label: 'کانال', render: (r) => <Badge kind="accent">{r.channel}</Badge> },
            { key: 'recipient', label: 'گیرنده', render: (r) => <span className="ltr">{r.recipient}</span> },
            { key: 'message', label: 'پیام', render: (r) => <span className="muted">{String(r.message).slice(0, 50)}…</span> },
            { key: 'status', label: 'وضعیت', render: (r) => <Badge kind={r.status === 'sent' ? 'ok' : r.status === 'failed' ? 'warn' : ''}>{r.status === 'sent' ? 'ارسال‌شده' : r.status === 'failed' ? 'خطا' : 'در صف'}</Badge> },
          ]}
          rows={rows}
        />
      </Card>
      <Card>
        <b>گزارش اتوماسیون</b>
        <Table
          columns={[
            { key: 'rule_title', label: 'قانون', render: (r) => r.rule_title || '—' },
            { key: 'event_name', label: 'رویداد', render: (r) => <code>{r.event_name}</code> },
            { key: 'result', label: 'نتیجه' },
            { key: 'created_at', label: 'زمان', render: (r) => faDate(r.created_at) },
          ]}
          rows={logs.slice(0, 30)}
        />
      </Card>
    </div>
  );
}

function SettingsAdmin() {
  const [s, setS] = useState(null);
  useEffect(() => { api('/cms/settings/all').then(setS).catch(() => {}); }, []);
  if (!s) return <div className="center muted">در حال بارگذاری…</div>;

  const setTheme = (k, v) => setS({ ...s, theme: { ...s.theme, [k]: v } });
  const setNested = (ns, k, v) => setS({ ...s, [ns]: { ...s[ns], [k]: v } });

  const save = async () => {
    await api('/cms/settings', { method: 'PUT', body: s });
    alert('✅ تنظیمات ذخیره شد — صفحه اصلی را رفرش کنید تا قالب اعمال شود.');
  };

  return (
    <div className="neo-grid">
      <Card>
        <h3 className="mb">🎨 تنظیمات قالب نئومورف</h3>
        <div className="neo-grid neo-grid--2">
          <Field label="رنگ پس‌زمینه"><Color value={s.theme.color_bg || '#e8edf5'} onChange={(v) => setTheme('color_bg', v)} /></Field>
          <Field label="رنگ متن"><Color value={s.theme.color_text || '#2f3542'} onChange={(v) => setTheme('color_text', v)} /></Field>
          <Field label="رنگ کمکی (Muted)"><Color value={s.theme.color_muted || '#8a93a6'} onChange={(v) => setTheme('color_muted', v)} /></Field>
          <Field label="رنگ اصلی (Accent)"><Color value={s.theme.color_accent || '#6c5ce7'} onChange={(v) => setTheme('color_accent', v)} /></Field>
          <Field label="رنگ دوم (Accent 2)"><Color value={s.theme.color_accent2 || '#00b894'} onChange={(v) => setTheme('color_accent2', v)} /></Field>
          <Field label="شیوه نرم" hint="raised = برجسته، inset = فرورفته">
            <Segmented value={s.theme.soft_style || 'raised'} onChange={(v) => setTheme('soft_style', v)} options={{ raised: 'برجسته', inset: 'فرورفته', flat: 'تخت' }} />
          </Field>
          <Field label="شعاع گوشه‌ها"><Slider min={4} max={48} value={Number(s.theme.radius ?? 22)} onChange={(v) => setTheme('radius', v)} unit="px" /></Field>
          <Field label="فاصله سایه"><Slider min={2} max={16} value={Number(s.theme.distance ?? 8)} onChange={(v) => setTheme('distance', v)} unit="px" /></Field>
          <Field label="نرمی سایه"><Slider min={1} max={4} value={Number(s.theme.softness ?? 2)} onChange={(v) => setTheme('softness', v)} unit="×" /></Field>
          <Field label="اندازه پایه فونت"><Slider min={13} max={20} value={Number(s.theme.font_size_base ?? 16)} onChange={(v) => setTheme('font_size_base', v)} unit="px" /></Field>
          <Field label="عرض کانتینر"><Slider min={900} max={1500} step={10} value={Number(s.theme.container_width ?? 1180)} onChange={(v) => setTheme('container_width', v)} unit="px" /></Field>
          <Field label="چیدمان هدر"><Segmented value={s.theme.header_layout || 'sticky'} onChange={(v) => setTheme('header_layout', v)} options={{ sticky: 'چسبان', static: 'ساده', centered: 'وسط‌چین' }} /></Field>
        </div>
      </Card>

      <Card>
        <h3 className="mb">🏢 اطلاعات سایت و تماس</h3>
        <Field label="نام سایت"><Input value={s.site.name || ''} onChange={(e) => setNested('site', 'name', e.target.value)} /></Field>
        <Field label="شعار"><Input value={s.site.tagline || ''} onChange={(e) => setNested('site', 'tagline', e.target.value)} /></Field>
        <Field label="تلفن تماس"><Input value={s.contact.phone || ''} onChange={(e) => setNested('contact', 'phone', e.target.value)} /></Field>
        <Field label="ایمیل"><Input className="ltr" value={s.contact.email || ''} onChange={(e) => setNested('contact', 'email', e.target.value)} /></Field>
        <Field label="نشانی"><TextArea value={s.contact.address || ''} onChange={(e) => setNested('contact', 'address', e.target.value)} /></Field>
        <Field label="شبکه‌های اجتماعی">
          <Repeater rows={s.socials || []} onChange={(rows) => setS({ ...s, socials: rows })} fields={[{ key: 'label', label: 'نام' }, { key: 'url', label: 'لینک', wide: true }]} blank={{ label: '', url: '' }} />
        </Field>
        <Field label="هدیه ثبت‌نام (امتیاز)"><Input type="number" value={s.loyalty?.signup_bonus ?? 100} onChange={(e) => setNested('loyalty', 'signup_bonus', Number(e.target.value))} /></Field>
      </Card>

      <Card>
        <h3 className="mb">🔌 یکپارچه‌سازی‌ها (SMS / بله / تلگرام)</h3>
        <Field label="ارسال پیامک از طریق" hint="پنل پیامکی پیش‌فرض">
          <Segmented value={s.sms_provider || 'sms_ir'} onChange={(v) => setS({ ...s, sms_provider: v })} options={{ sms_ir: 'sms.ir', melipayamak: 'ملی‌پیامک' }} />
        </Field>
        <Field label="sms.ir — کلید API"><Input className="ltr" value={s.integrations.sms_ir?.api_key || ''} onChange={(e) => setNested('integrations', 'sms_ir', { ...s.integrations.sms_ir, api_key: e.target.value })} /></Field>
        <Field label="sms.ir — شناسه قالب OTP"><Input className="ltr" value={s.integrations.sms_ir?.otp_template_id || ''} onChange={(e) => setNested('integrations', 'sms_ir', { ...s.integrations.sms_ir, otp_template_id: e.target.value })} /></Field>
        <Field label="ملی‌پیامک — نام کاربری"><Input className="ltr" value={s.integrations.melipayamak?.username || ''} onChange={(e) => setNested('integrations', 'melipayamak', { ...s.integrations.melipayamak, username: e.target.value })} /></Field>
        <Field label="ملی‌پیامک — رمز / شماره فرستنده"><Input className="ltr" value={s.integrations.melipayamak?.password || ''} onChange={(e) => setNested('integrations', 'melipayamak', { ...s.integrations.melipayamak, password: e.target.value })} /></Field>
        <Field label="بله — توکن ربات"><Input className="ltr" value={s.integrations.bale?.token || ''} onChange={(e) => setNested('integrations', 'bale', { ...s.integrations.bale, token: e.target.value })} /></Field>
        <Field label="تلگرام — توکن ربات"><Input className="ltr" value={s.integrations.telegram?.token || ''} onChange={(e) => setNested('integrations', 'telegram', { ...s.integrations.telegram, token: e.target.value })} /></Field>
        <Field label="حالت نمایشی OTP" hint="کد تایید در پاسخ API نمایش داده شود (بدون درگاه واقعی)">
          <Toggle value={s.otp?.demo ? 1 : 0} onChange={(v) => setNested('otp', 'demo', !!v)} labels={['فعال', 'غیرفعال']} />
        </Field>
      </Card>

      <div style={{ gridColumn: '1 / -1' }}>
        <Btn variant="primary" onClick={save}>💾 ذخیره همه تنظیمات</Btn>
      </div>
    </div>
  );
}
