<?php

namespace ResourceBundle\Middleware;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ResourceBundle\Routing\ResourceLoader;
use Towersystems\Resource\Metadata\RegistryInterface;
use Towersystems\Resource\Model\ResourceInterface;

/**
 *
 */
class ResourceRegistryMiddleware implements MiddlewareInterface
{

    /**
     * [$config description]
     * @var [type]
     */
    private $config;

    /**
     * [$resourceLoader description]
     * @var [type]
     */
    private $registry;

    /**
     * [__construct description]
     * @param ContainerInterface $container [description]
     */
    public function __construct(
        RegistryInterface $registry,
        $config
    ) {
        $this->config = $config;
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
        $resources = $this->config["towersystems_resource"]["resources"];
        foreach ($resources as $alias => $configuration) {
            $this->validateResource($configuration['classes']['model']);
            $this->registry->addFromAliasAndConfiguration($alias, $configuration);
        }
        return $handler->handle($request);
    }

    /**
     * [validateResource description]
     * @param  string $class [description]
     * @return [type]        [description]
     */
    private function validateResource(string $class): void
    {
        if (!in_array(ResourceInterface::class, class_implements($class), true)) {
            throw new \Exception(sprintf(
                'Class "%s" must implement "%s" to be registered as a resource.',
                $class,
                ResourceInterface::class
            ));
        }
    }
}
