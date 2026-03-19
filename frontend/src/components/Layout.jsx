import { Link, NavLink } from 'react-router-dom';

export function Layout({ settings, search, onSearchChange, children }) {
  return (
    <div className="app-shell" style={{ '--primary': settings?.primaryColor || '#e50914' }}>
      <header className="site-header">
        <Link to="/" className="brand">
          {settings?.logo ? <img src={settings.logo} alt={settings.siteName} className="brand-logo" /> : null}
          <span>{settings?.siteName || 'NightFlix'}</span>
        </Link>
        <input
          className="search-input"
          type="search"
          placeholder="Search videos or tags"
          value={search}
          onChange={(event) => onSearchChange(event.target.value)}
        />
        <nav className="nav-links">
          <NavLink to="/upload">Upload</NavLink>
          <NavLink to="/admin">Admin</NavLink>
        </nav>
      </header>
      <main className="content">{children}</main>
    </div>
  );
}
