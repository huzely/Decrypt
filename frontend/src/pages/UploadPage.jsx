import { useState } from 'react';
import { api } from '../api/client';

const initialState = {
  title: '',
  description: '',
  thumbnail: '',
  videoUrl: '',
  type: 'mp4',
  category: '',
  tags: ''
};

export function UploadPage({ onUploaded }) {
  const [form, setForm] = useState(initialState);
  const [saving, setSaving] = useState(false);

  const handleSubmit = async (event) => {
    event.preventDefault();
    setSaving(true);
    await api.post('/videos', form);
    setForm(initialState);
    setSaving(false);
    onUploaded();
  };

  return (
    <form className="admin-form upload-form" onSubmit={handleSubmit}>
      <h1>Upload Video</h1>
      {Object.entries(form).map(([key, value]) => (
        key === 'type' ? (
          <label key={key}>
            Type
            <select value={value} onChange={(event) => setForm({ ...form, type: event.target.value })}>
              <option value="mp4">mp4</option>
              <option value="embed">embed</option>
            </select>
          </label>
        ) : key === 'description' ? (
          <label key={key}>
            Description
            <textarea value={value} onChange={(event) => setForm({ ...form, [key]: event.target.value })} rows="5" />
          </label>
        ) : (
          <label key={key}>
            {key}
            <input value={value} onChange={(event) => setForm({ ...form, [key]: event.target.value })} required={key !== 'description'} />
          </label>
        )
      ))}
      <button type="submit" disabled={saving}>{saving ? 'Saving...' : 'Upload'}</button>
    </form>
  );
}
