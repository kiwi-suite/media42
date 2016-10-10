<?php
namespace Media42;

use Media42\View\Helper\Form\FormMedia;
use Media42\View\Helper\MediaOptions;
use Media42\View\Helper\Service\MediaOptionsFactory;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'view_helpers' => [
        'factories' => [
            FormMedia::class        => InvokableFactory::class,
            MediaOptions::class     => MediaOptionsFactory::class,
        ],
        'aliases' => [
            'formMedia'             => FormMedia::class,
            'mediaOptions'          => MediaOptions::class,
        ],
    ],
];
