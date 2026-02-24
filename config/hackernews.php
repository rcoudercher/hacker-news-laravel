<?php

return [
    'schedule' => [
        'top' => [
            'frequency' => env('HN_TOP_FREQUENCY', 15),
            'limit' => env('HN_TOP_LIMIT', 30),
        ],
        'new' => [
            'frequency' => env('HN_NEW_FREQUENCY', 30),
            'limit' => env('HN_NEW_LIMIT', 20),
        ],
        'best' => [
            'frequency' => env('HN_BEST_FREQUENCY', 60),
            'limit' => env('HN_BEST_LIMIT', 50),
        ],
    ],
];
