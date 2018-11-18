<?php

namespace ResourceBundle;

use Request\RequestConfigurationFactory;
use ResourceBundle\StateMachine\StateMachine;
use SM\Callback\CascadeTransitionCallback;
use Towersystems\Resource\Metadata\Registry;
use Towersystems\Resource\StateMachine\Callback\ContainerAwareCallbackFactory;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        StateMachine::class => [
            'tower.state_machine_factory',
        ],

        CascadeTransitionCallback::class => [
            'tower.state_machine_factory',
        ],
    ],
    'dependencies' => [
        'abstract_factories' => [
            ConfigAbstractFactory::class,
        ],
        'delegators' => [
            \Zend\Expressive\Application::class => [
                \Zend\Expressive\Container\ApplicationConfigInjectionDelegator::class,
            ],
        ],

        'invokables' => [
            RequestConfigurationFactory::class,
            Registry::class,
            EventManager::class,
        ],

        'factories' => [
            'doctrine.entity_manager.orm_default' => \ContainerInteropDoctrine\EntityManagerFactory::class,
            'tower.serializer' => Factory\SerializerFactory::class,
            'tower.state_machine_factory' => Factory\StateMachineFactory::class,
            'CommandBus' => Factory\CommandBusFactory::class,
            ContainerAwareCallbackFactory::class => Factory\ContainerFactory::class,
        ],

        'aliases' => [
            'sm.callback.cascade_transition' => CascadeTransitionCallback::class,
            'towersystems.resource_registry' => Registry::class,
            'tower.event_manager' => EventManager::class,
            'tower.state_machine_callback_factory' => ContainerAwareCallbackFactory::class,
            'tower.resource.state_machine' => StateMachine::class,
        ],
    ],

];
