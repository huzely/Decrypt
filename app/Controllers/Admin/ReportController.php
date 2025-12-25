<?php
namespace App\Controllers\Admin;

use App\Models\Stat;

class ReportController extends BaseAdminController
{
    public function exportCsv(): void
    {
        $this->requireAuth();
        $rows = Stat::byArticle();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="article_stats.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Tiêu đề', 'View', 'Click']);
        foreach ($rows as $row) {
            fputcsv($out, [$row['id'], $row['title'], $row['views'] ?? 0, $row['clicks'] ?? 0]);
        }
        fclose($out);
    }
}
