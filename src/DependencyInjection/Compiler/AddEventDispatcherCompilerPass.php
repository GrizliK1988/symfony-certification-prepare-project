<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 30.09.15
 * Time: 22:19
 */

namespace DG\SymfonyCert\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AddEventDispatcherCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $container->setDefinition('event_dispatcher',
            new Definition('Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher', [new Reference('service_container')])
        );
    }
}