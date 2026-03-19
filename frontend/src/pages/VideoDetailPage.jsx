import { useState } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../api/client';
import { VideoGridSkeleton } from '../components/Skeletons';

export function VideoDetailPage({ video, loading, onRefresh }) {
  const [comment, setComment] = useState('');
  const [submitting, setSubmitting] = useState(false);

  if (loading) return <VideoGridSkeleton />;
  if (!video) return <p>Video not found.</p>;

  const submitComment = async (event) => {
    event.preventDefault();
    if (!comment.trim()) return;
    setSubmitting(true);
    await api.post('/comments', { videoId: video._id, text: comment });
    setComment('');
    await onRefresh();
    setSubmitting(false);
  };

  return (
    <div className="detail-layout">
      <section className="player-panel">
        {video.type === 'mp4' ? (
          <video controls className="video-player" src={video.videoUrl} />
        ) : (
          <iframe
            className="video-player iframe-player"
            src={video.videoUrl}
            title={video.title}
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowFullScreen
          />
        )}
        <h1>{video.title}</h1>
        <p className="video-meta">{video.views.toLocaleString()} views • {new Date(video.createdAt).toLocaleDateString()}</p>
        <p>{video.description}</p>
        <div className="tag-bar">
          {video.tags.map((tag) => (
            <Link key={tag} to={`/?tag=${encodeURIComponent(tag)}`} className="tag-link">#{tag}</Link>
          ))}
        </div>
        <form className="comment-form" onSubmit={submitComment}>
          <textarea value={comment} onChange={(event) => setComment(event.target.value)} placeholder="Add a comment" rows="4" />
          <button type="submit" disabled={submitting}>{submitting ? 'Posting...' : 'Post Comment'}</button>
        </form>
        <div className="comment-list">
          {video.comments.map((item) => (
            <article key={item._id} className="comment-item">
              <p>{item.text}</p>
              <span>{new Date(item.createdAt).toLocaleString()}</span>
            </article>
          ))}
        </div>
      </section>
      <aside className="related-panel">
        <h2>Related Videos</h2>
        {video.relatedVideos.map((item) => (
          <Link key={item._id} to={`/video/${item._id}`} className="related-card">
            <img src={item.thumbnail} alt={item.title} />
            <div>
              <h3>{item.title}</h3>
              <p>{item.views.toLocaleString()} views</p>
            </div>
          </Link>
        ))}
      </aside>
    </div>
  );
}
