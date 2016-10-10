<?php
namespace Media42;

use Media42\View\Helper\Service\MediaFactory;
use Media42\View\Helper\Service\MediaUrlFactory;

return [

    'media' => [
        'upload_host' => '',
        'path' => 'data/media/',
        'url' => '/media/',

        'categories' => [
            'default' => 'media.category.default'
        ],

        'images' => [
            'adapter' => 'imagick',
            'dimensions' => [
                'admin_thumbnail' => [
                    'system'       => true,
                    'pre_generate' => true,
                    'mode'         => '',
                    'width'        => 300,
                    'height'       => 300
                ],
            ]
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __NAMESPACE__ => __DIR__ . '/../view',
        ],
    ],

    'view_helpers' => [
        'factories' => [
            'media'             => MediaFactory::class,
            'mediaUrl'          => MediaUrlFactory::class,
        ],
    ],

    'assets' => [
        __NAMESPACE__ => [
            'target' => 'public/assets/admin/media42',
            'source' => 'vendor/fruit42/media42/assets/dist/',
        ],
    ],

    'migration' => [
        'directory'     => [
            __NAMESPACE__ => __DIR__ . '/../data/migrations'
        ],
    ],
];
