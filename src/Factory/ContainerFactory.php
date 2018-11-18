<?php

namespace ResourceBundle\Factory;

use Interop\Container\ContainerInterface;

/**
 *
 */
class ContainerFactory {

	/**
	 * [__invoke description]
	 * @param  ContainerInterface $container     [description]
	 * @param  [type]             $requestedName [description]
	 * @return [type]                            [description]
	 */
	function __invoke(ContainerInterface $container, $requestedName) {
		return new $requestedName($container);
	}
}
