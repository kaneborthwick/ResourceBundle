<?php

namespace ResourceBundle;

use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [

    ConfigAbstractFactory::class => [
        Events\ResourceEventDispatcher::class => [
            'tower.event_manager',
        ],
    ],
    'dependencies' => [
        'aliases' => [
            'tower.resources.event_dispatcher' => Events\ResourceEventDispatcher::class,
        ],
    ],
];
