<?php

declare (strict_types = 1);

namespace ResourceBundle\Provider;

use ResourceBundle\Request\RequestConfiguration;
use Towersystems\Resource\Model\ResourceInterface;
use Towersystems\Resource\Repository\RepositoryInterface;

interface SingleResourceProviderInterface {
	/**
	 * @param RequestConfiguration $requestConfiguration
	 * @param RepositoryInterface $repository
	 *
	 * @return ResourceInterface|null
	 */
	public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository):  ? ResourceInterface;
}
