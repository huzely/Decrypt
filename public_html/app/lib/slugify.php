<?php
function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    $text = preg_replace('~[^\\pL\\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    $text = preg_replace('~[^-a-z0-9]+~', '', $text);
    return $text ?: ('post-' . time());
}
