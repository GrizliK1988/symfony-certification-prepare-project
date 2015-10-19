<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 18.10.15
 * Time: 20:01
 */

namespace DG\SymfonyCert\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AuthenticationProviderManagerCompilerPass implements CompilerPassInterface
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
        $definition = $container->findDefinition('security.authentication_provider_manager');
        $providerDefinitionIds = $container->findTaggedServiceIds('security.authentication_provider');

        $definitions = [];
        foreach ($providerDefinitionIds as $serviceId => $tags) {
            $definitions[] = $container->findDefinition($serviceId);
        }

        $definition->replaceArgument(0, $definitions);
    }
}