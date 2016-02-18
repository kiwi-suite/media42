<?php
namespace Media42;

return [
    'navigation' => [
        'containers' => [
            'admin42' => [
                'content' => [
                    'pages' => [
                        'media' => [
                            'options' => [
                                'label' => 'label.media',
                                'route' => 'admin/media',
                                'icon' => 'fa fa-picture-o fa-fw',
                                'order' => 5000,
                                'permission' => 'route/admin/media'
                            ],
                        ],
                    ]
                ],
            ],
        ],
    ],
];
