<?php
namespace Media42;

use Media42\MediaEvent;
use Media42\MediaUrl;
use Media42\Service\EventManagerFactory;
use Media42\Service\MediaOptionsFactory;
use Media42\Service\MediaUrlFactory;

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

    'service_manager' => [
        'factories' => [
            'Imagine' => 'Admin42\Imagine\Service\ImagineFactory',

             MediaOptions::class   => MediaOptionsFactory::class,
             MediaUrl::class       => MediaUrlFactory::class,

            'Media42\EventManager' => EventManagerFactory::class,

            \Media42\Link\Adapter\MediaLink::class => \Media42\Link\Adapter\Service\MediaLinkFactory::class,
        ],
    ],

    'link' => [
        'adapter' => [
            'media'  => 'Admin42\Link\MediaLink',
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __NAMESPACE__ => __DIR__ . '/../view',
        ],
    ],

    'view_helpers' => [
        'factories' => [
            'media'            => \Media42\View\Helper\Service\MediaFactory::class,
            'mediaUrl'         => \Media42\View\Helper\Service\MediaUrlFactory::class,
        ],
    ],

    'assets' => [
        __NAMESPACE__ => [
            'target' => 'public/assets/media42/core',
            'source' => 'module/media42/assets/dist/',
        ],
    ],

    'migration' => [
        'directory'     => [
            __NAMESPACE__ => __DIR__ . '/../data/migrations'
        ],
    ],
];
