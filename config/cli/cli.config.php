<?php
namespace Media42;

use Media42\Command\BulkImportCommand;

return [
    'cli' => [
        'media-import' => [
            'group'                     => '*',
            'route'                     => '<dir> [--category=]',
            'command-name'              => BulkImportCommand::class,
            'description'               => 'bulk import media files from a directory',
            'short_description'         => 'bulk import media files from a directory',
            'options_descriptions'      => [
                '--category'            => 'the category the files are assigned to. default: "default"',
            ],
        ],
    ]
];
