<?php
namespace Media42;

use Media42\Event\MediaEventListener;
use Media42\Event\Service\MediaEventListenerFactory;
use Media42\Service\EventManagerFactory;

return [
    'service_manager' => [
        'factories' => [
            'Media42\EventManager'      => EventManagerFactory::class,
            MediaEventListener::class   => MediaEventListenerFactory::class,
        ],
    ],
];
