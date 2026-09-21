import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api, faMoney, faDate } from '../api';
import { Card, Btn, Badge, Field, Input, TextArea, Alert } from '../components/ui';
import { useApp } from '../App';

/** صفحه اصلی — تمام ویجت‌های نئومورف: Hero/خدمات/درباره/پروژه‌ها/تیم/وبلاگ/محصولات/تماس */
export default function Home() {
  const { meta } = useApp();
  const [services, setServices] = useState([]);
  const [projects, setProjects] = useState([]);
  const [team, setTeam] = useState([]);
  const [posts, setPosts] = useState([]);
  const [products, setProducts] = useState([]);
  const [form, setForm] = useState({ subject: '', body: '' });
  const [sent, setSent] = useState(false);

  useEffect(() => {
    api('/services').then(setServices).catch(() => {});
    api('/projects').then(setProjects).catch(() => {});
    api('/team').then(setTeam).catch(() => {});
    api('/posts').then((r) => setPosts(r.slice(0, 3))).catch(() => {});
    api('/products').then((r) => setProducts(r.slice(0, 3))).catch(() => {});
  }, []);

  const sendConsult = async (e) => {
    e.preventDefault();
    try {
      await api('/my/consultations', { method: 'POST', body: { type: 'consult', ...form } });
      setSent(true);
    } catch (err) {
      alert(err.message);
    }
  };

  return (
    <div className="page">
      {/* Hero */}
      <section className="neo-hero">
        <div className="neo-icon-tile" style={{ margin: '0 auto 18px', width: 84, height: 84, fontSize: 38 }}>🧠</div>
        <h1>
          <em>هوش یار پارسی نگر</em><br />
          دستیار هوشمند کسب‌وکار شما
        </h1>
        <p>{meta?.site?.tagline || 'اتوماسیون، CRM، فاکتور و باشگاه مشتریان — همه در یک پلتفرم فارسی.'}</p>
        <div className="row" style={{ justifyContent: 'center' }}>
          <Link to="/shop" className="neo-btn neo-btn--primary">🛍 خرید اشتراک</Link>
          <Link to="/register" className="neo-btn">🚀 شروع رایگان</Link>
        </div>
      </section>

      {/* Services */}
      <div className="neo-section__head">
        <h2 className="neo-section__title">خدمات ما</h2>
        <p className="neo-section__subtitle">هر آنچه برای هوشمندسازی کسب‌وکارتان لازم دارید</p>
      </div>
      <div className="neo-grid neo-grid--3">
        {services.map((s) => (
          <Card key={s.id}>
            <div className="neo-icon-tile">{s.icon_text || '✦'}</div>
            <h3>{s.title}</h3>
            <p className="muted">{s.short_desc}</p>
            {(s.features || []).map((f, i) => (
              <div key={i} className="neo-inset mb" style={{ marginBottom: 8 }}>
                <b style={{ fontSize: 12.5 }}>{f.title}</b>
                <div className="muted" style={{ fontSize: 11.5 }}>{f.description}</div>
              </div>
            ))}
          </Card>
        ))}
      </div>

      {/* About + Projects */}
      <div className="neo-section__head">
        <h2 className="neo-section__title">درباره پارسی‌نگر</h2>
        <p className="neo-section__subtitle">تیمی از متخصصان هوش مصنوعی و محصول، متمرکز بر زبان فارسی</p>
      </div>
      <div className="neo-grid neo-grid--2">
        <Card>
          <p>
            ما در «هوش یار پارسی نگر» معتقدیم فناوری هوش مصنوعی وقتی ارزشمند است که زبان، فرهنگ و اقتصاد ایران را بفهمد.
            پلتفرم ما از دستیار هوشمند فارسی تا اتوماسیون بازاریابی، CRM و فاکتور آنلاین را یکجا ارائه می‌دهد.
          </p>
          <div className="row mt">
            <Badge kind="accent">۱۲ هزار+ گفتگوی روزانه</Badge>
            <Badge kind="ok">۹۹.۹٪ آپ‌تایم</Badge>
            <Badge>۴۰+ کسب‌وکار ایرانی</Badge>
          </div>
        </Card>
        <div className="neo-grid" style={{ gap: 14 }}>
          {projects.slice(0, 2).map((p) => (
            <Card key={p.id} style={{ padding: 18 }}>
              <div className="spread">
                <b>{p.title}</b>
                <Badge kind={p.status === 'completed' ? 'ok' : 'accent'}>
                  {p.status === 'completed' ? 'تکمیل‌شده' : p.status === 'ongoing' ? 'در حال اجرا' : 'نمونه‌کار'}
                </Badge>
              </div>
              <div className="muted" style={{ fontSize: 12 }}>{p.client} — {p.year}</div>
              {(p.highlights || []).map((h, i) => <li key={i} style={{ fontSize: 12.5 }}>{h}</li>)}
            </Card>
          ))}
        </div>
      </div>

      {/* Team */}
      <div className="neo-section__head">
        <h2 className="neo-section__title">تیم ما</h2>
      </div>
      <div className="neo-grid neo-grid--3">
        {team.map((m) => (
          <Card key={m.id} className="center">
            <img className="neo-avatar" src={m.photo_media_id ? '/assets/avatar1.svg' : '/assets/avatar1.svg'} alt={m.name} style={{ margin: '0 auto 12px' }} />
            <h3 style={{ fontSize: 17 }}>{m.name}</h3>
            <Badge kind="accent">{m.role_title}</Badge>
            <p className="muted mt">{m.bio}</p>
          </Card>
        ))}
      </div>

      {/* Blog */}
      <div className="neo-section__head">
        <h2 className="neo-section__title">تازه‌ترین مطالب</h2>
      </div>
      <div className="neo-grid neo-grid--3">
        {posts.map((p) => (
          <Card key={p.id}>
            <Badge>{p.category || 'وبلاگ'}</Badge>
            <h3 style={{ fontSize: 16, marginTop: 10 }}><Link to={'/blog/' + p.slug}>{p.title}</Link></h3>
            <p className="muted">{p.excerpt}</p>
            <div className="muted" style={{ fontSize: 11 }}>{faDate(p.published_at)}</div>
          </Card>
        ))}
      </div>

      {/* Products */}
      <div className="neo-section__head">
        <h2 className="neo-section__title">محصولات</h2>
      </div>
      <div className="neo-grid neo-grid--3">
        {products.map((p) => (
          <Card key={p.id}>
            <h3 style={{ fontSize: 16 }}>{p.title}</h3>
            <p className="muted">{p.short_desc}</p>
            <div className="spread">
              <b style={{ color: 'var(--neo-accent)' }}>{faMoney(p.sale_price ?? p.price)}</b>
              <Link to={'/shop/' + p.slug} className="neo-btn neo-btn--sm">جزئیات</Link>
            </div>
          </Card>
        ))}
      </div>

      {/* Contact / consultation */}
      <div className="neo-section__head">
        <h2 className="neo-section__title">درخواست مشاوره</h2>
        <p className="neo-section__subtitle">کارشناسان ما کمتر از یک روز کاری پاسخ می‌دهند</p>
      </div>
      <Card style={{ maxWidth: 640, margin: '0 auto' }}>
        {sent ? (
          <Alert kind="ok">✅ درخواست شما ثبت شد؛ به‌زودی با شما تماس می‌گیریم.</Alert>
        ) : (
          <form onSubmit={sendConsult}>
            <Field label="موضوع"><Input value={form.subject} onChange={(e) => setForm({ ...form, subject: e.target.value })} required /></Field>
            <Field label="توضیحات"><TextArea value={form.body} onChange={(e) => setForm({ ...form, body: e.target.value })} /></Field>
            <Btn variant="primary" type="submit">ارسال درخواست مشاوره</Btn>
          </form>
        )}
      </Card>
    </div>
  );
}
