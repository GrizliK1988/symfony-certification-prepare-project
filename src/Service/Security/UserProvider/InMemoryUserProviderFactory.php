<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 23.10.15
 * Time: 10:36
 */

namespace DG\SymfonyCert\Service\Security\UserProvider;


use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;

class InMemoryUserProviderFactory
{
    public static function create()
    {
        return new InMemoryUserProvider([
            'admin' => [
                'password' => 'zKgdNE7BHguhCKv+42U0WnRCbF8DgMJRQCi2aqzk3vMGfP0ZNIIes6SK+aE6cZtlVm4rEKfY4earvqcNGIMuSA==', //123456
                'roles' => [new Role('ROLE_ADMIN')]
            ],
        ]);
    }
} 