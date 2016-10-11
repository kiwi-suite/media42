<?php
namespace Media42;

use Media42\View\Helper\Service\MediaFactory;
use Media42\View\Helper\Service\MediaUrlFactory;

return [
    'assets' => [
        'directories' => [
            'media42' => [
                'target' => 'public/assets/admin/media42',
                'source' => 'vendor/fruit42/media42/assets/dist/',
                'base_url'  => '/assets/admin/media42',
            ],
        ],
    ],
];
