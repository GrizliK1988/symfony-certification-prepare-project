<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 18.10.15
 * Time: 19:32
 */

namespace DG\SymfonyCert\DependencyInjection\Compiler;


use DG\SymfonyCert\Service\Security\UserProvider\ApiUserProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApiUsersCompilerPass implements CompilerPassInterface
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
        $firewallsSettings = $container->getParameter('firewalls');
        $apiUsers = $firewallsSettings['api_auth_by_key']['keys'];

        $apiUsersProviderDefinition = $container->findDefinition(ApiUserProvider::DIC_NAME);
        foreach ($apiUsers as $user) {
            $apiUsersProviderDefinition->addMethodCall('addUser', [$user]);
        }
    }
}