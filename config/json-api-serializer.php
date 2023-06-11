<?php

return [

    'jsonapi' => [
        'version' => env('JSON_API_VERSION', '1.0'),

        'transformer_mapping' => [
            // Entity::class => EntityTransformer::class,
        ],

        'resource_type_mapping' => [
            // Entity::class => 'entities',
        ],
    ],

];