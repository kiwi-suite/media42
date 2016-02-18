<?php
namespace Media42;

return [
    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'media' => [
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route' => 'media/[:referrer/[:category/]]',
                            'defaults' => [
                                'controller' => __NAMESPACE__ . '\Media',
                                'action' => 'index',
                                'referrer' => 'index',
                                'category' => 'default',
                            ],
                            'constraints' => [
                                'referrer' => '(index|modal)'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'upload' => [
                                'type' => 'Zend\Mvc\Router\Http\Literal',
                                'options' => [
                                    'route' => 'upload/',
                                    'defaults' => [
                                        'action' => 'upload'
                                    ],
                                ],
                            ],
                            'crop' => [
                                'type' => 'Core42\Mvc\Router\Http\AngularSegment',
                                'options' => [
                                    'route' => 'crop/:id/:dimension/',
                                    'defaults' => [
                                        'action' => 'crop'
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => 'Core42\Mvc\Router\Http\AngularSegment',
                                'options' => [
                                    'route' => 'edit/:id/',
                                    'defaults' => [
                                        'action' => 'edit'
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type' => 'Core42\Mvc\Router\Http\AngularSegment',
                                'options' => [
                                    'route' => 'delete/',
                                    'defaults' => [
                                        'action' => 'delete'
                                    ],
                                ],
                            ],
                            'stream' => [
                                'type' => 'Core42\Mvc\Router\Http\AngularSegment',
                                'options' => [
                                    'route' => 'stream/:id/[:dimension/]',
                                    'defaults' => [
                                        'action' => 'stream'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
