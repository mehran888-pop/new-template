import React, { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { api, faDate } from '../api';
import { Card, Badge } from '../components/ui';

export function Blog() {
  const [posts, setPosts] = useState([]);
  useEffect(() => { api('/posts').then(setPosts).catch(() => {}); }, []);
  return (
    <div className="page">
      <div className="neo-section__head"><h2 className="neo-section__title">وبلاگ پارسی‌نگر</h2></div>
      <div className="neo-grid neo-grid--3">
        {posts.map((p) => (
          <Card key={p.id}>
            <Badge>{p.category || 'وبلاگ'}</Badge>
            <h3 style={{ fontSize: 17, marginTop: 10 }}><Link to={'/blog/' + p.slug}>{p.title}</Link></h3>
            <p className="muted">{p.excerpt}</p>
            <div className="muted" style={{ fontSize: 11 }}>{faDate(p.published_at)}</div>
          </Card>
        ))}
      </div>
    </div>
  );
}

export function Post() {
  const { slug } = useParams();
  const [post, setPost] = useState(null);
  const [err, setErr] = useState('');
  useEffect(() => {
    api('/posts/' + slug).then(setPost).catch((e) => setErr(e.message));
  }, [slug]);
  if (err) return <div className="page center muted">{err}</div>;
  if (!post) return <div className="page center muted">در حال بارگذاری…</div>;
  return (
    <div className="page">
      <Card style={{ maxWidth: 780, margin: '0 auto' }}>
        <Badge>{post.category || 'وبلاگ'}</Badge>
        <h1 style={{ marginTop: 12 }}>{post.title}</h1>
        <div className="muted mb" style={{ fontSize: 12 }}>{faDate(post.published_at)}</div>
        <p style={{ whiteSpace: 'pre-line' }}>{post.body}</p>
        <Link to="/blog" className="neo-btn neo-btn--sm mt">← بازگشت به وبلاگ</Link>
      </Card>
    </div>
  );
}
