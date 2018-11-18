<?php

namespace ResourceBundle\Middleware;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 *
 */
class DoctrineTargetEntitiesResolverMiddleware implements MiddlewareInterface
{

    const CONFIG_KEY = 'tower_resources';
    const RESOURCE_CONFIG_KEY = "resources";

    /**
     * [$container description]
     * @var [type]
     */
    private $container;

    /**
     * [$registry description]
     * @var [type]
     */
    private $registry;

    /**
     * [__construct description]
     * @param ContainerInterface $container [description]
     */
    public function __construct(
        ContainerInterface $container
    ) {

        $this->container = $container;

        $registry = $container->get("towersystems.resource_registry");

        if (!$registry) {
            throw new \Exception("Error Processing Request", 1);
        }

        $this->registry = $registry;
    }

    /**
     * [process description]
     * @param  ServerRequestInterface $request  [description]
     * @param  DelegateInterface      $delegate [description]
     * @return [type]                           [description]
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $container = $this->container;
        $em = $container->get("doctrine.entity_manager.orm_default")->getEventManager();
        $metafields = $this->registry->getAll();

        $rtel = new \Doctrine\ORM\Tools\ResolveTargetEntityListener;

        foreach ($metafields as $alias => $metafield) {
            $test[$metafield->getClass('interface')] = $metafield->getClass('model');
            $rtel->addResolveTargetEntity($metafield->getClass('interface'), $metafield->getClass('model'), []);
        }

        $em->addEventSubscriber($rtel);
        return $handler->handle($request);
    }
}
