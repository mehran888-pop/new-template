import React, { useEffect, useState, createContext, useContext } from 'react';
import { Routes, Route, NavLink, Link, useNavigate, Navigate } from 'react-router-dom';
import { api, getToken, setToken, faMoney } from './api';
import Home from './pages/Home';
import { Blog, Post } from './pages/Content';
import { Shop, Product, Cart } from './pages/Shop';
import Auth from './pages/Auth';
import { Jobs, Apply } from './pages/Careers';
import Pay from './pages/Pay';
import Panel from './pages/Panel';
import Cms from './pages/Cms';

/* ── Session + cart stores ── */
const Ctx = createContext({});
export const useApp = () => useContext(Ctx);

const CART_KEY = 'pn_cart';
export const getCart = () => JSON.parse(localStorage.getItem(CART_KEY) || '[]');
export const setCart = (c) => {
  localStorage.setItem(CART_KEY, JSON.stringify(c));
  window.dispatchEvent(new Event('pn-cart'));
};

export default function App() {
  const [user, setUser] = useState(null);
  const [meta, setMeta] = useState(null);
  const [cartCount, setCartCount] = useState(getCart().reduce((s, i) => s + i.qty, 0));

  useEffect(() => {
    api('/meta').then(setMeta).catch(() => {});
    if (getToken()) api('/auth/me').then((r) => setUser(r.user)).catch(() => setToken(''));
  }, []);
  useEffect(() => {
    const h = () => setCartCount(getCart().reduce((s, i) => s + i.qty, 0));
    window.addEventListener('pn-cart', h);
    return () => window.removeEventListener('pn-cart', h);
  }, []);

  // Apply theme from CMS settings.
  useEffect(() => {
    if (!meta?.theme) return;
    const t = meta.theme;
    const r = document.documentElement.style;
    if (t.color_bg) r.setProperty('--neo-bg', t.color_bg);
    if (t.color_text) r.setProperty('--neo-text', t.color_text);
    if (t.color_muted) r.setProperty('--neo-muted', t.color_muted);
    if (t.color_accent) r.setProperty('--neo-accent', t.color_accent);
    if (t.color_accent2) r.setProperty('--neo-accent-2', t.color_accent2);
    if (t.radius) r.setProperty('--neo-radius', t.radius + 'px');
    if (t.distance) r.setProperty('--neo-distance', t.distance + 'px');
    if (t.container_width) r.setProperty('--neo-container', t.container_width + 'px');
  }, [meta]);

  const logout = () => {
    api('/auth/logout', { method: 'POST' }).catch(() => {});
    setToken('');
    setUser(null);
  };

  return (
    <Ctx.Provider value={{ user, setUser, meta, logout }}>
      <header className="neo-header">
        <div className="container neo-header__inner">
          <Link to="/" className="neo-brand">
            <span className="neo-brand__logo">🧠</span>
            <span>
              {meta?.site?.name || 'هوش یار پارسی نگر'}
              <div className="muted" style={{ fontSize: 10.5, fontWeight: 600 }}>{meta?.site?.tagline}</div>
            </span>
          </Link>
          <nav className="neo-nav">
            <NavLink to="/" end>خانه</NavLink>
            <NavLink to="/blog">وبلاگ</NavLink>
            <NavLink to="/shop">فروشگاه</NavLink>
            <NavLink to="/jobs">فرصت‌های شغلی</NavLink>
            {user && <NavLink to="/panel">پنل من</NavLink>}
            {user && (user.role === 'admin' || user.role === 'crm') && <NavLink to="/cms">مدیریت</NavLink>}
          </nav>
          <div className="row">
            <Link to="/cart" className="neo-btn neo-btn--sm">🛒 سبد ({cartCount})</Link>
            {user ? (
              <button className="neo-btn neo-btn--sm" onClick={logout}>خروج ({user.name || user.phone})</button>
            ) : (
              <Link to="/login" className="neo-btn neo-btn--sm neo-btn--primary">ورود / ثبت‌نام</Link>
            )}
          </div>
        </div>
      </header>

      <main className="container">
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/blog" element={<Blog />} />
          <Route path="/blog/:slug" element={<Post />} />
          <Route path="/shop" element={<Shop />} />
          <Route path="/shop/:slug" element={<Product />} />
          <Route path="/cart" element={<Cart />} />
          <Route path="/login" element={<Auth />} />
          <Route path="/register" element={<Auth mode="register" />} />
          <Route path="/jobs" element={<Jobs />} />
          <Route path="/jobs/:id/apply" element={<Apply />} />
          <Route path="/pay/:token" element={<Pay />} />
          <Route path="/panel/*" element={<Panel />} />
          <Route path="/cms/*" element={<Cms />} />
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </main>

      <footer className="neo-footer">
        <div className="container neo-footer__grid">
          <div>
            <div className="neo-brand mb"><span className="neo-brand__logo">🧠</span> {meta?.site?.name || 'هوش یار پارسی نگر'}</div>
            <p className="muted">{meta?.site?.tagline}</p>
            <div>
              {(meta?.socials || []).map((s) => (
                <a key={s.url} className="neo-chip" href={s.url} target="_blank" rel="noreferrer">{s.label}</a>
              ))}
            </div>
          </div>
          <div>
            <b>دسترسی سریع</b>
            <p><Link to="/shop">فروشگاه</Link></p>
            <p><Link to="/blog">وبلاگ</Link></p>
            <p><Link to="/jobs">فرصت‌های شغلی</Link></p>
          </div>
          <div>
            <b>تماس</b>
            <p className="muted">{meta?.contact?.phone}</p>
            <p className="muted ltr">{meta?.contact?.email}</p>
            <p className="muted">{meta?.contact?.address}</p>
          </div>
        </div>
      </footer>
    </Ctx.Provider>
  );
}
