<?php
function main_keyboard(): array
{
    return [
        'inline_keyboard' => [
            [
                ['text' => '📰 Bài viết', 'callback_data' => 'posts_menu'],
                ['text' => '🛒 Quảng cáo', 'callback_data' => 'ads_menu'],
            ],
            [
                ['text' => '📊 Thống kê', 'callback_data' => 'stats_menu'],
                ['text' => '♻️ Reset', 'callback_data' => 'reset_menu'],
            ],
        ],
    ];
}

function posts_keyboard(): array
{
    return [
        'inline_keyboard' => [
            [['text'=>'Danh sách gần đây','callback_data'=>'posts_list']],
            [['text'=>'Thêm bài','callback_data'=>'post_add']],
            [['text'=>'Quay lại','callback_data'=>'back_main']],
        ],
    ];
}

function ads_keyboard(): array
{
    return [
        'inline_keyboard' => [
            [['text'=>'Xem ad_link','callback_data'=>'ad_info']],
            [['text'=>'Cập nhật ad_link','callback_data'=>'ad_set_link']],
            [['text'=>'Cập nhật ad_title','callback_data'=>'ad_set_title']],
            [['text'=>'Cập nhật ad_body','callback_data'=>'ad_set_body']],
            [['text'=>'Quay lại','callback_data'=>'back_main']],
        ],
    ];
}

function stats_keyboard(): array
{
    return [
        'inline_keyboard' => [
            [['text'=>'Hôm nay','callback_data'=>'stats_today']],
            [['text'=>'Tháng này','callback_data'=>'stats_month']],
            [['text'=>'Top view','callback_data'=>'stats_top']],
            [['text'=>'Tổng click Shopee','callback_data'=>'stats_click']],
            [['text'=>'Quay lại','callback_data'=>'back_main']],
        ],
    ];
}

function reset_keyboard(): array
{
    return [
        'inline_keyboard' => [
            [['text'=>'Reset view bài','callback_data'=>'reset_view']],
            [['text'=>'Reset click quảng cáo','callback_data'=>'reset_ad']],
            [['text'=>'Quay lại','callback_data'=>'back_main']],
        ],
    ];
}
