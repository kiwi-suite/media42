<?php
namespace Media42;

use Media42\View\Helper\Service\MediaFactory;
use Media42\View\Helper\Service\MediaUrlFactory;

return [

    'media' => [
        'upload_host' => '',
        'path' => 'data/media/',
        'prepend_base_path' => true,
        'url' => null,

        'categories' => [
            'default' => 'media.category.default'
        ],

        'allowed_mime_types' => null,

        'images' => [
            'adapter' => 'imagick',
            'dimensions' => [
                'admin_thumbnail' => [
                    'name'         => 'admin_thumbnail',
                    'label'        => 'Admin Thumbnail',
                    'system'       => true,
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

    'migration' => [
        'directory'     => [
            __NAMESPACE__ => __DIR__ . '/../data/migrations'
        ],
    ],
];
