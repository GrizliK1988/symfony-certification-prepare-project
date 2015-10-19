<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 18.10.15
 * Time: 20:18
 */

namespace DG\SymfonyCert\Service\Security\Firewall;


use DG\SymfonyCert\Service\Security\Authentication\Token\ApiUserToken;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class ApiAuthenticationListener implements ListenerInterface
{
    const AUTH_HEADER = 'X-Authentication-Key';

    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(AuthenticationManagerInterface $authenticationManager, TokenStorageInterface $tokenStorage)
    {
        $this->authenticationManager = $authenticationManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->headers->has(static::AUTH_HEADER)) {
            $unauthenticatedToken = new ApiUserToken();
            $unauthenticatedToken->setAttribute('key', $request->headers->get(static::AUTH_HEADER));

            $authenticatedToken = $this->authenticationManager->authenticate($unauthenticatedToken);
            $this->tokenStorage->setToken($authenticatedToken);
        }
    }
}