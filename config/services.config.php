<?php
namespace Media42;

use Media42\Imagine\Service\ImagineFactory;
use Media42\Link\Adapter\MediaLink;
use Media42\Link\Adapter\Service\MediaLinkFactory;
use Media42\Service\EventManagerFactory;
use Media42\Service\MediaOptionsFactory;
use Media42\Service\MediaUrlFactory;

return [
    'service_manager' => [
        'factories' => [
            MediaOptions::class   => MediaOptionsFactory::class,
            MediaUrl::class       => MediaUrlFactory::class,

            'Imagine'              => ImagineFactory::class,

            'Media42\EventManager' => EventManagerFactory::class,

            MediaLink::class        => MediaLinkFactory::class,
        ],
    ],
];
