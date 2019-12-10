<?php

declare (strict_types = 1);

namespace ResourceBundle\Handler;

use JMS\Serializer\SerializationContext;
use ResourceBundle\Handler\AbstractHandler;
use ResourceBundle\Hydrator\DoctrineObjectHydrator;
use Towersystems\Resource\ResourceActions;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\TextResponse;

class ResourceHandler extends AbstractHandler {

	/**
	 * [indexAction description]
	 * @param  \Psr\Http\Message\ServerRequestInterface $request [description]
	 * @param  \Psr\Http\Server\RequestHandlerInterface $handler [description]
	 * @return [type]                                            [description]
	 */
	public function indexAction(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Server\RequestHandlerInterface $handler
	)
	: \Psr\Http\Message\ResponseInterface{

		$configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
		$configuration->isHtmlRequest();
		$resources = $this->resourcesCollectionProvider->get($configuration, $this->repository);

		if ($configuration->isHtmlRequest()) {
			$template = $this->getOption("template");

			return new HtmlResponse($this->templates->render($template, [
				'resources' => $resources,
				'configuration' => $configuration,
			]));

		}

		$jsonContent = $this->serialize($resources);

		return new TextResponse($jsonContent);
	}

	/**
	 * [showAction description]
	 * @param  \Psr\Http\Message\ServerRequestInterface $request [description]
	 * @param  \Psr\Http\Server\RequestHandlerInterface $handler [description]
	 * @return [type]                                            [description]
	 */
	public function showAction(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Server\RequestHandlerInterface $handler
	)
	: \Psr\Http\Message\ResponseInterface{

		$configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
		$resource = $this->findOr404($configuration);

		if ($configuration->isHtmlRequest()) {
			$template = $this->getOption("template");

			return new HtmlResponse($this->templates->render($template, [
				'resource' => $resource,
				'configuration' => $configuration,
			]));

		}
		$jsonContent = $this->serialize($resource);
		return new TextResponse($jsonContent);
	}

	/**
	 * [updateAction description]
	 * @param  \Psr\Http\Message\ServerRequestInterface $request [description]
	 * @param  \Psr\Http\Server\RequestHandlerInterface $handler [description]
	 * @return [type]                                            [description]
	 */
	public function updateAction(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Server\RequestHandlerInterface $handler
	): \Psr\Http\Message\ResponseInterface{

		$configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
		$resource = $this->findOr404($configuration);
		$data = $request->getParsedBody() ?? [];

		$this->eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource, ['data' => $data]);

		$entityManager = $this->container->get('doctrine.entity_manager.orm_default');
		$hydrator = new DoctrineObjectHydrator($entityManager);
		$hydrator->hydrate($data, $resource);

		if (!$configuration->isHtmlRequest()) {
			try {
				$entityManager->persist($resource);
				$entityManager->flush();
				// temp hack to save relations
				$hydrator->hydrate($data, $resource);
				$entityManager->flush();

				$this->eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource);
			} catch (\Exception $error) {
				throw $error;
			}

			$jsonContent = $this->serialize($resource);
			return new TextResponse($jsonContent);
		}

		$template = $this->getOption("template");

		return new HtmlResponse($this->templates->render($template, [
			'resource' => $resource,
			'configuration' => $configuration,
		]));
	}

	/**
	 * [deleteAction description]
	 * @param  \Psr\Http\Message\ServerRequestInterface $request [description]
	 * @param  \Psr\Http\Server\RequestHandlerInterface $handler [description]
	 * @return [type]                                            [description]
	 */
	public function deleteAction(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Server\RequestHandlerInterface $handler
	): \Psr\Http\Message\ResponseInterface{

		$configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
		$resource = $this->findOr404($configuration);
		$this->repository->remove($resource);

		return new EmptyResponse(200);
	}

	public function createAction(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Server\RequestHandlerInterface $handler
	): \Psr\Http\Message\ResponseInterface{

		$configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
		$newResource = $this->newResourceFactory->create($configuration, $this->factory);

		$data = $request->getParsedBody();

		$entityManager = $this->container->get('doctrine.entity_manager.orm_default');
		$this->eventDispatcher->dispatchPreEvent(ResourceActions::CREATE, $configuration, $newResource);

		if (!$configuration->isHtmlRequest()) {
			$hydrator = new DoctrineObjectHydrator($entityManager);
			$hydrator->hydrate($data, $newResource);

			try {
				$entityManager->persist($newResource);
				$entityManager->flush();
				$this->eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $newResource);
			} catch (\Exception $error) {
				throw $error;
			}

			$jsonContent = $this->serialize($newResource);

			return new TextResponse($jsonContent);
		}

		$template = $this->getOption("template");

		return new HtmlResponse($this->templates->render($template, [
			'resource' => $newResource,
			'configuration' => $configuration,
		]));
	}

	/**
	 * [applyStateMachineTransitionAction description]
	 *
	 * @param  \Psr\Http\Message\ServerRequestInterface $request [description]
	 * @param  \Psr\Http\Server\RequestHandlerInterface $handler [description]
	 * @return [type]                                            [description]
	 */
	public function applyStateMachineTransitionAction(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Server\RequestHandlerInterface $handler
	): \Psr\Http\Message\ResponseInterface{

		$configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
		$resource = $this->findOr404($configuration);

		$this->eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource);

		if (!$this->statemachine->can($configuration, $resource)) {
			throw new BadRequestHttpException();
		}

		if ($configuration->hasStateMachine()) {
			$this->statemachine->apply($configuration, $resource);
		}

		$entityManager = $this->container->get('doctrine.entity_manager.orm_default');
		$entityManager->flush();
		$serializer = \JMS\Serializer\SerializerBuilder::create()->build();
		$jsonContent = $serializer->serialize($resource, 'json');

		return new TextResponse($jsonContent);
	}

	protected function serialize($data) {
		$context = new SerializationContext();
		$context->setSerializeNull(true);
		$jsonContent = $this->serializer->serialize($data, 'json', $context);
		return $jsonContent;
	}
}
