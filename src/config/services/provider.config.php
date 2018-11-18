<?php

namespace ResourceBundle;

use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [

    ConfigAbstractFactory::class => [
        Provider\ResourcesCollectionProvider::class => [
            'tower.resource.resource_resolver',
            \Hateoas\Representation\Factory\PagerfantaFactory::class,
        ],
    ],

    'dependencies' => [
        'aliases' => [
            'tower.resource.resource_collection_provider' => Provider\ResourcesCollectionProvider::class,
            'tower.resource.single_resource_provider' => Provider\SingleResourceProvider::class,
        ],

        'invokables' => [
            Provider\SingleResourceProvider::class,
        ],
    ],

];
