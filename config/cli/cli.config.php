<?php
namespace Media42;


use Media42\Command\RegenerateImagesCommand;

return [
    'cli' => [
        'media-regenerate' => [
            'group'                     => 'media',
            'route'                     => 'media-regenerate',
            'command-name'              => RegenerateImagesCommand::class,
            'description'               => 'Regenerate all images on given media dimension config',
            'short_description'         => 'Regenerate all images',
        ],
    ],
];
