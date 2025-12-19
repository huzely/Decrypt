<?php
echo json_encode([
    'inline_keyboard' => [
        [
            ['text' => 'Thống kê', 'callback_data' => 'stats'],
            ['text' => 'Reset', 'callback_data' => 'reset'],
        ],
        [
            ['text' => 'Thêm bài', 'callback_data' => 'add'],
            ['text' => 'Quảng cáo', 'callback_data' => 'ad'],
        ]
    ]
]);
