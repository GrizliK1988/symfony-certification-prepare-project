<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 27.09.15
 * Time: 11:41
 */

namespace DG\SymfonyCert\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterSerializersCompilerPass implements CompilerPassInterface
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
        $serializers = $container->findTaggedServiceIds('serializer');
        $delegatingSerializer = $container->findDefinition('delegating_serializer');
        foreach ($serializers as $serializerId => $tags) {
            foreach ($tags as $tag) {
                $delegatingSerializer->addMethodCall('addSerializer', [new Reference($serializerId), $tag['format']]);
            }
        }
    }

} 