<?php
// Helper script to process single file upload when cần gọi trực tiếp.
// Ví dụ: include 'upload_handler.php'; $path = upload_single('thumbnail');
require_once __DIR__ . '/functions.php';

function upload_single(string $field_name): ?string
{
    return handle_upload($field_name);
}
?>
