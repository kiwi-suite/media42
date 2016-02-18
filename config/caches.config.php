<?php
return [
    'caches' => [
        'Cache\Media' => [
            'adapter' => [
                'name' => 'memory',
            ],
            'plugins' => [
                'Serializer'
            ],
        ],
    ],
];
