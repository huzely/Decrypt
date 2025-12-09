<?php
require_once __DIR__ . '/../functions.php';
session_destroy();
redirect_with_message('/admin/login.php', 'Đã đăng xuất');
