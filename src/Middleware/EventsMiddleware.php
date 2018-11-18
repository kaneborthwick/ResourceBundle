<?php

namespace ResourceBundle\Middleware;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 *
 */
class EventsMiddleware implements MiddlewareInterface {

	/**
	 * [$container description]
	 * @var [type]
	 */
	private $eventsManager;

	/**
	 * [$container description]
	 * @var [type]
	 */
	private $container;

	/**
	 * [__construct description]
	 * @param ContainerInterface $container [description]
	 */
	public function __construct(
		$container
	) {
		$this->eventsManager = $container->get("tower.event_manager");
		$this->container = $container;
	}

	/**
	 * [process description]
	 * @param  ServerRequestInterface $request  [description]
	 * @param  DelegateInterface      $delegate [description]
	 * @return [type]                           [description]
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
		$config = $this->container->get("config");
		$events = $config['towersystems_events'];
		foreach ($events as $event => $a) {

			if (is_array($a)) {
				foreach ($a as $b) {

					if (!$this->container->has($b)) {
						$service = new $b();
					} else {
						$service = $this->container->get($b);
					}

					$this->eventsManager->attach($event, function ($b) use ($service) {
						$service($b);
					});
				}
			} else {
				if (!$this->container->has($b)) {
					$service = new $b();
				} else {
					$service = $this->container->get($b);
				}

				$this->eventsManager->attach($event, function ($e) use ($service) {
					$service($e);
				});
			}

		}
		return $handler->handle($request);
	}
}
