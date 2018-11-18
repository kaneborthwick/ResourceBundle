<?php

namespace ResourceBundle;

return [
    'dependencies' => [
        'invokables' => [
            Factory\NewResourceFactory::class,
            \Hateoas\Representation\Factory\PagerfantaFactory::class,
        ],
        'aliases' => [
            'tower.resource.new_resource_factory' => Factory\NewResourceFactory::class,
        ],
    ],
];
