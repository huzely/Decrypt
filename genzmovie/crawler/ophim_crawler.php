<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

function ophim_get(string $url): ?array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    if ($response === false) {
        return null;
    }
    $decoded = json_decode($response, true);
    return is_array($decoded) ? $decoded : null;
}

$pdo = Database::connection();
$listData = ophim_get(OPHIM_API_BASE . '/danh-sach/phim-moi-cap-nhat?page=1');
if (!$listData || empty($listData['data']['items'])) {
    echo "Crawler: no items found\n";
    return;
}

foreach ($listData['data']['items'] as $item) {
    $slug = $item['slug'] ?? null;
    if (!$slug) {
        continue;
    }

    $detail = ophim_get(OPHIM_API_BASE . '/phim/' . urlencode($slug));
    if (!$detail || empty($detail['data']['item'])) {
        continue;
    }

    $movie = $detail['data']['item'];
    $title = $movie['name'] ?? 'N/A';
    $origin = $movie['origin_name'] ?? '';
    $description = strip_tags($movie['content'] ?? '');
    $poster = $movie['poster_url'] ?? '';
    $year = (int) ($movie['year'] ?? date('Y'));
    $country = $movie['country'][0]['name'] ?? 'Việt Nam';
    $director = implode(', ', array_column($movie['director'] ?? [], 'name'));
    $actors = implode(', ', $movie['actor'] ?? []);
    $quality = $movie['quality'] ?? 'HD';
    $language = $movie['lang'] ?? 'Vietsub';
    $tags = implode(',', array_column($movie['category'] ?? [], 'name'));
    $type = $movie['type'] ?? 'single';

    $check = $pdo->prepare('SELECT id FROM movies WHERE slug = :slug LIMIT 1');
    $check->execute(['slug' => $slug]);
    $existing = $check->fetch();

    if ($existing) {
        $movieId = (int) $existing['id'];
        $update = $pdo->prepare('UPDATE movies SET title=:title, original_title=:original_title, description=:description, poster=:poster, year=:year, country=:country, director=:director, actors=:actors, quality=:quality, language=:language, tags=:tags, type=:type, updated_at=NOW() WHERE id=:id');
        $update->execute(compact('title', 'origin', 'description', 'poster', 'year', 'country', 'director', 'actors', 'quality', 'language', 'tags', 'type') + ['id' => $movieId, 'original_title' => $origin]);
    } else {
        $insert = $pdo->prepare('INSERT INTO movies (title, slug, original_title, description, poster, year, country, director, actors, quality, language, tags, type, is_featured, meta_title, meta_description, meta_keywords, created_at, updated_at) VALUES (:title,:slug,:original_title,:description,:poster,:year,:country,:director,:actors,:quality,:language,:tags,:type,0,:meta_title,:meta_description,:meta_keywords,NOW(),NOW())');
        $insert->execute([
            'title' => $title,
            'slug' => $slug,
            'original_title' => $origin,
            'description' => $description,
            'poster' => $poster,
            'year' => $year,
            'country' => $country,
            'director' => $director,
            'actors' => $actors,
            'quality' => $quality,
            'language' => $language,
            'tags' => $tags,
            'type' => $type,
            'meta_title' => $title,
            'meta_description' => mb_strimwidth($description, 0, 160, '...'),
            'meta_keywords' => $tags,
        ]);
        $movieId = (int) $pdo->lastInsertId();
    }

    $pdo->prepare('DELETE FROM episodes WHERE movie_id = :movie_id')->execute(['movie_id' => $movieId]);
    foreach (($detail['data']['episodes'] ?? []) as $server) {
        $serverName = $server['server_name'] ?? 'Server 1';
        foreach (($server['server_data'] ?? []) as $episode) {
            $pdo->prepare('INSERT INTO episodes (movie_id, server_name, episode_number, embed_link, mp4_link, subtitle_link, created_at, updated_at) VALUES (:movie_id,:server_name,:episode_number,:embed_link,:mp4_link,:subtitle_link,NOW(),NOW())')
                ->execute([
                    'movie_id' => $movieId,
                    'server_name' => $serverName,
                    'episode_number' => $episode['name'] ?? '1',
                    'embed_link' => $episode['link_embed'] ?? '',
                    'mp4_link' => $episode['link_m3u8'] ?? '',
                    'subtitle_link' => '',
                ]);
        }
    }
}

echo "Crawler finished\n";
