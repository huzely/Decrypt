import { useMemo } from 'react';
import { VideoCard } from '../components/VideoCard';
import { VideoGridSkeleton } from '../components/Skeletons';
import { AdBanner } from '../components/AdBanner';

export function HomePage({ videos, loading, categories, selectedCategory, onCategoryChange, selectedTag, onTagChange, announcement, ads }) {
  const tags = useMemo(() => [...new Set(videos.flatMap((video) => video.tags || []))], [videos]);
  const headerAd = ads.find((ad) => ad.position === 'header' && ad.active);
  const middleAd = ads.find((ad) => ad.position === 'middle' && ad.active);
  const footerAd = ads.find((ad) => ad.position === 'footer' && ad.active);

  return (
    <div className="page-stack">
      <AdBanner ad={headerAd} />
      {announcement ? (
        <section className="announcement">
          <h2>{announcement.title}</h2>
          <p>{announcement.content}</p>
        </section>
      ) : null}
      <section className="filter-bar">
        <button type="button" className={!selectedCategory ? 'active' : ''} onClick={() => onCategoryChange('')}>All</button>
        {categories.map((category) => (
          <button
            type="button"
            key={category._id}
            className={selectedCategory === category.name ? 'active' : ''}
            onClick={() => onCategoryChange(category.name)}
          >
            {category.name}
          </button>
        ))}
      </section>
      <section className="tag-bar">
        <button type="button" className={!selectedTag ? 'active' : ''} onClick={() => onTagChange('')}>All tags</button>
        {tags.map((tag) => (
          <button type="button" key={tag} className={selectedTag === tag ? 'active' : ''} onClick={() => onTagChange(tag)}>
            #{tag}
          </button>
        ))}
      </section>
      {loading ? <VideoGridSkeleton /> : <div className="video-grid">{videos.map((video) => <VideoCard key={video._id} video={video} />)}</div>}
      <AdBanner ad={middleAd} />
      <AdBanner ad={footerAd} />
    </div>
  );
}
