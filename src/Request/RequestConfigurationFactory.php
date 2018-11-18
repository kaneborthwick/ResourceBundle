<?php

namespace ResourceBundle\Request;

use Psr\Http\Message\ServerRequestInterface;
use Towersystems\Resource\Metadata\MetadataInterface;

class RequestConfigurationFactory
{

    /**
     * [create description]
     * @param  [type]                 $config  [description]
     * @param  ServerRequestInterface $request [description]
     * @return [type]                          [description]
     */
    public function create(MetadataInterface $metadata, ServerRequestInterface $request)
    {
        $paramaters = $this->parseApiParameters($request);
        return new RequestConfiguration($metadata, $request, $paramaters);
    }

    /**
     * @param  ServerRequestInterface $request [description]
     * @return [type]                          [description]
     */
    private function parseApiParameters(ServerRequestInterface $request)
    {

        $parameters = [];
        $attributes = $request->getAttributes();

        // not sure how to only get route attributes without adding more middleware
        // for now; remove the default router from attributes
        if (isset($attributes[\Zend\Expressive\Router\RouteResult::class])) {
            $route = $attributes[\Zend\Expressive\Router\RouteResult::class];
            $options = $route->getMatchedRoute()->getOptions();

            if (isset($options["tower"])) {
                $parameters = array_merge($options["tower"], $parameters);
            }

            $parameters['criteria'] = $route->getMatchedParams();
        }

        return array_merge_recursive($parameters, $request->getQueryParams());
    }
}
