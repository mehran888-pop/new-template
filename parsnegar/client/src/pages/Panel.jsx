import React, { useEffect, useState } from 'react';
import { Routes, Route, NavLink, Navigate, Link } from 'react-router-dom';
import { api, getToken, faMoney, faDate, faStatus, setToken } from '../api';
import { Card, Btn, Field, Input, TextArea, Select, Alert, Badge, Table, Stat, Modal, Segmented } from '../components/ui';
import { useApp } from '../App';

/** پنل مشتری: داشبورد، خریدها، مالی/فاکتور، وفاداری، تیکت‌ها، مشاوره، خدمات من، پروفایل */
export default function Panel() {
  const { user, setUser } = useApp();
  if (!getToken()) return <Navigate to="/login" replace />;
  if (!user) return <div className="page center muted">در حال بارگذاری…</div>;

  return (
    <div className="page">
      <div className="spread mb">
        <h2>👤 پنل {user.name || user.phone}</h2>
        <Badge kind="accent">امتیاز وفاداری: {(user.points_balance || 0).toLocaleString('fa-IR')}</Badge>
      </div>
      <div className="neo-shell">
        <nav className="neo-side neo-card" style={{ padding: 14 }}>
          <NavLink to="/panel" end>📊 داشبورد</NavLink>
          <NavLink to="/panel/orders">🛍 خریدهای من</NavLink>
          <NavLink to="/panel/invoices">💳 مالی و فاکتور</NavLink>
          <NavLink to="/panel/loyalty">🎁 باشگاه وفاداری</NavLink>
          <NavLink to="/panel/tickets">🎫 تیکت‌ها</NavLink>
          <NavLink to="/panel/consults">💬 مشاوره و پشتیبانی</NavLink>
          <NavLink to="/panel/services">🧩 خدمات من</NavLink>
          <NavLink to="/panel/profile">⚙️ پروفایل</NavLink>
        </nav>
        <div>
          <Routes>
            <Route index element={<Dashboard />} />
            <Route path="orders" element={<Orders />} />
            <Route path="invoices" element={<Invoices />} />
            <Route path="loyalty" element={<Loyalty />} />
            <Route path="tickets" element={<Tickets />} />
            <Route path="consults" element={<Consults />} />
            <Route path="services" element={<MyServices />} />
            <Route path="profile" element={<Profile />} />
            <Route path="*" element={<Navigate to="/panel" replace />} />
          </Routes>
        </div>
      </div>
    </div>
  );
}

function Dashboard() {
  const { user } = useApp();
  const [orders, setOrders] = useState([]);
  const [invoices, setInvoices] = useState([]);
  const [tickets, setTickets] = useState([]);
  useEffect(() => {
    api('/my/orders').then(setOrders).catch(() => {});
    api('/my/invoices').then(setInvoices).catch(() => {});
    api('/my/tickets').then(setTickets).catch(() => {});
  }, []);
  return (
    <div className="neo-grid neo-grid--4">
      <div className="neo-card neo-stat"><b>{orders.length}</b><span>سفارش</span></div>
      <div className="neo-card neo-stat"><b>{invoices.filter((i) => i.status === 'unpaid').length}</b><span>فاکتور باز</span></div>
      <div className="neo-card neo-stat"><b>{(user.points_balance || 0).toLocaleString('fa-IR')}</b><span>امتیاز وفاداری</span></div>
      <div className="neo-card neo-stat"><b>{tickets.filter((t) => t.status !== 'closed').length}</b><span>تیکت باز</span></div>
      <Card style={{ gridColumn: '1 / -1' }}>
        <b>آخرین فاکتورها</b>
        <Table
          columns={[
            { key: 'number', label: 'شماره' },
            { key: 'title', label: 'عنوان' },
            { key: 'total', label: 'مبلغ', render: (r) => faMoney(r.total) },
            { key: 'status', label: 'وضعیت', render: (r) => <Badge kind={r.status === 'paid' ? 'ok' : 'warn'}>{faStatus[r.status]}</Badge> },
          ]}
          rows={invoices.slice(0, 5)}
        />
      </Card>
    </div>
  );
}

function Orders() {
  const [orders, setOrders] = useState([]);
  useEffect(() => { api('/my/orders').then(setOrders).catch(() => {}); }, []);
  return (
    <Card>
      <h3 className="mb">🛍 خریدهای من</h3>
      <Table
        columns={[
          { key: 'id', label: 'شماره سفارش', render: (r) => '#' + r.id },
          { key: 'total', label: 'مبلغ', render: (r) => faMoney(r.total) },
          { key: 'created_at', label: 'تاریخ', render: (r) => faDate(r.created_at) },
          { key: 'status', label: 'وضعیت', render: (r) => <Badge kind={r.status === 'paid' || r.status === 'completed' ? 'ok' : 'warn'}>{faStatus[r.status]}</Badge> },
        ]}
        rows={orders}
      />
    </Card>
  );
}

function Invoices() {
  const [invoices, setInvoices] = useState([]);
  useEffect(() => { api('/my/invoices').then(setInvoices).catch(() => {}); }, []);
  return (
    <Card>
      <h3 className="mb">💳 مالی و فاکتور</h3>
      <Table
        columns={[
          { key: 'number', label: 'شماره' },
          { key: 'title', label: 'عنوان' },
          { key: 'total', label: 'مبلغ', render: (r) => faMoney(r.total) },
          { key: 'status', label: 'وضعیت', render: (r) => <Badge kind={r.status === 'paid' ? 'ok' : r.status === 'unpaid' ? 'warn' : ''}>{faStatus[r.status]}</Badge> },
          { key: 'pay', label: 'لینک پرداخت', render: (r) => <Link className="neo-btn neo-btn--sm" to={'/pay/' + r.token}>مشاهده / پرداخت</Link> },
        ]}
        rows={invoices}
      />
    </Card>
  );
}

function Loyalty() {
  const [data, setData] = useState(null);
  const load = () => api('/my/loyalty').then(setData).catch(() => {});
  useEffect(() => { load(); }, []);
  if (!data) return <div className="center muted">در حال بارگذاری…</div>;
  const redeem = async (id) => {
    try {
      const r = await api('/my/loyalty/redeem', { method: 'POST', body: { reward_id: id } });
      alert('✅ پاداش دریافت شد — موجودی جدید: ' + r.balance);
      load();
    } catch (e) { alert(e.message); }
  };
  return (
    <div className="neo-grid">
      <Card className="center">
        <div style={{ fontSize: 40 }}>🎁</div>
        <h3>باشگاه وفاداری پارسی‌نگر</h3>
        <div className="neo-inset" style={{ fontSize: 26, fontWeight: 900, color: 'var(--neo-accent)' }}>
          {(data.balance || 0).toLocaleString('fa-IR')} <span style={{ fontSize: 13 }}>امتیاز</span>
        </div>
      </Card>
      <Card>
        <b>پاداش‌های قابل دریافت</b>
        <div className="neo-grid neo-grid--3 mt">
          {data.rewards.map((r) => (
            <div key={r.id} className="neo-inset center">
              <b>{r.title}</b>
              <div className="muted" style={{ fontSize: 12 }}>{r.cost.toLocaleString('fa-IR')} امتیاز</div>
              <Btn size="sm" variant={data.balance >= r.cost ? 'primary' : ''} disabled={data.balance < r.cost} onClick={() => redeem(r.id)}>دریافت</Btn>
            </div>
          ))}
        </div>
      </Card>
      <Card>
        <b>کارنامه امتیاز</b>
        <Table
          columns={[
            { key: 'description', label: 'شرح' },
            { key: 'delta', label: 'امتیاز', render: (r) => <Badge kind={r.delta > 0 ? 'ok' : 'warn'}>{r.delta > 0 ? '+' : ''}{r.delta}</Badge> },
            { key: 'balance_after', label: 'موجودی', render: (r) => r.balance_after?.toLocaleString('fa-IR') },
            { key: 'created_at', label: 'تاریخ', render: (r) => faDate(r.created_at) },
          ]}
          rows={data.transactions}
        />
      </Card>
    </div>
  );
}

function Tickets() {
  const [tickets, setTickets] = useState([]);
  const [open, setOpen] = useState(null);
  const [newT, setNewT] = useState({ subject: '', body: '', department: 'general' });
  const [reply, setReply] = useState('');
  const load = () => api('/my/tickets').then(setTickets).catch(() => {});
  useEffect(() => { load(); }, []);

  const create = async () => {
    try {
      await api('/my/tickets', { method: 'POST', body: newT });
      setNewT({ subject: '', body: '', department: 'general' });
      load();
    } catch (e) { alert(e.message); }
  };
  const openTicket = async (id) => {
    try { setOpen(await api('/my/tickets/' + id)); } catch (e) { alert(e.message); }
  };
  const send = async () => {
    try {
      await api('/my/tickets/' + open.id + '/messages', { method: 'POST', body: { body: reply } });
      setReply('');
      openTicket(open.id);
      load();
    } catch (e) { alert(e.message); }
  };

  return (
    <div className="neo-grid">
      <Card>
        <h3 className="mb">🎫 تیکت‌های پشتیبانی</h3>
        <Table
          columns={[
            { key: 'subject', label: 'موضوع' },
            { key: 'department', label: 'واحد', render: (r) => ({ general: 'عمومی', sales: 'فروش', support: 'پشتیبانی', finance: 'مالی' }[r.department]) },
            { key: 'status', label: 'وضعیت', render: (r) => <Badge kind={r.status === 'answered' ? 'ok' : r.status === 'open' ? 'accent' : ''}>{faStatus[r.status]}</Badge> },
          ]}
          rows={tickets}
          renderActions={(r) => <Btn size="sm" onClick={() => openTicket(r.id)}>مشاهده</Btn>}
        />
        {open && (
          <Modal title={open.subject} onClose={() => setOpen(null)}>
            {(open.messages || []).map((m) => (
              <div key={m.id} className="neo-inset mb" style={{ marginBottom: 10, borderRight: m.is_staff ? '4px solid var(--neo-accent)' : '4px solid var(--neo-accent-2)' }}>
                <div className="spread"><b style={{ fontSize: 12.5 }}>{m.name} {m.is_staff && '— تیم پارسی‌نگر'}</b><span className="muted" style={{ fontSize: 11 }}>{faDate(m.created_at)}</span></div>
                <div style={{ whiteSpace: 'pre-line', fontSize: 13.5 }}>{m.body}</div>
              </div>
            ))}
            <TextArea value={reply} onChange={(e) => setReply(e.target.value)} placeholder="پیام شما…" />
            <div className="mt"><Btn variant="primary" onClick={send}>ارسال پاسخ</Btn></div>
          </Modal>
        )}
      </Card>
      <Card>
        <b>تیکت جدید</b>
        <Field label="موضوع"><Input value={newT.subject} onChange={(e) => setNewT({ ...newT, subject: e.target.value })} /></Field>
        <Field label="واحد">
          <Select value={newT.department} onChange={(e) => setNewT({ ...newT, department: e.target.value })} options={{ general: 'عمومی', sales: 'فروش', support: 'پشتیبانی', finance: 'مالی' }} />
        </Field>
        <Field label="متن پیام"><TextArea value={newT.body} onChange={(e) => setNewT({ ...newT, body: e.target.value })} /></Field>
        <Btn variant="primary" onClick={create}>ثبت تیکت</Btn>
      </Card>
    </div>
  );
}

function Consults() {
  const [rows, setRows] = useState([]);
  const [form, setForm] = useState({ type: 'consult', subject: '', body: '' });
  const load = () => api('/my/consultations').then(setRows).catch(() => {});
  useEffect(() => { load(); }, []);
  const create = async () => {
    try {
      await api('/my/consultations', { method: 'POST', body: form });
      setForm({ type: 'consult', subject: '', body: '' });
      load();
    } catch (e) { alert(e.message); }
  };
  return (
    <div className="neo-grid">
      <Card>
        <h3 className="mb">💬 مشاوره و درخواست پشتیبانی</h3>
        {rows.map((r) => (
          <div key={r.id} className="neo-inset mb" style={{ marginBottom: 10 }}>
            <div className="spread">
              <b>{r.subject}</b>
              <Badge kind={r.status === 'answered' ? 'ok' : 'accent'}>{faStatus[r.status]}</Badge>
            </div>
            <div className="muted" style={{ fontSize: 12 }}>{({ consult: 'مشاوره', support: 'پشتیبانی', quote: 'پیشنهاد قیمت' })[r.type]} — {faDate(r.created_at)}</div>
            <div style={{ fontSize: 13 }}>{r.body}</div>
            {r.answer && <div className="neo-inset mt" style={{ marginTop: 8, fontSize: 13 }}>💬 {r.answer}</div>}
          </div>
        ))}
        {!rows.length && <p className="muted center">درخواستی ثبت نشده است</p>}
      </Card>
      <Card>
        <b>درخواست جدید</b>
        <Field label="نوع">
          <Segmented value={form.type} onChange={(v) => setForm({ ...form, type: v })} options={{ consult: 'مشاوره', support: 'پشتیبانی', quote: 'پیشنهاد قیمت' }} />
        </Field>
        <Field label="موضوع"><Input value={form.subject} onChange={(e) => setForm({ ...form, subject: e.target.value })} /></Field>
        <Field label="شرح"><TextArea value={form.body} onChange={(e) => setForm({ ...form, body: e.target.value })} /></Field>
        <Btn variant="primary" onClick={create}>ثبت درخواست</Btn>
      </Card>
    </div>
  );
}

function MyServices() {
  const [rows, setRows] = useState([]);
  const [services, setServices] = useState([]);
  const load = () => api('/my/services').then(setRows).catch(() => {});
  useEffect(() => { load(); api('/services').then(setServices).catch(() => {}); }, []);
  const track = async (service_id, status) => {
    try {
      await api('/my/services', { method: 'POST', body: { service_id, status } });
      load();
    } catch (e) { alert(e.message); }
  };
  return (
    <div className="neo-grid">
      <Card>
        <h3 className="mb">🧩 خدمات انتخابی من</h3>
        <Table
          columns={[
            { key: 'title', label: 'خدمت', render: (r) => (r.icon_text || '✦') + ' ' + r.title },
            { key: 'status', label: 'وضعیت', render: (r) => <Badge kind={r.status === 'purchased' ? 'ok' : 'accent'}>{faStatus[r.status]}</Badge> },
            { key: 'created_at', label: 'ثبت‌شده', render: (r) => faDate(r.created_at) },
          ]}
          rows={rows}
        />
      </Card>
      <Card>
        <b>افزودن خدمت مورد علاقه</b>
        <div className="neo-grid neo-grid--2 mt">
          {services.map((s) => (
            <div key={s.id} className="neo-inset spread">
              <span style={{ fontSize: 13 }}>{s.icon_text} {s.title}</span>
              <Btn size="sm" onClick={() => track(s.id, 'interested')}>＋</Btn>
            </div>
          ))}
        </div>
      </Card>
    </div>
  );
}

function Profile() {
  const { user, setUser } = useApp();
  const [form, setForm] = useState(null);
  useEffect(() => {
    api('/auth/me').then((r) => setForm({ ...r.user, ...r.profile })).catch(() => {});
  }, []);
  if (!form) return <div className="center muted">در حال بارگذاری…</div>;
  const save = async () => {
    try {
      await api('/my/profile', { method: 'PUT', body: form });
      const me = await api('/auth/me');
      setUser(me.user);
      alert('✅ پروفایل ذخیره شد');
    } catch (e) { alert(e.message); }
  };
  return (
    <Card>
      <h3 className="mb">⚙️ پروفایل و تنظیمات</h3>
      <div className="neo-grid neo-grid--2">
        <Field label="نام"><Input value={form.name || ''} onChange={(e) => setForm({ ...form, name: e.target.value })} /></Field>
        <Field label="ایمیل"><Input className="ltr" value={form.email || ''} onChange={(e) => setForm({ ...form, email: e.target.value })} /></Field>
        <Field label="شرکت"><Input value={form.company || ''} onChange={(e) => setForm({ ...form, company: e.target.value })} /></Field>
        <Field label="شهر"><Input value={form.city || ''} onChange={(e) => setForm({ ...form, city: e.target.value })} /></Field>
        <Field label="کد ملی"><Input className="ltr" value={form.national_code || ''} onChange={(e) => setForm({ ...form, national_code: e.target.value })} /></Field>
        <Field label="تاریخ تولد"><Input value={form.birthday || ''} onChange={(e) => setForm({ ...form, birthday: e.target.value })} placeholder="۱۳۷۰/۰۵/۱۲" /></Field>
        <Field label="چت‌آیدی بله" hint="برای اعلان‌های بله"><Input className="ltr" value={form.bale_chat_id || ''} onChange={(e) => setForm({ ...form, bale_chat_id: e.target.value })} /></Field>
        <Field label="چت‌آیدی تلگرام" hint="برای اعلان‌های تلگرام"><Input className="ltr" value={form.telegram_chat_id || ''} onChange={(e) => setForm({ ...form, telegram_chat_id: e.target.value })} /></Field>
        <Field label="نشانی" wide><TextArea value={form.address || ''} onChange={(e) => setForm({ ...form, address: e.target.value })} /></Field>
      </div>
      <Btn variant="primary" onClick={save}>ذخیره تغییرات</Btn>
    </Card>
  );
}
