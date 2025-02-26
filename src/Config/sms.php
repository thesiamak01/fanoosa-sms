<?php

return [
    'defaults' => [
        'sms_panel' => 'meliPayamak',
    ],
    'configuration' => [
        'meliPayamak' => [
            'pattern_api_key' => env('MELIPAYAMAK_PATTERN_API_KEY', ''),
        ],
    ],
];
