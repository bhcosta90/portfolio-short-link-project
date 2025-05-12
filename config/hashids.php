<?php

declare(strict_types = 1);

return [
    'enable_cryptography' => env('ENABLE_CRYPTOGRAPHY', true),
    'regex'               => env('HASHID_REGEX', '/id$|_id$|Id$|_ids$/'), // Default
    'headers'             => [
        'regex' => env('HASHID_HEADER_REGEX', '/^(X-Admin|X-Agent|X-User)/i'),
    ],
    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [

        'main' => [
            'salt'     => env('HASH_SALT', 'salt-example'),
            'length'   => 10,
            'alphabet' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],
    ],

];
