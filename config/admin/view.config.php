<?php
namespace Media42;

use Media42\View\Helper\Form\FormMedia;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'view_helpers' => [
        'factories' => [
            FormMedia::class        => InvokableFactory::class,
        ],
        'aliases' => [
            'formMedia'             => FormMedia::class,
        ],
    ],
];
