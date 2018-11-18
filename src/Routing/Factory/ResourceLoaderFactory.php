<?php

namespace ResourceBundle\Routing\Factory;

use Interop\Container\ContainerInterface;
use ResourceBundle\Routing\ResourceLoader;

/**
 *
 */
class ResourceLoaderFactory
{

    /**
     * [__invoke description]
     * @param  ContainerInterface $container     [description]
     * @param  [type]             $requestedName [description]
     * @return [type]                            [description]
     */
    function __invoke(ContainerInterface $container, $requestedName)
    {
        return new ResourceLoader(
            $container->get(\Zend\Expressive\Router\RouterInterface::class),
            $container,
            $container->get('towersystems.resource_registry')
        );
    }
}
