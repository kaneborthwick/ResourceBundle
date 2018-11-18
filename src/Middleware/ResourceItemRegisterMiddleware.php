<?php

namespace ResourceBundle\Middleware;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ResourceBundle\Request\RequestConfigurationFactory;

/**
 *
 */
class ResourceItemRegisterMiddleware implements MiddlewareInterface {

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
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
		$container = $this->container;
		$resourceConfig = $this->getResourceConfigData($container);
		$metafields = $this->registry->getAll();

		foreach ($metafields as $metafield) {
			$this->configureInvokableResourceItem($container, $metafield);
			$this->configureResourceItemFactory($container, $metafield);
			$this->configureResourceRepository($container, $metafield);
			$this->configureResourceItemController($container, $metafield);
		}

		return $handler->handle($request);
	}

	/**
	 * [getConfigData description]
	 * @param  ContainerInterface $container [description]
	 * @return [type]                        [description]
	 */
	private function getResourceConfigData(ContainerInterface $container) {
		$config = $container->get('config');
		return $config[self::CONFIG_KEY][self::RESOURCE_CONFIG_KEY] ?? [];
	}

	/**
	 * [configureResourceItem description]
	 * @param  ContainerInterface $container   [description]
	 * @param  [type]             $serviceName [description]
	 * @param  [type]             $service     [description]
	 * @return [type]                          [description]
	 */
	private function configureInvokableResourceItem(
		ContainerInterface $container,
		$metafield
	) {
		$serviceName = $metafield->getServiceName("resource");
		$container->setInvokableClass($serviceName, $metafield->getClass('model'));
		$container->setShared($serviceName, false);
	}

	private function configureResourceItemFactory(
		ContainerInterface $container,
		$metafield
	) {
		$serviceName = $metafield->getServiceName("factory");

		if ($container->has($serviceName)) {
			return;
		}

		$factory = new \Towersystems\Resource\Factory\Factory($metafield->getClass("model"));
		$container->setService($serviceName, $factory);
	}

	private function configureResourceRepository(
		ContainerInterface $container,
		$metafield
	) {

		$serviceName = $metafield->getServiceName("repository");

		if ($container->has($serviceName)) {
			return;
		}

		$em = $container->get("doctrine.entity_manager.orm_default");

		$factory = new class($em, $metafield) {

			protected $em;
			protected $metafield;

			public function __construct($em, $metafield) {
				$this->em = $em;
				$this->metafield = $metafield;
			}

			function __invoke(ContainerInterface $container, $requestedName) {
				return $this->em->getRepository($this->metafield->getClass("model")); // temp
			}
		};

		$container->setFactory($serviceName, $factory);
	}

	/**
	 * iduno about this
	 *
	 * @param  ContainerInterface $container [description]
	 * @param  [type]             $alias     [description]
	 * @return [type]                        [description]
	 */
	private function configureResourceItemController(
		ContainerInterface $container,
		$metafield
	) {

		$tmp = new class($container, $metafield) {
			protected $container;
			protected $metafield;

			public function __construct($container, $metafield) {
				$this->container = $container;
				$this->metafield = $metafield;
			}

			function __invoke(ContainerInterface $container, $requestedName) {

				$metafield = $this->metafield;

				try {
					$class = $metafield->getClass('handler');
				} catch (\Exception $error) {
					$class = '\ResourceBundle\Handler\ResourceHandler';
				}

				$container = $this->container;

				$repository = $this->container->get($this->metafield->getServiceName('repository'));
				$factory = $this->container->get($this->metafield->getServiceName('factory'));

				return new $class(
					$container,
					new RequestConfigurationFactory(), // wont load from container ? ??
					$container->get("config"),
					$repository,
					$container->get("tower.resource.resource_collection_provider"),
					$container->get("tower.resource.single_resource_provider"),
					$container->get("tower.resource.new_resource_factory"),
					$factory,
					$metafield,
					$container->get("tower.resources.event_dispatcher"),
					$container->get("tower.serializer"),
					$container->get('tower.resource.state_machine'),
					$container->get(\Zend\Expressive\Template\TemplateRendererInterface::class)
				);
			}

		};

		$serviceName = $metafield->getServiceName('handler');
		$this->container->setFactory($serviceName, $tmp);
	}
}
