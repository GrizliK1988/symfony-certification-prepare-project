<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 18.10.15
 * Time: 9:21
 */
namespace DG\App;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Http\AccessMap;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;
use Symfony\Component\Security\Http\FirewallMap;
use Symfony\Component\Security\Http\HttpUtils;

function initSecurity(ContainerInterface $container, HttpKernel $kernel)
{
    /** @var TokenStorage $tokenStorage */
    /** @var UrlGenerator $urlGenerator */
    /** @var UrlMatcher $urlMatcher */
    /** @var EventDispatcherInterface $evenDispatcher */
    /** @var AuthenticationProviderManager $authManager */
    $tokenStorage = $container->get('token_storage');
    $urlGenerator = $container->get('url_generator');
    $urlMatcher = $container->get('url_matcher');
    $evenDispatcher = $container->get('event_dispatcher');
    $authManager = $container->get('security.authentication_provider_manager');

    $trustResolver = new AuthenticationTrustResolver(
        'Symfony\Component\Security\Core\Authentication\Token\AnonymousToken',
        'Symfony\Component\Security\Core\Authentication\Token\RememberMeToken'
    );

    $httpUtils = new HttpUtils($urlGenerator, $urlMatcher);

    $exceptionListener = new ExceptionListener($tokenStorage, $trustResolver, $httpUtils, 'exception_listener');

    $voters = [];
    $roleHierarchy = new RoleHierarchy([
        'ROLE_ADMIN' => ['ROLE_USER']
    ]);
    $voters[] = new RoleHierarchyVoter($roleHierarchy);

    $accessDecisionManager = new AccessDecisionManager($voters, AccessDecisionManager::STRATEGY_UNANIMOUS);

    $accessMap = new AccessMap();
    $accessMap->add(new RequestMatcher('^/api/admin'), ['ROLE_ADMIN']);
    $accessMap->add(new RequestMatcher('^/api'), ['ROLE_USER']);
    $accessListener = new Firewall\AccessListener($tokenStorage, $accessDecisionManager, $accessMap, $authManager);

    $map = new FirewallMap();
    $map->add(new RequestMatcher('^/api'), [
        new Firewall\AnonymousAuthenticationListener($tokenStorage, 'anonymous_listener'),
        $container->get('security.api_authentication_listener'),
        $accessListener
    ], $exceptionListener);

    $firewall = new Firewall($map, $evenDispatcher);
    $evenDispatcher->addListener(KernelEvents::REQUEST, [$firewall, 'onKernelRequest']);
}
