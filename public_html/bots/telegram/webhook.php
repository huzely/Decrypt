<?php
require __DIR__ . '/bot.php';

$update = json_decode(file_get_contents('php://input'), true);
$message = $update['message'] ?? $update['callback_query']['message'] ?? null;
$chatId = $message['chat']['id'] ?? null;
$text = $update['message']['text'] ?? '';
$callback = $update['callback_query']['data'] ?? null;

if (!$chatId || !is_allowed($chatId, $config)) {
    if ($chatId) tg_send($chatId, "Bạn không có quyền.");
    exit;
}

if ($text === '/start') {
    tg_send($chatId, "Menu quản trị:", main_keyboard());
    exit;
}

if ($callback) {
    switch ($callback) {
        case 'posts_menu': tg_send($chatId, 'Quản lý bài viết', posts_keyboard()); break;
        case 'ads_menu': tg_send($chatId, 'Quảng cáo Shopee', ads_keyboard()); break;
        case 'stats_menu': tg_send($chatId, 'Thống kê', stats_keyboard()); break;
        case 'reset_menu': tg_send($chatId, 'Reset dữ liệu', reset_keyboard()); break;
        case 'back_main': tg_send($chatId, 'Menu', main_keyboard()); break;
        case 'posts_list':
            $stmt = $pdo->query('SELECT id, title, slug, status FROM articles ORDER BY created_at DESC LIMIT 5');
            $lines=[];
            foreach ($stmt as $r) { $lines[]="#{$r['id']} {$r['title']} ({$r['status']}) /{$r['slug']}"; }
            tg_send($chatId, $lines?implode("\n",$lines):'Không có bài.', posts_keyboard());
            break;
        case 'post_add':
            tg_send($chatId, "Gửi: /add title | slug | excerpt", posts_keyboard());
            break;
        case 'ad_info':
            $s = $pdo->query('SELECT ad_link, ad_title, ad_body FROM site_settings WHERE id=1')->fetch();
            tg_send($chatId, "ad_link: ".($s['ad_link']??'')."\nad_title: ".($s['ad_title']??'')."\nad_body: ".($s['ad_body']??''), ads_keyboard());
            break;
        case 'stats_today':
            $pv = $pdo->query('SELECT COUNT(*) FROM click_events WHERE event_type="article_view" AND DATE(created_at)=CURDATE()')->fetchColumn();
            $ad = $pdo->query('SELECT COUNT(*) FROM click_events WHERE event_type="ad_forced_redirect" AND DATE(created_at)=CURDATE()')->fetchColumn();
            tg_send($chatId, "Hôm nay: PV {$pv}, ad {$ad}", stats_keyboard());
            break;
        case 'stats_month':
            $pv = $pdo->query('SELECT COUNT(*) FROM click_events WHERE event_type="article_view" AND MONTH(created_at)=MONTH(NOW())')->fetchColumn();
            $ad = $pdo->query('SELECT COUNT(*) FROM click_events WHERE event_type="ad_forced_redirect" AND MONTH(created_at)=MONTH(NOW())')->fetchColumn();
            tg_send($chatId, "Tháng này: PV {$pv}, ad {$ad}", stats_keyboard());
            break;
        case 'stats_top':
            $stmt=$pdo->query('SELECT slug, COUNT(*) c FROM click_events WHERE event_type="article_view" GROUP BY slug ORDER BY c DESC LIMIT 5');
            $lines=[]; foreach($stmt as $r){ $lines[]="{$r['slug']} : {$r['c']} view"; }
            tg_send($chatId, $lines?implode("\n",$lines):'Chưa có', stats_keyboard());
            break;
        case 'stats_click':
            $total=$pdo->query('SELECT COUNT(*) FROM click_events WHERE event_type="ad_forced_redirect"')->fetchColumn();
            tg_send($chatId, "Tổng click Shopee: {$total}", stats_keyboard());
            break;
        case 'reset_view':
            $pdo->exec('DELETE FROM click_events WHERE event_type="article_view"');
            log_admin($pdo, $chatId, 'reset_view');
            tg_send($chatId, "Đã reset view.", reset_keyboard());
            break;
        case 'reset_ad':
            $pdo->exec('DELETE FROM click_events WHERE event_type="ad_forced_redirect"');
            log_admin($pdo, $chatId, 'reset_ad');
            tg_send($chatId, "Đã reset click ads.", reset_keyboard());
            break;
    }
    exit;
}

if (strpos($text, '/add') === 0) {
    $parts = explode('|', substr($text, 4));
    $title = trim($parts[0] ?? '');
    $slug = trim($parts[1] ?? '');
    $excerpt = trim($parts[2] ?? '');
    if ($title) {
        $stmt = $pdo->prepare('INSERT INTO articles (slug,title,excerpt,content,status,created_at,updated_at) VALUES (?,?,?,?, "draft", NOW(), NOW())');
        $stmt->execute([$slug ?: time(), $title, $excerpt, '<p>Nội dung</p>']);
        log_admin($pdo, $chatId, 'add_post', ['title'=>$title]);
        tg_send($chatId, "Đã thêm bài.", posts_keyboard());
    }
    exit;
}

if (strpos($text, '/publish') === 0) {
    $slug = trim(substr($text, 8));
    $stmt = $pdo->prepare('UPDATE articles SET status="published" WHERE slug=?');
    $stmt->execute([$slug]);
    log_admin($pdo, $chatId, 'publish', ['slug'=>$slug]);
    tg_send($chatId, "Publish {$slug}");
    exit;
}

if (strpos($text, '/unpublish') === 0) {
    $slug = trim(substr($text, 10));
    $stmt = $pdo->prepare('UPDATE articles SET status="draft" WHERE slug=?');
    $stmt->execute([$slug]);
    log_admin($pdo, $chatId, 'unpublish', ['slug'=>$slug]);
    tg_send($chatId, "Unpublish {$slug}");
    exit;
}

if (strpos($text, '/adset') === 0) {
    $parts = explode(' ', $text, 3);
    $field = $parts[1] ?? '';
    $value = $parts[2] ?? '';
    if (in_array($field, ['link','title','body'], true)) {
        $col = $field === 'link' ? 'ad_link' : ($field === 'title' ? 'ad_title' : 'ad_body');
        $stmt = $pdo->prepare("UPDATE site_settings SET {$col}=? WHERE id=1");
        $stmt->execute([$value]);
        log_admin($pdo, $chatId, 'update_ad', ['field'=>$col]);
        tg_send($chatId, "Đã cập nhật {$col}");
    }
    exit;
}

tg_send($chatId, "Menu quản trị:", main_keyboard());
