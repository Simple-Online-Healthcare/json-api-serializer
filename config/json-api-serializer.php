<?php

return [

    'jsonapi' => [
        'version' => env('JSON_API_VERSION', '1.0'),

        'resource_type_mapping' => [
            // Entity::class => 'entities',
        ],

        'transformer_mapping' => [
            // Entity::class => EntityTransformer::class,
        ],
    ],

];