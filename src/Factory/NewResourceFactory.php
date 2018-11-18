<?php

declare (strict_types = 1);

namespace ResourceBundle\Factory;

use ResourceBundle\Request\RequestConfiguration;
use Towersystems\Resource\Factory\FactoryInterface;
use Towersystems\Resource\Model\ResourceInterface;

class NewResourceFactory implements NewResourceFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(RequestConfiguration $requestConfiguration, FactoryInterface $factory): ResourceInterface
    {
        // possible to pass options via request configuration
        return $factory->createNew();
    }
}
