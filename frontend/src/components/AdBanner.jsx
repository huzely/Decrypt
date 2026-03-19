export function AdBanner({ ad }) {
  if (!ad?.active) return null;

  return (
    <a className="ad-banner" href={ad.link} target="_blank" rel="noreferrer">
      <img src={ad.image} alt={`${ad.position} ad`} />
    </a>
  );
}
