import React, { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { api, faMoney, faStatus } from '../api';
import { getCart, setCart, useApp } from '../App';
import { Card, Btn, Badge, Alert } from '../components/ui';

export function Shop() {
  const [products, setProducts] = useState([]);
  useEffect(() => { api('/products').then(setProducts).catch(() => {}); }, []);
  const add = (p) => {
    const cart = getCart();
    const price = p.sale_price ?? p.price;
    const found = cart.find((i) => i.product_id === p.id);
    if (found) found.qty += 1;
    else cart.push({ product_id: p.id, title: p.title, price, qty: 1 });
    setCart(cart);
  };
  return (
    <div className="page">
      <div className="neo-section__head">
        <h2 className="neo-section__title">فروشگاه پارسی‌نگر</h2>
        <p className="neo-section__subtitle">اشتراک‌ها و بسته‌های خدمات هوشمند</p>
      </div>
      <div className="neo-grid neo-grid--3">
        {products.map((p) => (
          <Card key={p.id}>
            <h3>{p.title}</h3>
            <p className="muted">{p.short_desc}</p>
            <div className="spread">
              <div>
                <b style={{ color: 'var(--neo-accent)', fontSize: 17 }}>{faMoney(p.sale_price ?? p.price)}</b>
                {p.sale_price != null && <div className="muted" style={{ textDecoration: 'line-through', fontSize: 11 }}>{faMoney(p.price)}</div>}
              </div>
              <div className="row">
                <Link to={'/shop/' + p.slug} className="neo-btn neo-btn--sm">جزئیات</Link>
                <Btn size="sm" variant="primary" onClick={() => add(p)}>افزودن</Btn>
              </div>
            </div>
          </Card>
        ))}
      </div>
    </div>
  );
}

export function Product() {
  const { slug } = useParams();
  const [p, setP] = useState(null);
  useEffect(() => { api('/products/' + slug).then(setP).catch(() => {}); }, [slug]);
  if (!p) return <div className="page center muted">در حال بارگذاری…</div>;
  const add = () => {
    const cart = getCart();
    const price = p.sale_price ?? p.price;
    const found = cart.find((i) => i.product_id === p.id);
    if (found) found.qty += 1;
    else cart.push({ product_id: p.id, title: p.title, price, qty: 1 });
    setCart(cart);
  };
  return (
    <div className="page">
      <Card style={{ maxWidth: 780, margin: '0 auto' }}>
        <h1>{p.title}</h1>
        <div className="row mb">
          <Badge kind="accent">{faMoney(p.sale_price ?? p.price)}</Badge>
          {p.stock > 0 ? <Badge kind="ok">موجود: {p.stock.toLocaleString('fa-IR')}</Badge> : <Badge kind="warn">ناموجود</Badge>}
        </div>
        <p className="muted">{p.short_desc}</p>
        <p style={{ whiteSpace: 'pre-line' }}>{p.body}</p>
        <div className="row mt">
          <Btn variant="primary" onClick={add}>🛒 افزودن به سبد</Btn>
          <Link to="/shop" className="neo-btn neo-btn--ghost">بازگشت به فروشگاه</Link>
        </div>
      </Card>
    </div>
  );
}

export function Cart() {
  const { user } = useApp();
  const nav = useNavigate();
  const [cart, setCartState] = useState(getCart());
  const [msg, setMsg] = useState('');
  const [err, setErr] = useState('');
  const [usePoints, setUsePoints] = useState(0);
  const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);

  const setQty = (idx, qty) => {
    const next = cart.map((i, k) => (k === idx ? { ...i, qty: Math.max(1, qty) } : i));
    setCartState(next);
    setCart(next);
  };
  const remove = (idx) => {
    const next = cart.filter((_, k) => k !== idx);
    setCartState(next);
    setCart(next);
  };

  const checkout = async () => {
    setErr('');
    if (!user) return nav('/login');
    try {
      const r = await api('/my/checkout', {
        method: 'POST',
        body: { cart: cart.map((i) => ({ product_id: i.product_id, qty: i.qty })), use_points: usePoints },
      });
      setCart([]);
      setCartState([]);
      setMsg('سفارش ثبت شد! در حال انتقال به لینک پرداخت…');
      setTimeout(() => nav('/pay/' + r.invoiceToken), 1200);
    } catch (e) {
      setErr(e.message);
    }
  };

  return (
    <div className="page" style={{ maxWidth: 720, margin: '0 auto' }}>
      <div className="neo-section__head"><h2 className="neo-section__title">🛒 سبد خرید</h2></div>
      <Alert kind="ok">{msg}</Alert>
      <Alert kind="err">{err}</Alert>
      <Card>
        {cart.map((i, idx) => (
          <div key={i.product_id} className="spread neo-inset mb">
            <div>
              <b>{i.title}</b>
              <div className="muted" style={{ fontSize: 12 }}>{faMoney(i.price)}</div>
            </div>
            <div className="row">
              <input className="neo-input" style={{ width: 70 }} type="number" min="1" value={i.qty} onChange={(e) => setQty(idx, Number(e.target.value))} />
              <Btn size="sm" variant="danger" onClick={() => remove(idx)}>✕</Btn>
            </div>
          </div>
        ))}
        {!cart.length && !msg && <p className="center muted">سبد خرید شما خالی است</p>}
        {!!cart.length && (
          <>
            <div className="spread mb">
              <span>کسر امتیاز وفاداری (هر ۱۰۰ امتیاز = ۱۰٬۰۰۰ تومان)</span>
              <input className="neo-input" style={{ width: 110 }} type="number" min="0" value={usePoints} onChange={(e) => setUsePoints(Number(e.target.value))} />
            </div>
            <div className="spread">
              <span className="big">جمع: {faMoney(subtotal - Math.min(usePoints * 100, subtotal))}</span>
              <Btn variant="primary" onClick={checkout}>{user ? 'ثبت سفارش و پرداخت' : 'ورود و ادامه'}</Btn>
            </div>
          </>
        )}
      </Card>
    </div>
  );
}
