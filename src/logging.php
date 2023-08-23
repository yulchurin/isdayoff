<?php

return [
    'channels' => [
        'api-day-off' => [
            'daily' => [
                'driver' => 'daily',
                'path' => storage_path('logs/day-off-api.log'),
                'level' => env('LOG_LEVEL', 'debug'),
                'days' => 60,
            ],
        ],
    ],
];
