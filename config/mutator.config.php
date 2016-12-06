<?php
namespace Media42;

use Media42\Mutator\Strategy\MediaStrategy;
use Media42\Mutator\Strategy\Service\MediaStrategyFactory;

return [
    'mutator' => [
        'factories' => [
            MediaStrategy::class                    => MediaStrategyFactory::class,
        ],
        'aliases' => [
            'media'                                 => MediaStrategy::class,
            'image'                                 => MediaStrategy::class,
        ],
    ],
];
