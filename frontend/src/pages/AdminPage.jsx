import { useEffect, useMemo, useState } from 'react';
import { LineChart, Line, CartesianGrid, XAxis, YAxis, Tooltip, ResponsiveContainer, BarChart, Bar } from 'recharts';
import { api } from '../api/client';
import { ChartSkeleton } from '../components/Skeletons';

function CrudList({ title, items, fields, onCreate, onUpdate, onDelete, initialState }) {
  const [form, setForm] = useState(initialState);
  const [editingId, setEditingId] = useState('');

  const submit = async (event) => {
    event.preventDefault();
    if (editingId) {
      await onUpdate(editingId, form);
    } else {
      await onCreate(form);
    }
    setForm(initialState);
    setEditingId('');
  };

  return (
    <section className="admin-section">
      <h2>{title}</h2>
      <form className="admin-form" onSubmit={submit}>
        {fields.map((field) => (
          <label key={field.name}>
            {field.label}
            {field.type === 'textarea' ? (
              <textarea value={form[field.name] ?? ''} onChange={(e) => setForm({ ...form, [field.name]: e.target.value })} rows="3" />
            ) : field.type === 'checkbox' ? (
              <input type="checkbox" checked={Boolean(form[field.name])} onChange={(e) => setForm({ ...form, [field.name]: e.target.checked })} />
            ) : (
              <input value={form[field.name] ?? ''} onChange={(e) => setForm({ ...form, [field.name]: e.target.value })} />
            )}
          </label>
        ))}
        <button type="submit">{editingId ? 'Update' : 'Create'}</button>
      </form>
      <div className="admin-list">
        {items.map((item) => (
          <div key={item._id} className="admin-item">
            <div>
              <strong>{item.name || item.title || item.siteName || item.position}</strong>
              {item.content ? <p>{item.content}</p> : null}
            </div>
            <div className="admin-actions">
              <button type="button" onClick={() => { setEditingId(item._id); setForm({ ...item, tags: Array.isArray(item.tags) ? item.tags.join(', ') : item.tags }); }}>Edit</button>
              <button type="button" className="danger" onClick={() => onDelete(item._id)}>Delete</button>
            </div>
          </div>
        ))}
      </div>
    </section>
  );
}

export function AdminPage({ stats, daily, monthly, loadingStats, videos, categories, tags, ads, announcement, settings, refreshAll }) {
  const [settingsForm, setSettingsForm] = useState(settings || {});
  useEffect(() => {
    setSettingsForm(settings || {});
  }, [settings]);
  const announcementItems = announcement ? [announcement] : [];
  const overviewCards = useMemo(() => [
    { label: 'Total Videos', value: stats?.totalVideos ?? 0 },
    { label: 'Total Views', value: stats?.totalViews ?? 0 }
  ], [stats]);

  const createEntity = (path) => (payload) => api.post(path, payload).then(refreshAll);
  const updateEntity = (path) => (id, payload) => api.put(`${path}/${id}`, payload).then(refreshAll);
  const deleteEntity = (path) => (id) => api.delete(`${path}/${id}`).then(refreshAll);

  const saveSettings = async (event) => {
    event.preventDefault();
    await api.post('/settings', settingsForm);
    refreshAll();
  };

  return (
    <div className="admin-dashboard page-stack">
      <section className="overview-grid">
        {overviewCards.map((card) => (
          <article key={card.label} className="overview-card">
            <span>{card.label}</span>
            <strong>{card.value}</strong>
          </article>
        ))}
      </section>
      <section className="chart-grid">
        <div className="chart-card">
          <h2>Daily Views</h2>
          {loadingStats ? <ChartSkeleton /> : (
            <ResponsiveContainer width="100%" height={280}>
              <LineChart data={daily}>
                <Line type="monotone" dataKey="count" stroke="#e50914" strokeWidth={2} />
                <CartesianGrid stroke="#2b2b2b" />
                <XAxis dataKey="date" stroke="#888" />
                <YAxis stroke="#888" />
                <Tooltip />
              </LineChart>
            </ResponsiveContainer>
          )}
        </div>
        <div className="chart-card">
          <h2>Monthly Views</h2>
          {loadingStats ? <ChartSkeleton /> : (
            <ResponsiveContainer width="100%" height={280}>
              <BarChart data={monthly}>
                <Bar dataKey="count" fill="#e50914" />
                <CartesianGrid stroke="#2b2b2b" />
                <XAxis dataKey="month" stroke="#888" />
                <YAxis stroke="#888" />
                <Tooltip />
              </BarChart>
            </ResponsiveContainer>
          )}
        </div>
      </section>
      <CrudList title="Video Management" items={videos} fields={[{ name: 'title', label: 'Title' }, { name: 'thumbnail', label: 'Thumbnail' }, { name: 'videoUrl', label: 'Video URL' }, { name: 'category', label: 'Category' }, { name: 'tags', label: 'Tags (comma separated)' }, { name: 'type', label: 'Type' }]} initialState={{ title: '', thumbnail: '', videoUrl: '', category: '', tags: '', type: 'mp4' }} onCreate={createEntity('/videos')} onUpdate={updateEntity('/videos')} onDelete={deleteEntity('/videos')} />
      <CrudList title="Category Management" items={categories} fields={[{ name: 'name', label: 'Name' }, { name: 'description', label: 'Description' }]} initialState={{ name: '', description: '' }} onCreate={createEntity('/categories')} onUpdate={updateEntity('/categories')} onDelete={deleteEntity('/categories')} />
      <CrudList title="Tag Management" items={tags} fields={[{ name: 'name', label: 'Name' }]} initialState={{ name: '' }} onCreate={createEntity('/tags')} onUpdate={updateEntity('/tags')} onDelete={deleteEntity('/tags')} />
      <CrudList title="Ads Management" items={ads} fields={[{ name: 'image', label: 'Image' }, { name: 'link', label: 'Link' }, { name: 'position', label: 'Position' }, { name: 'active', label: 'Active', type: 'checkbox' }]} initialState={{ image: '', link: '', position: 'header', active: true }} onCreate={createEntity('/ads')} onUpdate={updateEntity('/ads')} onDelete={deleteEntity('/ads')} />
      <CrudList title="Announcement Management" items={announcementItems} fields={[{ name: 'title', label: 'Title' }, { name: 'content', label: 'Content', type: 'textarea' }, { name: 'active', label: 'Active', type: 'checkbox' }]} initialState={{ title: '', content: '', active: true }} onCreate={createEntity('/announcement')} onUpdate={updateEntity('/announcement')} onDelete={deleteEntity('/announcement')} />
      <section className="admin-section">
        <h2>Settings</h2>
        <form className="admin-form" onSubmit={saveSettings}>
          <label>Site Name<input value={settingsForm.siteName || ''} onChange={(e) => setSettingsForm({ ...settingsForm, siteName: e.target.value })} /></label>
          <label>Logo URL<input value={settingsForm.logo || ''} onChange={(e) => setSettingsForm({ ...settingsForm, logo: e.target.value })} /></label>
          <label>Primary Color<input value={settingsForm.primaryColor || ''} onChange={(e) => setSettingsForm({ ...settingsForm, primaryColor: e.target.value })} /></label>
          <label className="checkbox-row">Enable Popup Ads<input type="checkbox" checked={Boolean(settingsForm.popupAdsEnabled)} onChange={(e) => setSettingsForm({ ...settingsForm, popupAdsEnabled: e.target.checked })} /></label>
          <button type="submit">Save Settings</button>
        </form>
      </section>
    </div>
  );
}
