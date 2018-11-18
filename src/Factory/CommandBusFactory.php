<?php

namespace ResourceBundle\Factory;

use Interop\Container\ContainerInterface;
use League\Tactician\CommandBus;
use League\Tactician\Container\ContainerLocator;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\InvokeInflector;

class CommandBusFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $commandsMapping = $container->get('config')["command_bus_mappings"] ?? [];
        $inflector = new InvokeInflector();
        $locator = new ContainerLocator($container, $commandsMapping);

        $nameExtractor = new ClassNameExtractor();

        $commandHandlerMiddleware = new CommandHandlerMiddleware(
            $nameExtractor,
            $locator,
            $inflector
        );

        $commandBus = new CommandBus([
            $commandHandlerMiddleware,
        ]);

        return $commandBus;
    }
}
