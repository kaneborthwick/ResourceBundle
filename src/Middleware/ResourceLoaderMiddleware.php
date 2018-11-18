<?php

namespace ResourceBundle\Middleware;

use ArrayObject;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ResourceBundle\Routing\ResourceLoader;

/**
 *
 */
class ResourceLoaderMiddleware implements MiddlewareInterface {

	const CONFIG_KEY = "towersystems_resource";
	const ROUTE_CONFIG_KEY = "routes";

	/**
	 * [$config description]
	 * @var [type]
	 */
	private $config;

	/**
	 * [$resourceLoader description]
	 * @var [type]
	 */
	private $resourceLoader;

	/**
	 * [__construct description]
	 * @param ContainerInterface $container [description]
	 */
	public function __construct(
		ResourceLoader $resourceLoader,
		$config
	) {
		$this->config = $config;
		$this->resourceLoader = $resourceLoader;
	}

	/**
	 * [process description]
	 * @param  ServerRequestInterface $request  [description]
	 * @param  DelegateInterface      $delegate [description]
	 * @return [type]                           [description]
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{

		$config = $this->config;

		if (!(is_array($config) || $config instanceof ArrayObject)) {
			throw new \Exception('Config must be an array or an instance of ArrayObject');
		}

		if (!array_key_exists(self::CONFIG_KEY, $config)) {
			throw new \Exception('Cannot find a `' . self::CONFIG_KEY . '` key in the config array');
		}

		$towerConfig = $config[self::CONFIG_KEY];

		if (!array_key_exists(self::ROUTE_CONFIG_KEY, $towerConfig)) {
			throw new \Exception('Cannot find a `' . self::CONFIG_KEY . '` key in the config array');
		}

		foreach ($towerConfig[self::ROUTE_CONFIG_KEY] as $resource) {
			$this->resourceLoader->load($resource);
		}

		return $handler->handle($request);
	}
}
