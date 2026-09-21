import React, { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { api, faMoney, faDate, getToken } from '../api';
import { Card, Btn, Alert, Badge } from '../components/ui';

/** لینک پرداخت عمومی فاکتور: /pay/:token */
export default function Pay() {
  const { token } = useParams();
  const nav = useNavigate();
  const [inv, setInv] = useState(null);
  const [msg, setMsg] = useState('');
  const [err, setErr] = useState('');

  useEffect(() => {
    // Public summary first (no auth), then full if logged in.
    api('/my/pay/' + token).then(setInv).catch((e) => setErr(e.message));
  }, [token]);

  const pay = async (gateway) => {
    setErr('');
    if (!getToken()) return nav('/login');
    try {
      const r = await api('/my/invoices/' + token + '/pay', { method: 'POST', body: { gateway } });
      setMsg('✅ پرداخت با موفقیت انجام شد — کد پیگیری: ' + r.ref);
      setInv({ ...inv, status: 'paid' });
    } catch (e) { setErr(e.message); }
  };

  if (err && !inv) return <div className="page center muted">{err}</div>;
  if (!inv) return <div className="page center muted">در حال بارگذاری…</div>;

  return (
    <div className="page" style={{ maxWidth: 560, margin: '0 auto' }}>
      <div className="center mb">
        <div className="neo-icon-tile" style={{ margin: '0 auto' }}>💳</div>
        <h2>پرداخت فاکتور {inv.number}</h2>
      </div>
      <Card>
        <Alert kind="ok">{msg}</Alert>
        <Alert kind="err">{err}</Alert>
        <div className="spread mb"><span className="muted">عنوان</span><b>{inv.title}</b></div>
        <div className="spread mb"><span className="muted">تاریخ صدور</span><span>{faDate(inv.created_at)}</span></div>
        <div className="spread mb"><span className="muted">مهلت پرداخت</span><span>{faDate(inv.due_at)}</span></div>
        <div className="spread mb"><span className="muted">وضعیت</span>
          <Badge kind={inv.status === 'paid' ? 'ok' : 'warn'}>{inv.status === 'paid' ? 'پرداخت‌شده' : 'در انتظار پرداخت'}</Badge>
        </div>
        <div className="neo-inset spread mb" style={{ fontSize: 18 }}>
          <b>مبلغ قابل پرداخت</b>
          <b style={{ color: 'var(--neo-accent)' }}>{faMoney(inv.total)}</b>
        </div>
        {inv.status !== 'paid' && (
          <div className="row">
            <Btn variant="primary" onClick={() => pay('zarinpal')}>پرداخت با زرین‌پال</Btn>
            <Btn onClick={() => pay('idpay')}>آیدی‌پی</Btn>
            <Btn onClick={() => pay('wallet')}>کیف پول</Btn>
          </div>
        )}
      </Card>
    </div>
  );
}
