<?php
function slugify(string $text): string
{
    $text = preg_replace('~[\p{Pd}\s]+~u', '-', $text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    $text = preg_replace('~[^\w-]+~', '', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    return $text ?: uniqid('post-', true);
}
