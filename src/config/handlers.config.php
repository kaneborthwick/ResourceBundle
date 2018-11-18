<?php

namespace ResourceBundle;

return [
    'dependencies' => [
        'factories' => [
            Handler\ResourceHandler::class => Handler\HandlerFactory::class,
        ],
    ],
];
