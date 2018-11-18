<?php

namespace ResourceBundle\EventListener;

interface EventListenerInterface {

	/**
	 * [__invoke description]
	 *
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function __invoke($data): void;

}