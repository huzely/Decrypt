<aside class="admin-sidebar panel">
    <h2>Admin Panel</h2>
    <p class="muted">Logged in as <?php echo esc($_SESSION['admin_username']); ?></p>
    <nav class="admin-nav">
        <a href="index.php">Dashboard</a>
        <a href="videos.php">Videos</a>
        <a href="taxonomy.php">Categories & Tags</a>
        <a href="ads.php">Ads</a>
        <a href="announcement.php">Announcement</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php">Logout</a>
    </nav>
</aside>
