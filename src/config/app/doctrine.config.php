<?php

return [
	'doctrine' => [
		'event_manager' => [
			'orm_default' => [
				'subscribers' => [
					'Gedmo\Timestampable\TimestampableListener',
					'Gedmo\SoftDeleteable\SoftDeleteableListener',
					'ORMMappedSuperClassSubscriber',
					'Gedmo\Sortable\SortableListener',
				],
			],
		],
		'configuration' => [
			'orm_default' => [
				'filters' => [
					'soft-deleteable' => 'Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter',
				],
			],
		],
	],
];
