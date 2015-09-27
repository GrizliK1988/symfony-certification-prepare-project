<?php

use Symfony\Component\DependencyInjection\Reference;

if (!$container->hasParameter('api.endpoint') || !$container->hasParameter('api.key')) {
    throw new \Exception('Required parameter not found');
}

$apiMakesDef = new \Symfony\Component\DependencyInjection\Definition();
$apiMakesDef
    ->setClass('DG\SymfonyCert\Service\EdmundsApi\MakesService')
    ->addArgument('%api.endpoint%')
    ->addArgument('%api.key%')
    ->addArgument(new Reference('delegating_serializer'))
    ->addMethodCall('setLogger', [new Reference('array_logger')])
    ->addMethodCall('setStatService', [new Reference('service.stat.calls')])
;
$container->setDefinition('api.makes', $apiMakesDef);

$container->register('delegating_serializer', 'DG\SymfonyCert\Service\Serializer\DelegatingSerializer');
$container->register('array_logger', 'DG\SymfonyCert\Service\Logger\ArrayLogger');
