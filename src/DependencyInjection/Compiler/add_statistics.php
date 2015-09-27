<?php

use Symfony\Component\DependencyInjection\Reference;

$statServiceDef = new \Symfony\Component\DependencyInjection\Definition();
$statServiceDef->setSynthetic(true);
$container->setDefinition('service.stat.calls', $statServiceDef);

$container->getDefinition('api.makes')->addMethodCall('setStatService', [new Reference('service.stat.calls')]);
