<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 23.10.15
 * Time: 9:10
 */

namespace DG\SymfonyCert\Service\Security\Authentication\Provider;


use DG\SymfonyCert\Service\Security\UserProvider\InMemoryUserProviderFactory;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\User\UserChecker;

class DaoAuthenticationProviderFactory
{
    public function create()
    {
        $userProvider = InMemoryUserProviderFactory::create();

        $userChecker = new UserChecker();

        $passwordEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);

        $encoders = [
            'Symfony\\Component\\Security\\Core\\User\\User' => $passwordEncoder,
        ];
        $passwordEncoderFactory = new EncoderFactory($encoders);

        return new DaoAuthenticationProvider($userProvider, $userChecker, 'dao_auth_provider', $passwordEncoderFactory);
    }
} 