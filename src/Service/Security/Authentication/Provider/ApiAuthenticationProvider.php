<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 18.10.15
 * Time: 20:27
 */

namespace DG\SymfonyCert\Service\Security\Authentication\Provider;


use DG\SymfonyCert\Entity\ApiUser;
use DG\SymfonyCert\Service\Security\Authentication\Token\ApiUserToken;
use DG\SymfonyCert\Service\Security\UserProvider\ApiUserProvider;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class ApiAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var ApiUserProvider
     */
    private $apiUserProvider;

    public function __construct(ApiUserProvider $apiUserProvider)
    {
        $this->apiUserProvider = $apiUserProvider;
    }

    /**
     * Attempts to authenticate a TokenInterface object.
     *
     * @param TokenInterface $token The TokenInterface instance to authenticate
     *
     * @return TokenInterface An authenticated TokenInterface instance, never null
     *
     * @throws AuthenticationException if the authentication fails
     */
    public function authenticate(TokenInterface $token)
    {
        try {
            $key = $token->getAttribute('key');
            /** @var ApiUser $user */
            $user = $this->apiUserProvider->loadUserByKey($key);

            $authenticatedToken = new ApiUserToken($user->getRoles());
            $authenticatedToken->setUser($user);
            $authenticatedToken->setAuthenticated(true);

            return $authenticatedToken;
        } catch (BadCredentialsException $notFoundException) {
            throw new AuthenticationException('User not found');
        }
    }

    /**
     * Checks whether this provider supports the given token.
     *
     * @param TokenInterface $token A TokenInterface instance
     *
     * @return bool true if the implementation supports the Token, false otherwise
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof ApiUserToken;
    }
}