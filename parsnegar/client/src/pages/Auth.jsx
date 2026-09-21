import React, { useState } from 'react';
import { useNavigate, Link, useSearchParams } from 'react-router-dom';
import { api, setToken } from '../api';
import { Card, Btn, Field, Input, Segmented, Alert } from '../components/ui';
import { useApp } from '../App';

/** ورود OTP / رمز + ثبت‌نام */
export default function Auth({ mode = 'login' }) {
  const nav = useNavigate();
  const { setUser, meta } = useApp();
  const [isReg, setIsReg] = useState(mode === 'register');
  const [method, setMethod] = useState('otp');
  const [form, setForm] = useState({ name: '', phone: '', password: '', code: '' });
  const [codeSent, setCodeSent] = useState(false);
  const [demoCode, setDemoCode] = useState('');
  const [err, setErr] = useState('');

  const sendOtp = async () => {
    setErr('');
    try {
      const r = await api('/auth/otp/send', { method: 'POST', body: { phone: form.phone } });
      setCodeSent(true);
      setDemoCode(r.demo_code || '');
    } catch (e) { setErr(e.message); }
  };

  const doLogin = async () => {
    setErr('');
    try {
      let r;
      if (isReg && method === 'password') {
        r = await api('/auth/register', { method: 'POST', body: form });
      } else if (method === 'otp') {
        r = await api('/auth/otp/verify', { method: 'POST', body: { phone: form.phone, code: form.code, name: form.name } });
      } else {
        r = await api('/auth/login', { method: 'POST', body: { phone: form.phone, password: form.password } });
      }
      setToken(r.token);
      setUser(r.user);
      nav(r.user.role === 'admin' || r.user.role === 'crm' ? '/cms' : '/panel');
    } catch (e) { setErr(e.message); }
  };

  return (
    <div className="page" style={{ maxWidth: 520, margin: '0 auto' }}>
      <div className="center mb">
        <div className="neo-icon-tile" style={{ margin: '0 auto' }}>🔐</div>
        <h2>{isReg ? 'ثبت‌نام در ' : 'ورود به '}{meta?.site?.name || 'پارسی‌نگر'}</h2>
        <p className="muted">ورود با پیامک (OTP) یا رمز عبور</p>
      </div>
      <Card>
        <Alert kind="err">{err}</Alert>
        {isReg && (
          <Field label="نام و نام خانوادگی">
            <Input value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} />
          </Field>
        )}
        <Field label="شماره موبایل">
          <Input className="ltr" placeholder="09xxxxxxxxx" value={form.phone} onChange={(e) => setForm({ ...form, phone: e.target.value })} />
        </Field>

        <div className="mb">
          <Segmented
            name="method"
            value={method}
            onChange={setMethod}
            options={{ otp: 'کد پیامکی (OTP)', password: 'رمز عبور' }}
          />
        </div>

        {method === 'otp' ? (
          <>
            {!codeSent ? (
              <Btn variant="primary" onClick={sendOtp}>📲 دریافت کد تایید</Btn>
            ) : (
              <>
                {demoCode && <Alert kind="ok">کد تایید (حالت نمایشی): <b className="ltr">{demoCode}</b></Alert>}
                <Field label="کد تایید">
                  <Input className="ltr" value={form.code} onChange={(e) => setForm({ ...form, code: e.target.value })} />
                </Field>
                <div className="row">
                  <Btn variant="primary" onClick={doLogin}>ورود</Btn>
                  <Btn variant="ghost" size="sm" onClick={sendOtp}>ارسال مجدد</Btn>
                </div>
              </>
            )}
          </>
        ) : (
          <>
            <Field label="رمز عبور">
              <Input className="ltr" type="password" value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} />
            </Field>
            <Btn variant="primary" onClick={doLogin}>{isReg ? 'ثبت‌نام' : 'ورود'}</Btn>
          </>
        )}

        <div className="center mt">
          <Btn variant="ghost" size="sm" onClick={() => setIsReg(!isReg)}>
            {isReg ? 'قبلاً ثبت‌نام کرده‌اید؟ ورود' : 'حساب ندارید؟ ثبت‌نام'}
          </Btn>
          <div className="muted" style={{ fontSize: 11 }}>
            حساب نمایشی مدیر: 09123456789 / admin1234 — مشتری: 09351112233 / demo1234
          </div>
        </div>
      </Card>
    </div>
  );
}
