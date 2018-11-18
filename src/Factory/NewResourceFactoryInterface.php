<?php

declare (strict_types = 1);

namespace ResourceBundle\Factory;

use ResourceBundle\Request\RequestConfiguration;
use Towersystems\Resource\Factory\FactoryInterface;
use Towersystems\Resource\Model\ResourceInterface;

interface NewResourceFactoryInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param FactoryInterface $factory
     *
     * @return ResourceInterface
     */
    public function create(RequestConfiguration $requestConfiguration, FactoryInterface $factory): ResourceInterface;
}
