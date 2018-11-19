<?php

namespace ResourceBundle\Resolver;

use ResourceBundle\Request\RequestConfiguration;
use Towersystems\Resource\Repository\RepositoryInterface;

class ResourcesResolver implements ResourcesResolverInterface {

	/**
	 * {@inheritdoc}
	 */
	public function getResources(RequestConfiguration $requestConfiguration, RepositoryInterface $repository) {

		$criteria = $requestConfiguration->getCriteria();
		$orderBy = $requestConfiguration->getOrderBy();

		if ($requestConfiguration->isPaginated()) {
			return $repository->createPaginator($criteria, $orderBy, null, $requestConfiguration->isComplex());
		}

		return $repository->findBy($criteria, $orderBy, $requestConfiguration->getLimit());
	}

}
