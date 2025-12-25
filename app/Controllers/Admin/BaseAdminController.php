<?php
namespace App\Controllers\Admin;

class BaseAdminController
{
    protected function requireAuth(): void
    {
        if (empty($_SESSION['admin_id'])) {
            redirect('admin/login');
        }
    }
}
