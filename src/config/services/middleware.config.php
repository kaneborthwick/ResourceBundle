<?php

namespace ResourceBundle;

use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [

    ConfigAbstractFactory::class => [
        Middleware\ResourceLoaderMiddleware::class => [
            'tower.loader.resource_loader',
            'config',
        ],

        Middleware\ResourceRegistryMiddleware::class => [
            'towersystems.resource_registry',
            'config',
        ],

        Middleware\DeleteableFilterEnablerMiddleware::class => [
            'doctrine.entity_manager.orm_default',
        ],
    ],

    'dependencies' => [
        'factories' => [
            Middleware\DoctrineTargetEntitiesResolverMiddleware::class => Factory\ContainerFactory::class,
            Middleware\EventsMiddleware::class => Factory\ContainerFactory::class,
            Middleware\ResourceItemRegisterMiddleware::class => Factory\ContainerFactory::class,
            Routing\ResourceLoader::class => Routing\Factory\ResourceLoaderFactory::class,
        ],

        'aliases' => [
            'tower.loader.resource_loader' => Routing\ResourceLoader::class,
        ],
    ],

];
