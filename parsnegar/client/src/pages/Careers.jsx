import React, { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { api } from '../api';
import { Card, Btn, Field, Input, TextArea, Alert, Badge, Segmented } from '../components/ui';

export function Jobs() {
  const [jobs, setJobs] = useState([]);
  useEffect(() => { api('/jobs').then(setJobs).catch(() => {}); }, []);
  return (
    <div className="page">
      <div className="neo-section__head">
        <h2 className="neo-section__title">فرصت‌های شغلی</h2>
        <p className="neo-section__subtitle">فرم چندمرحله‌ای با امتیازدهی خودکار — با امتیاز بالا مستقیم به مصاحبه ویدیویی</p>
      </div>
      <div className="neo-grid neo-grid--2">
        {jobs.map((j) => (
          <Card key={j.id}>
            <div className="spread">
              <h3 style={{ fontSize: 18 }}>{j.title}</h3>
              <Badge kind="accent">{j.department}</Badge>
            </div>
            <div className="row mb">
              <Badge>{j.job_type === 'full-time' ? 'تمام‌وقت' : j.job_type}</Badge>
              <Badge>{j.location}</Badge>
              <Badge kind="ok">حد نصاب: {j.min_score}</Badge>
            </div>
            <p className="muted">{j.description}</p>
            <Link to={`/jobs/${j.id}/apply`} className="neo-btn neo-btn--primary neo-btn--sm">ثبت درخواست ←</Link>
          </Card>
        ))}
      </div>
    </div>
  );
}

const STEPS = ['اطلاعات فردی', 'سوابق و مهارت‌ها', 'انگیزه', 'نمونه‌کار', 'تأیید و ارسال'];

export function Apply() {
  const { id } = useParams();
  const nav = useNavigate();
  const [job, setJob] = useState(null);
  const [step, setStep] = useState(0);
  const [form, setForm] = useState({ full_name: '', phone: '', email: '', experience: '', skills: '', motivation: '', portfolio: '', agree: '' });
  const [result, setResult] = useState(null);
  const [err, setErr] = useState('');

  useEffect(() => { api('/jobs/' + id).then(setJob).catch((e) => setErr(e.message)); }, [id]);

  const next = () => {
    if (step === 0 && (!form.full_name || !/^09\d{9}$/.test(form.phone))) {
      return setErr('نام و شماره موبایل معتبر لازم است');
    }
    setErr('');
    setStep(step + 1);
  };

  const submit = async () => {
    setErr('');
    try {
      const r = await api('/applications', {
        method: 'POST',
        body: {
          job_id: Number(id),
          full_name: form.full_name,
          phone: form.phone,
          email: form.email,
          answers: {
            experience: form.experience,
            skills: form.skills,
            motivation: form.motivation,
            portfolio: form.portfolio,
            agree: form.agree,
          },
        },
      });
      setResult(r);
    } catch (e) { setErr(e.message); }
  };

  if (!job && !err) return <div className="page center muted">در حال بارگذاری…</div>;
  return (
    <div className="page" style={{ maxWidth: 680, margin: '0 auto' }}>
      <div className="neo-section__head">
        <h2 className="neo-section__title">{job ? job.title : 'درخواست شغلی'}</h2>
        <p className="neo-section__subtitle">فرم ۵ مرحله‌ای — امتیاز شما بلافاصله محاسبه می‌شود</p>
      </div>

      {result ? (
        <Card className="center">
          <div style={{ fontSize: 44 }}>{result.pass ? '🎉' : '📝'}</div>
          <h3>امتیاز شما: {result.score} از ۱۰۰</h3>
          <Alert kind={result.pass ? 'ok' : 'err'}>{result.message}</Alert>
          <div style={{ textAlign: 'right' }}>
            {(result.details || []).map((d, i) => (
              <div key={i} className="spread neo-inset mb" style={{ marginBottom: 8 }}>
                <span style={{ fontSize: 13 }}>{d.label}</span>
                <Badge kind={d.ratio >= 0.6 ? 'ok' : d.ratio > 0 ? 'accent' : 'warn'}>
                  {d.points} / {d.weight}
                </Badge>
              </div>
            ))}
          </div>
          <Link to="/jobs" className="neo-btn">سایر فرصت‌ها</Link>
        </Card>
      ) : (
        <Card>
          <div className="neo-steps">
            {STEPS.map((s, i) => (
              <span key={s} className={i === step ? 'on' : i < step ? 'done' : ''}>{i + 1}. {s}</span>
            ))}
          </div>
          <Alert kind="err">{err}</Alert>

          {step === 0 && (
            <>
              <Field label="نام و نام خانوادگی"><Input value={form.full_name} onChange={(e) => setForm({ ...form, full_name: e.target.value })} /></Field>
              <Field label="شماره موبایل"><Input className="ltr" value={form.phone} onChange={(e) => setForm({ ...form, phone: e.target.value })} /></Field>
              <Field label="ایمیل (اختیاری)"><Input className="ltr" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} /></Field>
            </>
          )}
          {step === 1 && (
            <>
              <Field label="سوابق حرفه‌ای" hint="سال‌ها تجربه، شرکت‌ها، پروژه‌ها"><TextArea value={form.experience} onChange={(e) => setForm({ ...form, experience: e.target.value })} /></Field>
              <Field label="مهارت‌های کلیدی" hint="با کاما جدا کنید — python, tensorflow, NLP…"><Input value={form.skills} onChange={(e) => setForm({ ...form, skills: e.target.value })} /></Field>
            </>
          )}
          {step === 2 && (
            <Field label="چرا پارسی‌نگر؟" hint="انگیزه شما از پیوستن به تیم"><TextArea value={form.motivation} onChange={(e) => setForm({ ...form, motivation: e.target.value })} /></Field>
          )}
          {step === 3 && (
            <Field label="لینک نمونه‌کار / گیت‌هاب / لینکدین"><Input className="ltr" value={form.portfolio} onChange={(e) => setForm({ ...form, portfolio: e.target.value })} /></Field>
          )}
          {step === 4 && (
            <>
              <p className="muted">درخواست شما با اطلاعات زیر ثبت می‌شود و سیستم به‌صورت خودکار امتیازدهی می‌کند:</p>
              <div className="neo-inset mb"><b>{form.full_name}</b> — <span className="ltr">{form.phone}</span></div>
              <Field label="تأیید نهایی" hint="عبارت «تایید» را بنویسید"><Input value={form.agree} onChange={(e) => setForm({ ...form, agree: e.target.value })} placeholder="تایید" /></Field>
            </>
          )}

          <div className="row mt">
            {step > 0 && <Btn onClick={() => setStep(step - 1)}>مرحله قبل</Btn>}
            {step < 4 && <Btn variant="primary" onClick={next}>مرحله بعد</Btn>}
            {step === 4 && <Btn variant="primary" onClick={submit}>🚀 ارسال و دریافت امتیاز</Btn>}
          </div>
        </Card>
      )}
    </div>
  );
}
