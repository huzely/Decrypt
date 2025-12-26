<?php
session_start();
require_once __DIR__ . '/../../config.php';
if (empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}
