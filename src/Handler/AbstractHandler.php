<?php

namespace ResourceBundle\Handler;

use Psr\Http\Server\MiddlewareInterface;
use ResourceBundle\Events\ResourceEventDispatcher;
use ResourceBundle\Factory\NewResourceFactory;
use ResourceBundle\Provider\ResourcesCollectionProviderInterface;
use ResourceBundle\Provider\SingleResourceProvider;
use ResourceBundle\Repository\EntityRepository;
use ResourceBundle\Request\RequestConfiguration;
use ResourceBundle\Request\RequestConfigurationFactory;
use ResourceBundle\StateMachine\StateMachineInterface;
use Towersystems\Resource\Factory\FactoryInterface;
use Towersystems\Resource\Metadata\Metadata;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

abstract class AbstractHandler implements MiddlewareInterface {

	/**
	 * [$container description]
	 * @var [type]
	 */
	protected $container;

	/**
	 * [$config description]
	 * @var [type]
	 */
	protected $config;

	/**
	 * [$requestConfigurationFactory description]
	 * @var [type]
	 */
	protected $requestConfigurationFactory;

	/**
	 * [$repository description]
	 * @var [type]
	 */
	protected $repository;

	/**
	 * [$resourcesCollectionProvider description]
	 * @var [type]
	 */
	protected $resourcesCollectionProvider;

	/**
	 * [$singleResourceProvider description]
	 * @var [type]
	 */
	protected $singleResourceProvider;

	/**
	 * [$newResourceFactory description]
	 * @var [type]
	 */
	protected $newResourceFactory;

	/**
	 * [$factory description]
	 * @var [type]
	 */
	protected $factory;

	/**
	 * [$metadata description]
	 * @var [type]
	 */
	protected $metadata;

	/**
	 * [$eventDispatcher description]
	 * @var [type]
	 */
	protected $eventDispatcher;

	/**
	 * [$serializer description]
	 * @var [type]
	 */
	protected $serializer;

	/**
	 * [$statemachine description]
	 * @var [type]
	 */
	protected $statemachine;

	/** @var [type] [description] */
	protected $templates;

	/**
	 * [__construct description]
	 * @param [type] $container [description]
	 * @param [type] $config    [description]
	 */
	public function __construct(
		$container,
		RequestConfigurationFactory $requestConfigurationFactory,
		$config,
		EntityRepository $repository,
		ResourcesCollectionProviderInterface $resourcesCollectionProvider,
		SingleResourceProvider $singleResourceProvider,
		NewResourceFactory $newResourceFactory,
		FactoryInterface $factory,
		Metadata $metadata,
		ResourceEventDispatcher $eventDispatcher,
		$serializer,
		StateMachineInterface $statemachine,
		TemplateRendererInterface $templates
	) {
		$this->container = $container;
		$this->config = $config;
		$this->requestConfigurationFactory = $requestConfigurationFactory;
		$this->repository = $repository;
		$this->resourcesCollectionProvider = $resourcesCollectionProvider;
		$this->singleResourceProvider = $singleResourceProvider;
		$this->newResourceFactory = $newResourceFactory;
		$this->factory = $factory;
		$this->metadata = $metadata;
		$this->eventDispatcher = $eventDispatcher;
		$this->serializer = $serializer;
		$this->statemachine = $statemachine;
		$this->templates = $templates;
	}

	/**
	 * {@iheritdoc}
	 */
	public function process(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Server\RequestHandlerInterface $handler
	)
	: \Psr\Http\Message\ResponseInterface{

		$options = $request->getAttribute('Zend\Expressive\Router\RouteResult')->getMatchedRoute()->getOptions();
		$action = isset($options['action']) ? $options['action'] . 'Action' : '';

		if (!method_exists($this, $action)) {
			return new EmptyResponse(404);
		}

		try {
			return $this->$action($request, $handler);
		} catch (ResourceNotFoundException $e) {
			return new EmptyResponse(404);
		}
	}

	/**
	 * [findOr404 description]
	 * @param  RequestConfiguration $configuration [description]
	 * @return [type]                              [description]
	 */
	protected function findOr404(RequestConfiguration $configuration) {
		if (null === $resource = $this->singleResourceProvider->get($configuration, $this->repository)) {
			throw new ResourceNotFoundException();
		}
		return $resource;
	}
}
