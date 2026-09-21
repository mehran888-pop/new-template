'use strict';
/**
 * هوش یار پارسی نگر — API + CMS server
 * Serves the built React client (client/dist) and the JSON API.
 */
const path = require('path');
const fs = require('fs');
const express = require('express');

const app = express();
app.disable('x-powered-by');
app.use(express.json({ limit: '2mb' }));

// Basic security headers for the preview/proxy environment.
app.use((req, res, next) => {
  res.setHeader('X-Content-Type-Options', 'nosniff');
  next();
});

// Routers
app.use('/api/auth', require('./routes/auth').router);
app.use('/api', require('./routes/public'));
app.use('/api/my', require('./routes/panel'));
app.use('/api/applications', require('./routes/applications'));
app.use('/api/cms', require('./routes/cms'));

app.get('/api/health', (req, res) => res.json({ ok: true, app: 'هوش یار پارسی نگر', time: new Date().toISOString() }));

// Static React client (production build)
const dist = path.join(__dirname, '..', '..', 'client', 'dist');
if (fs.existsSync(dist)) {
  app.use(express.static(dist));
  app.get(/^\/(?!api).*/, (req, res) => res.sendFile(path.join(dist, 'index.html')));
} else {
  app.get('/', (req, res) =>
    res.send('هوش یار پارسی نگر: کلاینت React هنوز build نشده است — داخل parsnegar/client دستور npm install && npm run build را اجرا کنید.')
  );
}

const PORT = Number(process.env.PORT) || 4170;
app.listen(PORT, '0.0.0.0', () => {
  console.log(`هوش یار پارسی نگر ▸ http://0.0.0.0:${PORT}`);
});

// Never crash the API on a stray async error.
process.on('unhandledRejection', (e) => console.error('unhandledRejection:', e));
process.on('uncaughtException', (e) => console.error('uncaughtException:', e));

// Seed on first boot (empty DB).
const { get } = require('./db');
if (!get('SELECT id FROM users LIMIT 1')) {
  require('./db/seed');
}
