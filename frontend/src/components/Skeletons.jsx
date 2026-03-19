export function VideoGridSkeleton() {
  return (
    <div className="video-grid">
      {Array.from({ length: 8 }).map((_, index) => (
        <div key={index} className="skeleton-card">
          <div className="skeleton thumbnail-skeleton" />
          <div className="skeleton line" />
          <div className="skeleton line short" />
        </div>
      ))}
    </div>
  );
}

export function ChartSkeleton() {
  return <div className="chart-skeleton skeleton" />;
}
