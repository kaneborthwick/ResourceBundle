<?php

namespace ResourceBundle\Provider;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Pagerfanta\Pagerfanta;
use ResourceBundle\Request\RequestConfiguration;
use ResourceBundle\Resolver\ResourcesResolverInterface;
use Towersystems\Resource\Repository\RepositoryInterface;

class ResourcesCollectionProvider implements ResourcesCollectionProviderInterface {

	/**
	 * @var ResourcesResolverInterface
	 */
	private $resourcesResolver;

	/**
	 * @var PagerfantaFactory
	 */
	private $pagerfantaRepresentationFactory;

	/**
	 * @param ResourcesResolverInterface $resourcesResolver
	 * @param PagerfantaFactory $pagerfantaRepresentationFactory
	 */
	public function __construct(
		ResourcesResolverInterface $resourcesResolver,
		PagerfantaFactory $pagerfantaRepresentationFactory
	) {
		$this->resourcesResolver = $resourcesResolver;
		$this->pagerfantaRepresentationFactory = $pagerfantaRepresentationFactory;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository) {
		$resources = $this->resourcesResolver->getResources($requestConfiguration, $repository);

		if ($resources instanceof Pagerfanta) {
			$resources->setMaxPerPage($requestConfiguration->getPaginationMaxPerPage());
			$resources->setCurrentPage($requestConfiguration->getPage());
			$resources->getCurrentPageResults();
			$route = new Route('l');
			return $this->pagerfantaRepresentationFactory->createRepresentation($resources, $route);
		}

		return $resources;
	}

}