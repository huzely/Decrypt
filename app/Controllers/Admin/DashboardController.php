<?php
namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\Article;
use App\Models\Stat;
use App\Models\Setting;

class DashboardController extends BaseAdminController
{
    public function index(): void
    {
        $this->requireAuth();
        $stats = Stat::totals();
        $daily = Stat::daily();
        $byArticle = Stat::byArticle();
        $settings = Setting::getAll();
        View::render('admin/dashboard', [
            'stats' => $stats,
            'daily' => $daily,
            'byArticle' => $byArticle,
            'settings' => $settings,
        ]);
    }

    public function resetStats(): void
    {
        $this->requireAuth();
        verify_csrf();
        Stat::reset();
        $logs = Setting::get('reset_logs', []);
        $logs[] = [
            'by' => $_SESSION['admin_name'] ?? 'unknown',
            'at' => date('Y-m-d H:i:s'),
        ];
        Setting::set('reset_logs', $logs);
        $_SESSION['flash'] = 'Đã reset thống kê.';
        redirect('admin');
    }
}
