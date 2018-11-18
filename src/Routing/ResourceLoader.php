<?php
namespace ResourceBundle\Routing;

use Doctrine\Common\Inflector\Inflector;
use Gedmo\Sluggable\Util\Urlizer;
use Towersystems\Resource\Loader\LoaderInterface;
use Towersystems\Resource\Metadata\RegistryInterface;
use Zend\Expressive\Router\Route;
use Zend\Router\RouteStackInterface;

/**
 *
 */
final class ResourceLoader implements LoaderInterface {

	const API_URL_SEGEMENT_PREFIX = "/api";

	/**
	 * [$routerStack description]
	 * @var [type]
	 */
	private $routerStack;

	/**
	 * [$container description]
	 * @var [type]
	 */
	private $container;

	/**
	 * [__construct description]
	 * @param RouteStackInterface $routerStack [description]
	 */
	function __construct(
		\Zend\Expressive\Router\FastRouteRouter $routerStack,
		$container = null,
		RegistryInterface $resourceRegistry
	) {
		$this->routerStack = $routerStack;
		$this->container = $container;
		$this->resourceRegistry = $resourceRegistry;
	}

	/**
	 * {@inheritdoc}
	 */
	public function load($resource, $type = null) {

		$configuration = $resource;

		$routesToGenerate = ['show', 'index', 'create', 'update', 'delete'];

		if (!empty($configuration['only'])) {
			$routesToGenerate = $configuration['only'];
		}

		if (!empty($configuration['except'])) {
			$routesToGenerate = array_diff($routesToGenerate, $configuration['except']);
		}

		$metadata = $this->resourceRegistry->get($configuration['alias']);
		$rootPath = sprintf('/%s', $configuration['path'] ?? Urlizer::urlize($metadata->getPluralName()));
		$identifier = sprintf('{%s}', 'id');

		if (in_array('index', $routesToGenerate)) {
			$handler = $metadata->getServiceName("handler");
			$indexRoute = $this->createRoute([], $configuration, $handler, $rootPath, 'index', ['GET'], $this->getRouteName([], $configuration, 'index'));
			$this->routerStack->addRoute($indexRoute);
		}

		if (in_array('show', $routesToGenerate)) {
			$handler = $metadata->getServiceName("handler");
			$showRoute = $this->createRoute([], $configuration, $handler, $rootPath . '/' . $identifier, 'show', ['GET'], $this->getRouteName([], $configuration, 'show'));
			$this->routerStack->addRoute($showRoute);
		}

		if (in_array('create', $routesToGenerate)) {
			$handler = $metadata->getServiceName("handler");
			$showRoute = $this->createRoute([], $configuration, $handler, $rootPath, 'create', ['POST'], $this->getRouteName([], $configuration, 'create'));
			$this->routerStack->addRoute($showRoute);
		}

		if (in_array('update', $routesToGenerate)) {
			$handler = $metadata->getServiceName("handler");
			$showRoute = $this->createRoute([], $configuration, $handler, $rootPath . '/' . $identifier, 'update', ['PUT'], $this->getRouteName([], $configuration, 'update'));
			$this->routerStack->addRoute($showRoute);
		}

		if (in_array('delete', $routesToGenerate)) {
			$handler = $metadata->getServiceName("handler");
			$showRoute = $this->createRoute([], $configuration, $handler, $rootPath . '/' . $identifier, 'delete', ['DELETE'], $this->getRouteName([], $configuration, 'delete'));
			$this->routerStack->addRoute($showRoute);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function supports($resource, $type = null) {
	}

	/**
	 * [createRoute description]
	 * @return [type] [description]
	 */
	private function createRoute($metadata, $configuration, $handler, $path, $actionName, $methods, $name) {

		if (isset($configuration["criteria"]) && is_array($configuration["criteria"])) {
			foreach ($configuration["criteria"] as $segement => $matching) {
				$path = "/" . Inflector::pluralize($segement) . "/" . $matching . $path;
			}
		}

		// need to lazy load the routes;
		// not sure if there is already a service with this container
		$middlewareContainer = new \Zend\Expressive\MiddlewareContainer($this->container);

		$route = new Route(
			self::API_URL_SEGEMENT_PREFIX . $path,
			new \Zend\Expressive\Middleware\LazyLoadingMiddleware($middlewareContainer, $handler),
			$methods,
			$name
		);

		$route->setOptions([
			'action' => $actionName,
		]);

		return $route;
	}

	/**
	 * [getRouteName description]
	 * @param  [type] $metadata      [description]
	 * @param  [type] $configuration [description]
	 * @param  [type] $actionName    [description]
	 * @return [type]                [description]
	 */
	private function getRouteName($metadata, $configuration, $actionName) {
		return sprintf('%s_%s_%s', "towersystems", $configuration["alias"], $actionName);
	}
}
