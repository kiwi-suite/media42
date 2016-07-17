<?php
namespace Media42;

use Media42\Command\BulkImportCommand;
use Media42\Command\RegenerateCommand;

return [
    'cli' => [
        'media-import' => [
            'route'                     => '<dir> [--category=]',
            'command-name'              => BulkImportCommand::class,
            'description'               => 'bulk import media files from a directory',
            'short_description'         => 'bulk import media files from a directory',
            'options_descriptions'      => [
                '--category'            => 'the category the files are assigned to. default: "default"',
            ],
        ],

        'media-regenerate-images' => [
            'route'                     => '[--force] [--dimension=] [--category=]',
            'command-name'              => RegenerateCommand::class,
            'description'               => 'Regenerate all Images',
            'short_description'         => 'Regenerate all Images',
        ],
    ]
];
