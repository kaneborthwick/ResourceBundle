<?php

namespace ResourceBundle\Resolver;

use ResourceBundle\Request\RequestConfiguration;
use Towersystems\Grid\Parameters;
use Towersystems\Resource\Repository\RepositoryInterface;

class ResourcesResolver implements ResourcesResolverInterface {

	/** @var GridProviderInterface */
	private $gridProvider;

	/** @var ResourceGridViewFactoryInterface */
	private $gridViewFactory;

	public function __construct(
		$gridProvider,
		$gridViewFactory
	) {
		$this->gridProvider = $gridProvider;
		$this->gridViewFactory = $gridViewFactory;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResources(RequestConfiguration $requestConfiguration, RepositoryInterface $repository) {

		if (!$requestConfiguration->hasGrid()) {

			$criteria = $requestConfiguration->getCriteria();

			$orderBy = $requestConfiguration->getOrderBy();

			if ($requestConfiguration->isPaginated()) {
				return $repository->createPaginator($criteria, $orderBy);
			}

			return $repository->findBy($criteria, $orderBy, $requestConfiguration->getLimit());
		}

		$gridDefinition = $this->gridProvider->get($requestConfiguration->getGrid());

		$request = $requestConfiguration->getRequest();

		$gridView = $this->gridViewFactory->create($gridDefinition, new Parameters($request->getQueryParams()), $requestConfiguration->getMetadata(), $requestConfiguration);

		if ($requestConfiguration->isHtmlRequest()) {
			return $gridView;
		}

		return $gridView->getData();

	}
}
