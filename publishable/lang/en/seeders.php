<?php

return [

    'groups'      => ['admin', 'user'],
    'permissions' => [
        'app.access' => 'Application access',
        'dashboard.browse' => 'Browse dashboard',
    ],

    'defaultPermissions' => [
        'admin' => [
            'app.access',
            'dashboard.browse',
        ]
    ],

    'settings' => [
        'auth.wallpaper' => [
            'name' => 'Authentication background',
            'default' => 'f::infinity_asset(\'images/decoration/background.png\')',
            'type' => 'image',
        ],
        'date.format.long' => [
            'name' => 'Long date format',
            'default' => 'j FS, Y',
            'type' => 'text',
        ],
        'date.format.short' => [
            'name' => 'Short date format',
            'default' => 'd/m/Y',
            'type' => 'text',
        ],
        'time.format.long' => [
            'name' => 'Long time format',
            'default' => 'H:i:s',
            'type' => 'text',
        ],
        'time.format.short' => [
            'name' => 'Short time format',
            'default' => 'H:i',
            'type' => 'text',
        ],
    ]

];
