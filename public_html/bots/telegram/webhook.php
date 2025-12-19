<?php
require __DIR__ . '/bot.php';
$update = json_decode(file_get_contents('php://input'), true);
if ($update) {
    handle_update($update);
}
