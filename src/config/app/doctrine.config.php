<?php
namespace TowerResourceBundle;

use Gedmo\Loggable\Entity;

return [
    'doctrine' => [
        'event_manager' => [
            'orm_default' => [
                'subscribers' => [
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\SoftDeleteable\SoftDeleteableListener',
                    'ORMMappedSuperClassSubscriber',
                    'Gedmo\Sortable\SortableListener',
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'drivers' => [
                    Entity::class => 'resource_bundle_config',
                ],
            ],
            'resource_bundle_config' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array'
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'filters' => [
                    'soft-deleteable' => 'Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter',
                ],
            ],
        ],
    ],
];
