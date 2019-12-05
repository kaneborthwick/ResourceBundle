<?php

namespace ResourceBundle;

use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [

	ConfigAbstractFactory::class => [
		Resolver\ResourcesResolver::class => [
			'tower.grid.grid_provider',
			'tower.grid.grid_view_factory',
		],
	],

	'dependencies' => [
		'aliases' => [
			'tower.resource.resource_resolver' => Resolver\ResourcesResolver::class,
		],
	],

];
