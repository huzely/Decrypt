<?php
$config = require __DIR__ . '/../app/config/config.php';
require __DIR__ . '/../app/helpers/util.php';
require __DIR__ . '/../app/models/ClickEvent.php';

header('Content-Type: application/json');
$stats = ClickEvent::stats();
echo json_encode($stats);
