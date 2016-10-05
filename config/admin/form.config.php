<?php
namespace Media42;

use Core42\Form\Service\ElementFactory;
use Media42\FormElements\File;
use Media42\FormElements\Media;

return [
    'form_elements' => [
        'factories' => [
            Media::class    => ElementFactory::class,
            File::class     => ElementFactory::class,
        ],
        'aliases' => [
            'media'         => Media::class,
        ],
    ],
];
