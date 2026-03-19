import { useEffect, useState } from 'react';

export function PopupAd({ ad, enabled }) {
  const [open, setOpen] = useState(false);

  useEffect(() => {
    if (!enabled || !ad?.active) return;
    const alreadySeen = sessionStorage.getItem('popupSeen');
    if (!alreadySeen) {
      setOpen(true);
      sessionStorage.setItem('popupSeen', 'true');
    }
  }, [ad, enabled]);

  if (!open || !ad?.active || !enabled) return null;

  return (
    <div className="popup-overlay" onClick={() => setOpen(false)}>
      <div className="popup-card" onClick={(event) => event.stopPropagation()}>
        <button type="button" className="popup-close" onClick={() => setOpen(false)}>×</button>
        <a href={ad.link} target="_blank" rel="noreferrer">
          <img src={ad.image} alt="Popup ad" />
        </a>
      </div>
    </div>
  );
}
