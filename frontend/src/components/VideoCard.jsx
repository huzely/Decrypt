import { Link } from 'react-router-dom';

export function VideoCard({ video }) {
  return (
    <Link to={`/video/${video._id}`} className="video-card">
      <div className="thumbnail-wrap">
        <img src={video.thumbnail} alt={video.title} className="thumbnail" />
      </div>
      <div className="video-card-body">
        <h3>{video.title}</h3>
        <p>{video.views.toLocaleString()} views</p>
        <span>{new Date(video.createdAt).toLocaleDateString()}</span>
      </div>
    </Link>
  );
}
