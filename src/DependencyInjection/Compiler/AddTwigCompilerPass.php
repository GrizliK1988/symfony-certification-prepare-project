<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 08.10.15
 * Time: 8:14
 */

namespace DG\SymfonyCert\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class AddTwigCompilerPass implements CompilerPassInterface
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
        $twigDefinition = new Definition('Twig_Environment');
        $twigDefinition->setSynthetic(true);
        $container->setDefinition('twig', $twigDefinition);
    }
}