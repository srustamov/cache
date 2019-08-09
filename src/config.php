<?php


return array(

    'adapter' => 'file',

    'file' => array(
        'path' => __DIR__.'/storage'
    ),

    'memcache' => array(
        'host' => '127.0.0.1',
        'port' => 11211
    ),

    'redis' => array(
        'host' => '127.0.0.1',
        'port' => 6379,
        'database' => 0,
        'auth' => [
            'has' => false,
            'password' => ''
        ]
    ),
);
