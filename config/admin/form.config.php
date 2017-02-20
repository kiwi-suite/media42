<?php
namespace Media42;

use Core42\Form\Service\ElementFactory;
use Media42\Form\Service\UploadFormFactory;
use Media42\Form\UploadForm;
use Media42\FormElements\File;
use Media42\FormElements\Media;
use Media42\FormElements\Service\ImageFactory;

return [
    'form_elements' => [
        'factories' => [
            UploadForm::class => UploadFormFactory::class,

            Media::class    => ElementFactory::class,
            File::class     => ElementFactory::class,

            'image'         => ImageFactory::class,
        ],
        'aliases' => [
            'media'         => Media::class,
        ],
    ],
];
