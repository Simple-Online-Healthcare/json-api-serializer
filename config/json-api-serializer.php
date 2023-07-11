<?php

return [

    'jsonapi' => [
        'version' => env('JSON_API_VERSION', '1.0'),

        'resource_types' => [
            // Entity::class => 'entities',
        ],

        'normalizers' => [
            // Entity::class => EntityNormalizer::class,
        ],
    ],

];