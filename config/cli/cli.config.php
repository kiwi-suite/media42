<?php
namespace Media42;


use Media42\Command\RegenerateImagesCommand;

return [
    'cli' => [
        'media-regenerate' => [
            'group'                     => 'media',
            'route'                     => 'media-regenerate [--media=]',
            'command-name'              => RegenerateImagesCommand::class,
            'description'               => 'Regenerate all/one images on given media dimension config',
            'short_description'         => 'Regenerate all/one images',
            'options_descriptions'      => [
                '--media'           => 'Given MediaId'
            ]
        ],
    ],
];
