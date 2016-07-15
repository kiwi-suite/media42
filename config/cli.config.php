<?php

return [
    'cli' => [
        'media-import' => [
            'route'                     => '<dir> [--category=]',
            'command-name'              => 'Media42\BulkImport',
            'description'               => 'bulk import media files from a directory',
            'short_description'         => 'bulk import media files from a directory',
            'options_descriptions'      => [
                '--category'            => 'the category the files are assigned to. default: "default"',
            ],
        ],

        'media-regenerate-images' => [
            'route'                     => '[--force] [--dimension=] [--category=]',
            'command-name'              => 'Media42\Regenerate',
            'description'               => 'Regenerate all Images',
            'short_description'         => 'Regenerate all Images',
        ],
    ]
];