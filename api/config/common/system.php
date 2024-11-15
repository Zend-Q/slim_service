<?php

declare(strict_types=1);

return [
    'config' => [
        'env' => (string)getenv('APP_ENV') ?: 'prod',
        'debug' => (bool)getenv('APP_DEBUG'),
    ],
];
