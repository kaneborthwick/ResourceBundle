<?php

namespace ResourceBundle\StateMachine;

use ResourceBundle\Request\RequestConfiguration;
use Towersystems\Resource\Model\ResourceInterface;

interface StateMachineInterface
{
    /**
     * @param RequestConfiguration $configuration
     * @param ResourceInterface $resource
     *
     * @return bool
     */
    public function can(RequestConfiguration $configuration, ResourceInterface $resource);

    /**
     * @param RequestConfiguration $configuration
     * @param ResourceInterface $resource
     */
    public function apply(RequestConfiguration $configuration, ResourceInterface $resource);
}
