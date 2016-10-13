<?php
namespace Media42;

use Media42\View\Helper\Service\MediaFactory;
use Media42\View\Helper\Service\MediaUrlFactory;

return [
    'assets' => [
        'directories' => [
            'media42' => [
                'target' => 'admin/media42',
                'source' => 'vendor/fruit42/media42/assets/dist/',
            ],
        ],
    ],
];
