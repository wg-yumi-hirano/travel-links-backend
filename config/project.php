<?php declare(strict_types=1);

/** 本プロジェクト用に新規追加した設定値 */
return [
    'login' => [
        'throttle_limit' => 5,
        'throttle_decay_second' => 10,
    ],
    'register' => [
        'throttle_limit' => 3
    ],
    'verification' => [
        'expire_minutes' => 60,
        'throttle_limit' => 6,
        'throttle_decay_minute' => 1,
    ],
    'search_per_page' => 10,
    'command' => [
        'purge_before_hours' => 24
    ]
];
