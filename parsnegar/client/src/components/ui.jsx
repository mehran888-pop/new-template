/**
 * UI kit — نئومورفیک: کارت، فیلد، کلید/سوئیچ، سگمنت، اسلایدر، جدول، مودال
 */
import React, { useState } from 'react';

export const Card = ({ children, className = '', ...p }) => (
  <div className={'neo-card ' + className} {...p}>{children}</div>
);

export const Field = ({ label, hint, children, wide }) => (
  <div className={'neo-field' + (wide ? ' mb' : '')}>
    {label && <label className="neo-field__label">{label}</label>}
    {children}
    {hint && <span className="neo-field__hint">{hint}</span>}
  </div>
);

export const Input = (p) => <input className={'neo-input ' + (p.className || '')} {...p} />;
export const TextArea = (p) => <textarea className={'neo-textarea ' + (p.className || '')} {...p} />;
export const Select = ({ options = [], value, onChange, ...p }) => (
  <div className="neo-select">
    <select value={value} onChange={onChange} {...p}>
      {Object.entries(options).map(([v, l]) => <option key={v} value={v}>{l}</option>)}
    </select>
  </div>
);

export const Btn = ({ children, variant = '', size = '', className = '', ...p }) => (
  <button className={`neo-btn ${variant ? 'neo-btn--' + variant : ''} ${size ? 'neo-btn--' + size : ''} ${className}`} {...p}>{children}</button>
);

export const Toggle = ({ value, onChange, labels = ['فعال', 'غیرفعال'], name }) => (
  <div className="neo-switch" role="radiogroup">
    {[1, 0].map((v, i) => (
      <label className="neo-switch__opt" key={v}>
        <input type="radio" name={name} checked={Number(value) === v} onChange={() => onChange(v)} />
        <span>{labels[i]}</span>
      </label>
    ))}
  </div>
);

export const Segmented = ({ value, onChange, options, name }) => (
  <div className="neo-seg" role="radiogroup">
    {Object.entries(options).map(([v, l]) => (
      <label className="neo-seg__opt" key={v}>
        <input type="radio" name={name} checked={String(value) === String(v)} onChange={() => onChange(v)} />
        <span>{l}</span>
      </label>
    ))}
  </div>
);

export const Slider = ({ value, onChange, min = 0, max = 100, step = 1, unit = '' }) => (
  <div className="neo-slider">
    <input type="range" min={min} max={max} step={step} value={value} onChange={(e) => onChange(Number(e.target.value))} />
    <input type="number" min={min} max={max} step={step} value={value} onChange={(e) => onChange(Number(e.target.value))} />
    {unit && <span className="muted" style={{ fontSize: 11 }}>{unit}</span>}
  </div>
);

export const Color = ({ value, onChange }) => (
  <div className="neo-color">
    <span className="neo-color__chip" style={{ background: value }} />
    <input type="color" value={value} onChange={(e) => onChange(e.target.value)} />
    <code className="muted ltr">{value}</code>
  </div>
);

export const Badge = ({ children, kind = '' }) => (
  <span className={'neo-badge ' + (kind ? 'neo-badge--' + kind : '')}>{children}</span>
);

export const Alert = ({ kind = 'ok', children }) => children ? (
  <div className={'neo-alert neo-alert--' + kind + ' mb'}>{children}</div>
) : null;

export const Stat = ({ label, value }) => (
  <div className="neo-inset neo-stat"><b>{value}</b><span>{label}</span></div>
);

export function Table({ columns, rows, renderActions }) {
  return (
    <div style={{ overflowX: 'auto' }}>
      <table className="neo-table">
        <thead>
          <tr>
            {columns.map((c) => <th key={c.key}>{c.label}</th>)}
            {renderActions && <th />}
          </tr>
        </thead>
        <tbody>
          {rows.map((row) => (
            <tr key={row.id}>
              {columns.map((c) => <td key={c.key}>{c.render ? c.render(row) : (row[c.key] ?? '—')}</td>)}
              {renderActions && <td>{renderActions(row)}</td>}
            </tr>
          ))}
          {!rows.length && <tr><td colSpan={columns.length + 1} className="muted center">موردی یافت نشد</td></tr>}
        </tbody>
      </table>
    </div>
  );
}

export function Modal({ title, onClose, children }) {
  return (
    <div className="neo-modal-bg" onClick={onClose}>
      <div className="neo-modal neo-card" onClick={(e) => e.stopPropagation()}>
        <div className="spread mb">
          <h3 style={{ margin: 0 }}>{title}</h3>
          <Btn size="sm" onClick={onClose}>✕</Btn>
        </div>
        {children}
      </div>
    </div>
  );
}

/** Repeatable row list editor: rows=[{...}], fields per row */
export function Repeater({ rows, onChange, fields, addLabel = '＋ افزودن ردیف', blank = {} }) {
  const set = (i, k, v) => {
    const next = rows.map((r, idx) => (idx === i ? { ...r, [k]: v } : r));
    onChange(next);
  };
  return (
    <div>
      <div style={{ display: 'grid', gap: 10 }}>
        {rows.map((row, i) => (
          <div key={i} className="neo-inset row" style={{ flexWrap: 'wrap' }}>
            {fields.map((f) => (
              <div key={f.key} style={{ flex: f.wide ? '1 1 100%' : '1 1 180px' }}>
                <input
                  className="neo-input"
                  placeholder={f.label}
                  value={row[f.key] ?? ''}
                  onChange={(e) => set(i, f.key, e.target.value)}
                />
              </div>
            ))}
            <Btn size="sm" variant="danger" onClick={() => onChange(rows.filter((_, idx) => idx !== i))}>✕</Btn>
          </div>
        ))}
      </div>
      <div className="mt">
        <Btn size="sm" onClick={() => onChange([...rows, { ...blank }])}>{addLabel}</Btn>
      </div>
    </div>
  );
}
