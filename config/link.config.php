<?php
namespace Media42;

use Media42\Link\Adapter\MediaLink;

return [
    'link' => [
        'adapter' => [
            'media' => MediaLink::class,
        ],
    ],
];
