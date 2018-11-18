<?php

namespace ResourceBundle\Database;

use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        DatabaseSchemaCreator::class => [
            'doctrine.entity_manager.orm_default',
            'towersystems.resource_registry',
        ],
    ],

    'dependencies' => [
        'aliases' => [
            'tower.database_schema_creator' => DatabaseSchemaCreator::class,
        ],
    ],
];
