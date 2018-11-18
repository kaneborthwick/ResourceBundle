<?php

namespace ResourceBundle\Middleware;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DeleteableFilterEnablerMiddleware implements MiddlewareInterface
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $includeDeleted = $request->getAttribute('includeDeleted');

        if (!$includeDeleted) {
            $this->entityManager->getFilters()->enable('soft-deleteable');
        }

        return $handler->handle($request);
    }
}
