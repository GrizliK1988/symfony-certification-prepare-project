<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 23.10.15
 * Time: 8:28
 */

namespace DG\SymfonyCert\Service\Security\Authentication\Provider;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FormAuthenticator implements SimpleFormAuthenticatorInterface
{

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        //do nothing because it's only used in simple authentication provider
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        //do nothing because it's only used in simple authentication provider
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }
}