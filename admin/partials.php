<?php
function adminHeader(string $title = 'Admin'): void
{
    echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>' . e($title) . ' - AVSTube Admin</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">';
    echo '<style>body{background:#0b0b0b;color:#ddd} .sidebar{background:#111;min-height:100vh;padding:20px} .sidebar a{display:block;color:#bbb;padding:10px;border-radius:8px;margin-bottom:6px} .sidebar a:hover,.sidebar a.active{background:#c00;color:#fff} .card-dark{background:#111;border:1px solid #222} .table{color:#ddd} .form-control,.form-select,textarea{background:#1a1a1a!important;color:#ddd!important;border-color:#333!important}</style>';
    echo '</head><body><div class="container-fluid"><div class="row">';
    echo '<aside class="col-md-3 col-lg-2 sidebar">';
    echo '<h3 class="text-danger">AVSTube</h3>';
    echo '<a href="dashboard.php"><i class="fa fa-chart-line"></i> Dashboard</a>';
    echo '<a href="videos.php"><i class="fa fa-video"></i> Videos</a>';
    echo '<a href="ads.php"><i class="fa fa-bullhorn"></i> Ads</a>';
    echo '<a href="popup.php"><i class="fa fa-up-right-and-down-left-from-center"></i> Popup Ads</a>';
    echo '<a href="announcements.php"><i class="fa fa-bell"></i> Announcements</a>';
    echo '<a href="settings.php"><i class="fa fa-gear"></i> Settings</a>';
    echo '<a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>';
    echo '</aside><main class="col-md-9 col-lg-10 p-4">';
}

function adminFooter(): void
{
    echo '</main></div></div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>';
}
