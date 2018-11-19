<?php

declare (strict_types = 1);

namespace ResourceBundle\Provider;

use ResourceBundle\Request\RequestConfiguration;
use Towersystems\Resource\Model\ResourceInterface;
use Towersystems\Resource\Repository\RepositoryInterface;

class SingleResourceProvider implements SingleResourceProviderInterface {

	/**
	 * {@inheritdoc}
	 */
	public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository):  ? ResourceInterface{
		$request = $requestConfiguration->getRequest();
        $criteria = $requestConfiguration->getCriteria();
		return $repository->findOneBy($criteria);
	}
}
