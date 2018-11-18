<?php

namespace ResourceBundle\Resolver;

use ResourceBundle\Request\RequestConfiguration;
use Towersystems\Resource\Repository\RepositoryInterface;

interface ResourcesResolverInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function getResources(RequestConfiguration $requestConfiguration, RepositoryInterface $repository);
}
