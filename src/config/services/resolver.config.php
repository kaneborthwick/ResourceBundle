<?php

namespace ResourceBundle;

use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [

    ConfigAbstractFactory::class => [
        Resolver\ResourcesResolver::class => [

        ],
    ],

    'dependencies' => [
        'aliases' => [
            'tower.resource.resource_resolver' => Resolver\ResourcesResolver::class,
        ],
    ],

];
