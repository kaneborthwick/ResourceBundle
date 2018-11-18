<?php

namespace ResourceBundle\Factory;

use Interop\Container\ContainerInterface;

/**
 *
 */
class EntityManagerProviderFactory
{

    /**
     * [__invoke description]
     * @param  ContainerInterface $container     [description]
     * @param  [type]             $requestedName [description]
     * @return [type]                            [description]
     */
    function __invoke(ContainerInterface $container, $requestedName)
    {
        return new $requestedName($container->get("doctrine.entity_manager.orm_default"));
    }
}
