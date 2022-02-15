<?php

return [
    'name' => 'Infinity',

    /*
     * Caching.
     */
    'cache' => [
        'config' => [
            'use_cache' => env('CACHE_INFINITY_SETTINGS', true),
            'ttl' => env('INFINITY_CONFIG_CACHE_TTL', 60 * 60 * 24)
        ]
    ],

    /*
     * FontAwesome configuration.
     */
    'fontawesome' => [
        'licence' => env('FONTAWESOME_LICENCE', 'free'), // "free" or "pro"
        'src' => env('FONTAWESOME_SRC', 'https://use.fontawesome.com/releases/v5.15.4/js/all.js'),
        'integrity' => env('FONTAWESOME_INTEGRITY'),
        'crossorigin' => env('FONTAWESOME_CROSSORIGIN', 'anonymous'),
        'defer' => env('FONTAWESOME_DEFER', true),
    ],

    /*
     * Controllers that are used by Infinity.
     */
    'controllers' => [
        'namespace' => 'Infinity\\Http\\Controllers'
    ],

    /*
     * Multilingual support for Infinity CMS.
     */
    'multilingual' => [

        /*
         * If set to true, then translations will be attempted for different
         * locales.
         */
        'enabled' => true,

        /*
         * The default locale that is used throughout Infinity if one is not
         * set for a user.
         */
        'default' => 'en',

        /*
         * Available locales within Infinity.
         */
        'locales' => []
    ],

    /*
     * Application model configuration.
     */
    'models' => [
         'namespace' => 'App\\Models\\',
    ],
];
