<?php

namespace ResourceBundle\Provider;

use ResourceBundle\Request\RequestConfiguration;
use Towersystems\Resource\Repository\RepositoryInterface;

interface ResourcesCollectionProviderInterface
{

    /**
     * [get description]
     * @param  RequestConfiguration $requestConfiguration [description]
     * @param  RepositoryInterface  $repository           [description]
     * @return [type]                                     [description]
     */
    public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository);
}
