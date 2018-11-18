<?php

namespace ResourceBundle\StateMachine;

use ResourceBundle\Request\RequestConfiguration;
use SM\Factory\FactoryInterface;
use Towersystems\Resource\Model\ResourceInterface;

class StateMachine implements StateMachineInterface
{

    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @param FactoryInterface $stateMachineFactory
     */
    public function __construct(FactoryInterface $stateMachineFactory)
    {
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /**
     * {inheritdoc}
     */
    public function can(RequestConfiguration $configuration, ResourceInterface $resource)
    {
        if (!$configuration->hasStateMachine()) {
            throw new \InvalidArgumentException('State machine must be configured to apply transition, check your routing.');
        }

        return $this->stateMachineFactory->get($resource, $configuration->getStateMachineGraph())->can($configuration->getStateMachineTransition());
    }

    /**
     * {inheritdoc}
     */
    public function apply(RequestConfiguration $configuration, ResourceInterface $resource)
    {

        if (!$configuration->hasStateMachine()) {
            throw new \InvalidArgumentException('State machine must be configured to apply transition, check your routing.');
        }

        $this->stateMachineFactory->get($resource, $configuration->getStateMachineGraph())->apply($configuration->getStateMachineTransition());
    }
}
