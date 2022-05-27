<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    |
    | The default driver you would like to use for location retrieval.
    |
    */
    'driver' => Stevebauman\Location\Drivers\IpApi::class,

    /*
    |--------------------------------------------------------------------------
    | Driver Fallbacks
    |--------------------------------------------------------------------------
    |
    | The drivers you want to use to retrieve the users location
    | if the above selected driver is unavailable.
    |
    | These will be called upon in order (first to last).
    |
    */
    'fallbacks' => [
        Stevebauman\Location\Drivers\GeoPlugin::class,
        Stevebauman\Location\Drivers\MaxMind::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Position
    |--------------------------------------------------------------------------
    |
    | Here you may configure the position instance that is created
    | and returned from the above drivers. The instance you
    | create must extend the built-in Position class.
    |
    */
    'position' => Stevebauman\Location\Position::class,

    /*
    |--------------------------------------------------------------------------
    | MaxMind Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration for the MaxMind driver.
    |
    | If web service is enabled, you must fill in your user ID and license key.
    |
    | If web service is disabled, it will try and retrieve the users' location
    | from the MaxMind database file located in the local path below.
    | https://www.maxmind.com/en/accounts/590471/geoip/downloads
    */
    'maxmind' => [
        'local' => [
            'path' => database_path('maxmind/GeoLite2-City.mmdb')
        ],
    ],
];
