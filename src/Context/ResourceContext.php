<?php

namespace ResourceBundle\Context;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 *  Get the resource for the current request context
 */
class ResourceContext extends ResourceContextInterface
{

    /**
     * [$request description]
     * @var [type]
     */
    private $request;

    /**
     * [$container description]
     * @var [type]
     */
    private $container;

    /**
     * [$config description]
     * @var [type]
     */
    private $config;

    /**
     * [__construct description]
     * @param ServerRequestInterface $request [description]
     */
    public function __construct(
        ServerRequestInterface $request,
        ContainerInterface $container,
        $config
    ) {
        $this->request = $request;
        $this->container = $container;
        $this->config = $config;
    }

    public function getRepository()
    {
    }
}
