<?php
namespace Media42;

return [
    'cache' => [
        'caches' => [
            'media' => [
                'driver' => (DEVELOPMENT_MODE === true) ? 'development' : 'production',
                'namespace' => __NAMESPACE__,
            ],
        ],
    ],
];
