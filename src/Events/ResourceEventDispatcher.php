<?php

namespace ResourceBundle\Events;

use ResourceBundle\Request\RequestConfiguration;
use Towersystems\Resource\Model\ResourceInterface;
use Zend\EventManager\EventManagerInterface;

/**
 *
 */
final class ResourceEventDispatcher
{

    /**
     * [$eventManager description]
     * @var [type]
     */
    private $eventManager;

    /**
     * [__construct description]
     * @param EventManagerInterface $eventManager [description]
     */
    function __construct(
        EventManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
    }

    /**
     * [dispatchPreEvent description]
     * @param  [type]               $eventName            [description]
     * @param  RequestConfiguration $requestConfiguration [description]
     * @param  ResourceInterface    $resource             [description]
     * @param  array                $fields               [description]
     * @return [type]                                     [description]
     */
    public function dispatchPreEvent(
        $eventName,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource,
        $fields = []
    ) {
        $metadata = $requestConfiguration->getMetadata();
        $this->eventManager->trigger(sprintf('%s.%s.pre_%s', $metadata->getApplicationName(), $metadata->getName(), $eventName), array_merge([
            'resource' => $resource,
        ], $fields));
    }

    /**
     * [dispatchPostEvent description]
     * @param  [type]               $eventName            [description]
     * @param  RequestConfiguration $requestConfiguration [description]
     * @param  ResourceInterface    $resource             [description]
     * @return [type]                                     [description]
     */
    public function dispatchPostEvent($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource, $fields = [])
    {
        $metadata = $requestConfiguration->getMetadata();
        $this->eventManager->trigger(sprintf('%s.%s.post_%s', $metadata->getApplicationName(), $metadata->getName(), $eventName), array_merge([
            'resource' => $resource,
        ], $fields));
    }
}
