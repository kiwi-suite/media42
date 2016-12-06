<?php
namespace Media42;

use Core42\Mvc\Router\Http\AngularSegment;
use Media42\Controller\MediaController;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'media' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'media/[:referrer/:typeSelection/:categorySelection/]',
                            'defaults' => [
                                'controller' => MediaController::class,
                                'action' => 'index',
                                'referrer' => 'index',
                                'categorySelection' => '*',
                                'typeSelection' => '*',
                            ],
                            'constraints' => [
                                'referrer' => '(index|modal)',
                                'typeSelection' => '(\*|pdf|images)',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'upload' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => 'upload/[:id/]',
                                    'defaults' => [
                                        'action' => 'upload'
                                    ],
                                ],
                            ],
                            'crop' => [
                                'type' => AngularSegment::class,
                                'options' => [
                                    'route' => 'crop/:id/:dimension/',
                                    'defaults' => [
                                        'action' => 'crop'
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => AngularSegment::class,
                                'options' => [
                                    'route' => 'edit/:id/',
                                    'defaults' => [
                                        'action' => 'edit'
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type' => AngularSegment::class,
                                'options' => [
                                    'route' => 'delete/',
                                    'defaults' => [
                                        'action' => 'delete'
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
