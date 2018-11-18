<?php

namespace ResourceBundle\Factory;

use Interop\Container\ContainerInterface;
use SM\Factory\Factory;

class StateMachineFactory
{

    /**
     * [__invoke description]
     * @param  ContainerInterface $container [description]
     * @return [type]                        [description]
     */
    public function __invoke(ContainerInterface $container)
    {
        $callbackFactory = $container->get("tower.state_machine_callback_factory");
        $graphs = $container->get("config")["state_machine"]["graphs"] ?? [];
        return new Factory($graphs, null, $callbackFactory);
    }
}
