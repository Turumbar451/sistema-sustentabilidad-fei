<?php

declare(strict_types=1);

use App\Models\UsuarioInstitucional;

return [
    'defaults' => [
        'guard' => 'web',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => UsuarioInstitucional::class,
        ],
    ],

    'passwords' => [],

    'password_timeout' => 10800,
];
