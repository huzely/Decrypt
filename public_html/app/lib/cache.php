<?php
function cache_path(array $config, string $key): string
{
    return rtrim($config['cache_path'], '/') . '/' . md5($key) . '.cache.php';
}

function cache_get(array $config, string $key)
{
    $file = cache_path($config, $key);
    if (!file_exists($file)) {
        return null;
    }
    $data = include $file;
    if (!is_array($data) || ($data['expires'] ?? 0) < time()) {
        @unlink($file);
        return null;
    }
    return $data['value'];
}

function cache_set(array $config, string $key, $value, int $ttl): void
{
    if (!is_dir($config['cache_path'])) {
        @mkdir($config['cache_path'], 0775, true);
    }
    $payload = ['expires' => time() + $ttl, 'value' => $value];
    file_put_contents(cache_path($config, $key), '<?php return ' . var_export($payload, true) . ';');
}

function cache_clear(array $config, ?string $prefix = null): void
{
    if (!is_dir($config['cache_path'])) {
        return;
    }
    foreach (glob(rtrim($config['cache_path'], '/') . '/*.cache.php') as $file) {
        if ($prefix && strpos($file, md5($prefix)) === false) {
            continue;
        }
        @unlink($file);
    }
}
