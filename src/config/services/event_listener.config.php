<?php

namespace ResourceBundle;

use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [

    ConfigAbstractFactory::class => [
        EventListener\ORMMappedSuperClassSubscriber::class => [
            'towersystems.resource_registry',
        ],
    ],

    'dependencies' => [
        'aliases' => [
            'ORMMappedSuperClassSubscriber' => EventListener\ORMMappedSuperClassSubscriber::class,
        ],
    ],

];
