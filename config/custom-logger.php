<?php
use Monolog\Logger;

return [
    'path' => storage_path('logs/custom.log'),

    'enabled' => env('CUSTOM_LOGGER_ENABLED', true),

    'level' => env('CUSTOM_LOGGER_LEVEL', Logger::ERROR),

    // Format settings
    'date_format' => 'Y-m-d H:i:s',
    'format' => "{'time': '%datetime%', 'channel': '%channel%', 'level': '%level_name%' , 'message': '%message%', 'extra': %extra%}",

    // Security
    'mask_fields' => [
        'password',
        'token',
        'secret',
        'key',
        'api_key',
        'csrf',
        'credit_card',
        'email'
    ],
];