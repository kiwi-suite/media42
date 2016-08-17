<?php
namespace Media42;

return [
    'cache' => [
        'caches' => [
            'media' => [
                'driver' => 'ephemeral',
                'namespace' => __NAMESPACE__,
            ],
        ],
    ],
];
