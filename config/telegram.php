<?php

use App\TelegramCommands\StartCommand;

return [
    'bots' => [
        'main_bot' => [
            'username' => env('TELEGRAM_BOT_NAME', ''),
            'token' => env('TELEGRAM_BOT_API', ''),
            //'webhook_url' => env('TELEGRAM_WEBHOOK_URL', ''),
            'commands' => [
                StartCommand::class
            ],
        ],
    ],
    'default' => 'main_bot',
    'async_requests' => env('TELEGRAM_ASYNC_REQUESTS', false),
    'http_client_handler' => null,
    'resolve_command_dependencies' => true,
    'transactions_channel' => env('TELEGRAM_TRANSACTIONS_CHANNEL', ''),
    'errors_channel' => env('TELEGRAM_ERRORS_CHANNEL', ''),
];
