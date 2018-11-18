<?php

namespace ResourceBundle\Controller;

use Psr\Http\Server\MiddlewareInterface;
use Zend\Diactoros\Response\EmptyResponse;

abstract class Controller implements MiddlewareInterface {

	/** @var [type] [description] */
	protected $templates;

	/** @var [type] [description] */
	protected $options;

	/**
	 * [__construct description]
	 * @param [type] $templates [description]
	 */
	public function __construct(
		$templates
	) {
		$this->templates = $templates;
	}

	/**
	 * {@iheritdoc}
	 */
	public function process(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Server\RequestHandlerInterface $handler
	)
	: \Psr\Http\Message\ResponseInterface{

		$options = $request->getAttribute('Zend\Expressive\Router\RouteResult')->getMatchedRoute()->getOptions();
		$this->options = $options;
		$action = isset($options['action']) ? $options['action'] . 'Action' : '';

		if (!method_exists($this, $action)) {
			return new EmptyResponse(404);
		}

		try {
			return $this->$action($request, $handler);
		} catch (ResourceNotFoundException $e) {
			return new EmptyResponse(404);
		}

	}

	/**
	 * [getOption description]
	 *
	 * @param  [type] $option       [description]
	 * @param  [type] $defaultValue [description]
	 * @return [type]               [description]
	 */
	protected function getOption($option, $defaultValue = null) {
		return $this->options[$option] ?? $defaultValue;
	}

}