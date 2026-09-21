-- ============================================================================
-- هوش یار پارسی نگر — اختصاصی CMS Database
-- Professional normalized schema: one table per entity, FKs, indexes, timestamps
-- ============================================================================

PRAGMA foreign_keys = ON;

-- ── Identity & Access ───────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  name          TEXT NOT NULL DEFAULT '',
  phone         TEXT NOT NULL UNIQUE,
  email         TEXT UNIQUE,
  password_hash TEXT,                          -- scrypt salt:hash
  role          TEXT NOT NULL DEFAULT 'customer' CHECK (role IN ('customer','crm','admin')),
  points_balance INTEGER NOT NULL DEFAULT 0,
  status        TEXT NOT NULL DEFAULT 'active' CHECK (status IN ('active','blocked')),
  created_at    TEXT NOT NULL DEFAULT (datetime('now')),
  updated_at    TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS user_profiles (
  user_id        INTEGER PRIMARY KEY REFERENCES users(id) ON DELETE CASCADE,
  company        TEXT DEFAULT '',
  birthday       TEXT DEFAULT '',
  national_code  TEXT DEFAULT '',
  city           TEXT DEFAULT '',
  address        TEXT DEFAULT '',
  avatar_media_id INTEGER REFERENCES media(id) ON DELETE SET NULL,
  phone_verified INTEGER NOT NULL DEFAULT 0,
  bale_chat_id   TEXT DEFAULT '',
  telegram_chat_id TEXT DEFAULT '',
  crm_note       TEXT DEFAULT '',
  last_activity  TEXT
);

CREATE TABLE IF NOT EXISTS media (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  file_name  TEXT NOT NULL,
  url        TEXT NOT NULL,
  alt        TEXT DEFAULT '',
  mime       TEXT DEFAULT '',
  size       INTEGER DEFAULT 0,
  created_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS otp_codes (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  phone      TEXT NOT NULL,
  code       TEXT NOT NULL,
  channel    TEXT NOT NULL DEFAULT 'sms' CHECK (channel IN ('sms','sms_ir','melipayamak','bale','telegram','email')),
  expires_at TEXT NOT NULL,
  used_at    TEXT,
  attempts   INTEGER NOT NULL DEFAULT 0,
  request_ip TEXT DEFAULT '',
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_otp_phone ON otp_codes(phone, used_at);

CREATE TABLE IF NOT EXISTS sessions (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  token_hash TEXT NOT NULL UNIQUE,
  user_agent TEXT DEFAULT '',
  expires_at TEXT NOT NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_sessions_user ON sessions(user_id);

-- ── Content ─────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
  id        INTEGER PRIMARY KEY AUTOINCREMENT,
  name      TEXT NOT NULL,
  slug      TEXT NOT NULL UNIQUE,
  parent_id INTEGER REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS posts (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  title         TEXT NOT NULL,
  slug          TEXT NOT NULL UNIQUE,
  excerpt       TEXT DEFAULT '',
  body          TEXT DEFAULT '',
  category_id   INTEGER REFERENCES categories(id) ON DELETE SET NULL,
  cover_media_id INTEGER REFERENCES media(id) ON DELETE SET NULL,
  author_id     INTEGER REFERENCES users(id) ON DELETE SET NULL,
  status        TEXT NOT NULL DEFAULT 'draft' CHECK (status IN ('draft','published')),
  published_at  TEXT,
  created_at    TEXT NOT NULL DEFAULT (datetime('now')),
  updated_at    TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_posts_status ON posts(status, published_at);

CREATE TABLE IF NOT EXISTS services (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  title         TEXT NOT NULL,
  slug          TEXT NOT NULL UNIQUE,
  icon_text     TEXT DEFAULT '✦',
  icon_media_id INTEGER REFERENCES media(id) ON DELETE SET NULL,
  short_desc    TEXT DEFAULT '',
  body          TEXT DEFAULT '',
  link          TEXT DEFAULT '',
  menu_order    INTEGER NOT NULL DEFAULT 0,
  is_active     INTEGER NOT NULL DEFAULT 1,
  created_at    TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS service_features (
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  service_id  INTEGER NOT NULL REFERENCES services(id) ON DELETE CASCADE,
  title       TEXT NOT NULL DEFAULT '',
  description TEXT DEFAULT '',
  sort        INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_sf_service ON service_features(service_id);

CREATE TABLE IF NOT EXISTS projects (
  id             INTEGER PRIMARY KEY AUTOINCREMENT,
  title          TEXT NOT NULL,
  slug           TEXT NOT NULL UNIQUE,
  client         TEXT DEFAULT '',
  year           TEXT DEFAULT '',
  project_url    TEXT DEFAULT '',
  status         TEXT NOT NULL DEFAULT 'completed' CHECK (status IN ('completed','ongoing','showcase')),
  featured       INTEGER NOT NULL DEFAULT 0,
  body           TEXT DEFAULT '',
  excerpt        TEXT DEFAULT '',
  cover_media_id INTEGER REFERENCES media(id) ON DELETE SET NULL,
  menu_order     INTEGER NOT NULL DEFAULT 0,
  created_at     TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS project_highlights (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  project_id INTEGER NOT NULL REFERENCES projects(id) ON DELETE CASCADE,
  text       TEXT NOT NULL DEFAULT '',
  sort       INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_ph_project ON project_highlights(project_id);

CREATE TABLE IF NOT EXISTS team_members (
  id             INTEGER PRIMARY KEY AUTOINCREMENT,
  name           TEXT NOT NULL,
  role_title     TEXT DEFAULT '',
  bio            TEXT DEFAULT '',
  photo_media_id INTEGER REFERENCES media(id) ON DELETE SET NULL,
  socials_json   TEXT DEFAULT '[]',
  sort           INTEGER NOT NULL DEFAULT 0,
  created_at     TEXT NOT NULL DEFAULT (datetime('now'))
);

-- ── Commerce ────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS products (
  id             INTEGER PRIMARY KEY AUTOINCREMENT,
  title          TEXT NOT NULL,
  slug           TEXT NOT NULL UNIQUE,
  price          INTEGER NOT NULL DEFAULT 0,     -- تومان
  sale_price     INTEGER,
  stock          INTEGER NOT NULL DEFAULT 0,
  short_desc     TEXT DEFAULT '',
  body           TEXT DEFAULT '',
  cover_media_id INTEGER REFERENCES media(id) ON DELETE SET NULL,
  status         TEXT NOT NULL DEFAULT 'publish' CHECK (status IN ('draft','publish')),
  created_at     TEXT NOT NULL DEFAULT (datetime('now')),
  updated_at     TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS orders (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  status     TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending','paid','shipped','completed','cancelled')),
  subtotal   INTEGER NOT NULL DEFAULT 0,
  discount   INTEGER NOT NULL DEFAULT 0,
  total      INTEGER NOT NULL DEFAULT 0,
  note       TEXT DEFAULT '',
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  paid_at    TEXT
);
CREATE INDEX IF NOT EXISTS idx_orders_user ON orders(user_id, created_at);

CREATE TABLE IF NOT EXISTS order_items (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  order_id   INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
  product_id INTEGER REFERENCES products(id) ON DELETE SET NULL,
  title      TEXT NOT NULL DEFAULT '',
  qty        INTEGER NOT NULL DEFAULT 1,
  unit_price INTEGER NOT NULL DEFAULT 0,
  line_total INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_oi_order ON order_items(order_id);

CREATE TABLE IF NOT EXISTS invoices (
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  number      TEXT NOT NULL UNIQUE,             -- NMC-1001 / PN-1001
  user_id     INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  token       TEXT NOT NULL UNIQUE,             -- payment-link token
  status      TEXT NOT NULL DEFAULT 'unpaid' CHECK (status IN ('draft','unpaid','paid','cancelled','expired')),
  title       TEXT DEFAULT '',
  subtotal    INTEGER NOT NULL DEFAULT 0,
  discount    INTEGER NOT NULL DEFAULT 0,
  tax         INTEGER NOT NULL DEFAULT 0,
  total       INTEGER NOT NULL DEFAULT 0,
  due_at      TEXT,
  paid_at     TEXT,
  gateway     TEXT DEFAULT '',
  gateway_ref TEXT DEFAULT '',
  note        TEXT DEFAULT '',
  created_at  TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_invoices_user ON invoices(user_id, created_at);

CREATE TABLE IF NOT EXISTS invoice_items (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  invoice_id INTEGER NOT NULL REFERENCES invoices(id) ON DELETE CASCADE,
  title      TEXT NOT NULL DEFAULT '',
  qty        INTEGER NOT NULL DEFAULT 1,
  unit_price INTEGER NOT NULL DEFAULT 0,
  line_total INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_ii_invoice ON invoice_items(invoice_id);

CREATE TABLE IF NOT EXISTS payments (
  id           INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id      INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  invoice_id   INTEGER REFERENCES invoices(id) ON DELETE SET NULL,
  order_id     INTEGER REFERENCES orders(id) ON DELETE SET NULL,
  gateway      TEXT NOT NULL DEFAULT 'zarinpal' CHECK (gateway IN ('zarinpal','idpay','wallet')),
  amount       INTEGER NOT NULL DEFAULT 0,
  ref_id       TEXT DEFAULT '',
  status       TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending','ok','failed')),
  created_at   TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_payments_user ON payments(user_id);

-- ── Support & Requests ──────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS tickets (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  subject    TEXT NOT NULL,
  department TEXT NOT NULL DEFAULT 'general' CHECK (department IN ('general','sales','support','finance')),
  status     TEXT NOT NULL DEFAULT 'open' CHECK (status IN ('open','answered','closed')),
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_tickets_user ON tickets(user_id, status);

CREATE TABLE IF NOT EXISTS ticket_messages (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  ticket_id  INTEGER NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
  user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  is_staff   INTEGER NOT NULL DEFAULT 0,
  body       TEXT NOT NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_tm_ticket ON ticket_messages(ticket_id, created_at);

CREATE TABLE IF NOT EXISTS consultations (
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id     INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  type        TEXT NOT NULL DEFAULT 'consult' CHECK (type IN ('consult','support','quote')),
  subject     TEXT NOT NULL,
  body        TEXT DEFAULT '',
  answer      TEXT DEFAULT '',
  status      TEXT NOT NULL DEFAULT 'new' CHECK (status IN ('new','answered','closed')),
  created_at  TEXT NOT NULL DEFAULT (datetime('now')),
  answered_at TEXT
);
CREATE INDEX IF NOT EXISTS idx_consults_user ON consultations(user_id, status);

-- ── Loyalty Club ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS loyalty_transactions (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id       INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  delta         INTEGER NOT NULL,               -- +earn / -redeem
  balance_after INTEGER NOT NULL DEFAULT 0,
  type          TEXT NOT NULL DEFAULT 'earn' CHECK (type IN ('earn','redeem','expire','adjust')),
  ref_table     TEXT DEFAULT '',
  ref_id        INTEGER,
  description   TEXT DEFAULT '',
  created_at    TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_lt_user ON loyalty_transactions(user_id, created_at);

CREATE TABLE IF NOT EXISTS loyalty_rewards (
  id        INTEGER PRIMARY KEY AUTOINCREMENT,
  title     TEXT NOT NULL,
  cost      INTEGER NOT NULL,
  kind      TEXT NOT NULL DEFAULT 'percent' CHECK (kind IN ('percent','fixed','shipping')),
  value     INTEGER NOT NULL DEFAULT 0,
  is_active INTEGER NOT NULL DEFAULT 1,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

-- ── CRM ─────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS segments (
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  title       TEXT NOT NULL,
  description TEXT DEFAULT '',
  color       TEXT DEFAULT '#6c5ce7',
  created_at  TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS user_segments (
  user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  segment_id INTEGER NOT NULL REFERENCES segments(id) ON DELETE CASCADE,
  rule_id    INTEGER,
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  PRIMARY KEY (user_id, segment_id)
);

CREATE TABLE IF NOT EXISTS user_services (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  service_id INTEGER NOT NULL REFERENCES services(id) ON DELETE CASCADE,
  status     TEXT NOT NULL DEFAULT 'interested' CHECK (status IN ('interested','purchased','active')),
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  UNIQUE (user_id, service_id)
);

-- ── Careers (multi-step recruitment + auto scoring) ─────────────────────────
CREATE TABLE IF NOT EXISTS jobs (
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  title       TEXT NOT NULL,
  department  TEXT DEFAULT '',
  job_type    TEXT NOT NULL DEFAULT 'full-time',
  location    TEXT DEFAULT '',
  description TEXT DEFAULT '',
  min_score   INTEGER NOT NULL DEFAULT 70,      -- ≥ → مرحله مصاحبه ویدیویی
  status      TEXT NOT NULL DEFAULT 'open' CHECK (status IN ('open','closed')),
  created_at  TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS job_criteria (
  id       INTEGER PRIMARY KEY AUTOINCREMENT,
  job_id   INTEGER NOT NULL REFERENCES jobs(id) ON DELETE CASCADE,
  label    TEXT NOT NULL,
  weight   INTEGER NOT NULL DEFAULT 1,
  keywords TEXT DEFAULT '',                     -- جداشده با کاما
  sort     INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_jc_job ON job_criteria(job_id);

CREATE TABLE IF NOT EXISTS applications (
  id                INTEGER PRIMARY KEY AUTOINCREMENT,
  job_id            INTEGER NOT NULL REFERENCES jobs(id) ON DELETE CASCADE,
  full_name         TEXT NOT NULL,
  phone             TEXT NOT NULL,
  email             TEXT DEFAULT '',
  resume_media_id   INTEGER REFERENCES media(id) ON DELETE SET NULL,
  answers_json      TEXT DEFAULT '{}',          -- مرحله‌های فرم چندمرحله‌ای
  score             REAL NOT NULL DEFAULT 0,
  score_details_json TEXT DEFAULT '[]',
  stage             TEXT NOT NULL DEFAULT 'applied'
                    CHECK (stage IN ('applied','screening','video','test','interview','hired','rejected')),
  created_at        TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_apps_job ON applications(job_id, score);

-- ── Automation & Notifications ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS automation_rules (
  id           INTEGER PRIMARY KEY AUTOINCREMENT,
  title        TEXT NOT NULL,
  event_name   TEXT NOT NULL,                   -- user_registered|order_paid|invoice_paid|ticket_created|application_received|loyalty_redeemed
  condition_field TEXT DEFAULT '',              -- مثال: amount
  condition_op    TEXT DEFAULT 'gte',           -- gte|lte|eq|contains
  condition_value TEXT DEFAULT '',
  channel      TEXT NOT NULL DEFAULT 'sms'      -- sms_ir|melipayamak|bale|telegram|email|add_segment|add_points
                CHECK (channel IN ('sms_ir','melipayamak','bale','telegram','email','add_segment','add_points')),
  target       TEXT DEFAULT '',                 -- segment id / points مقدار / ...
  template     TEXT DEFAULT '',                -- توکن‌ها: {name} {phone} {amount} {invoice} {title} {balance} {site}
  is_active    INTEGER NOT NULL DEFAULT 1,
  created_at   TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS automation_logs (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  rule_id    INTEGER REFERENCES automation_rules(id) ON DELETE SET NULL,
  event_name TEXT NOT NULL,
  payload_json TEXT DEFAULT '{}',
  result     TEXT DEFAULT '',
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_al_rule ON automation_logs(rule_id, created_at);

CREATE TABLE IF NOT EXISTS notification_outbox (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  channel       TEXT NOT NULL CHECK (channel IN ('sms_ir','melipayamak','bale','telegram','email')),
  recipient     TEXT NOT NULL,
  message       TEXT NOT NULL,
  status        TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending','sent','failed')),
  provider_ref  TEXT DEFAULT '',
  error         TEXT DEFAULT '',
  created_at    TEXT NOT NULL DEFAULT (datetime('now')),
  sent_at       TEXT
);
CREATE INDEX IF NOT EXISTS idx_outbox_status ON notification_outbox(status, created_at);

-- ── Settings (theme + integrations + otp) ───────────────────────────────────
CREATE TABLE IF NOT EXISTS settings (
  key        TEXT PRIMARY KEY,
  value_json TEXT NOT NULL,
  updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);
