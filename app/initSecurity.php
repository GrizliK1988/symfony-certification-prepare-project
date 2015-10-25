<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 18.10.15
 * Time: 9:21
 */
namespace DG\App;

use DG\SymfonyCert\Service\Security\Authentication\Provider\FormAuthenticator;
use DG\SymfonyCert\Service\Security\UserProvider\InMemoryUserProviderFactory;
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
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Http\AccessMap;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\EntryPoint\FormAuthenticationEntryPoint;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;
use Symfony\Component\Security\Http\FirewallMap;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\CookieClearingLogoutHandler;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
use Symfony\Component\Security\Http\RememberMe\ResponseListener;
use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategy;

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

    $voters = [];
    $roleHierarchy = new RoleHierarchy([
        'ROLE_ADMIN' => ['ROLE_USER']
    ]);
    $voters[] = new RoleHierarchyVoter($roleHierarchy);

    $accessDecisionManager = new AccessDecisionManager($voters, AccessDecisionManager::STRATEGY_UNANIMOUS);

    $accessMap = new AccessMap();
    $accessMap->add(new RequestMatcher('^/api/admin'), ['ROLE_ADMIN']);
    $accessMap->add(new RequestMatcher('^/api'), ['ROLE_USER']);
    $accessMap->add(new RequestMatcher('^/crud'), ['ROLE_USER']);
    $accessListener = new Firewall\AccessListener($tokenStorage, $accessDecisionManager, $accessMap, $authManager);

    $map = new FirewallMap();
    $exceptionListener = new ExceptionListener($tokenStorage, $trustResolver, $httpUtils, 'exception_listener');
    $map->add(new RequestMatcher('^/api'), [
        new Firewall\AnonymousAuthenticationListener($tokenStorage, 'anonymous_listener'),
        $container->get('security.api_authentication_listener'),
        $accessListener
    ], $exceptionListener);

    $authEntryPoint = new FormAuthenticationEntryPoint($kernel, $httpUtils, '/login');
    $exceptionListener = new ExceptionListener($tokenStorage, $trustResolver, $httpUtils, 'exception_listener', $authEntryPoint);

    $logoutListener = new Firewall\LogoutListener($tokenStorage, $httpUtils, new DefaultLogoutSuccessHandler($httpUtils, '/login'));
    $logoutListener->addHandler(new CookieClearingLogoutHandler(['remember_crud' => [
        'path' => '/',
        'domain' => ''
    ]]));

    $map->add(new RequestMatcher('^/(login|logout)'), [
        new Firewall\AnonymousAuthenticationListener($tokenStorage, 'anonymous_listener'),
        getSimpleAuthFormListener($container, $kernel),
        $logoutListener,
        $accessListener,
    ], $exceptionListener);

    $rememberMeListener = new Firewall\RememberMeListener($tokenStorage, getRememberMeServices(), $authManager, null, $evenDispatcher);

    $map->add(new RequestMatcher('^/crud'), [
        $rememberMeListener,
        $accessListener
    ], $exceptionListener);

    $firewall = new Firewall($map, $evenDispatcher);
    $evenDispatcher->addListener(KernelEvents::REQUEST, [$firewall, 'onKernelRequest']);
    $evenDispatcher->addSubscriber(new ResponseListener());
}

function getSimpleAuthFormListener(ContainerInterface $container, HttpKernel $kernel)
{
    /** @var TokenStorage $tokenStorage */
    /** @var UrlGenerator $urlGenerator */
    /** @var UrlMatcher $urlMatcher */
    /** @var EventDispatcherInterface $evenDispatcher */
    /** @var AuthenticationProviderManager $authManager */
    /** @var CsrfTokenManager $csrfTokenManager */
    $tokenStorage = $container->get('token_storage');
    $urlGenerator = $container->get('url_generator');
    $urlMatcher = $container->get('url_matcher');
    $evenDispatcher = $container->get('event_dispatcher');
    $authManager = $container->get('security.authentication_provider_manager');
    $csrfTokenManager = $container->get('csrf_token.manager');
    $httpUtils = new HttpUtils($urlGenerator, $urlMatcher);

    $successHandler = new DefaultAuthenticationSuccessHandler($httpUtils, [
        'default_target_path' => '/crud/view'
    ]);
    $failureHandler = new DefaultAuthenticationFailureHandler($kernel, $httpUtils);

    $simpleFormAuthListener = new Firewall\SimpleFormAuthenticationListener(
        $tokenStorage,
        $authManager,
        new SessionAuthenticationStrategy(SessionAuthenticationStrategy::MIGRATE),
        $httpUtils,
        'dao_auth_provider',
        $successHandler,
        $failureHandler, [
            'csrf_parameter' => 'form[_token]',
            'username_parameter' => 'form[_username]',
            'password_parameter' => 'form[_password]'
        ], null, $evenDispatcher, $csrfTokenManager, new FormAuthenticator()
    );

    $simpleFormAuthListener->setRememberMeServices(getRememberMeServices());

    return $simpleFormAuthListener;
}

function getRememberMeServices()
{
    $rememberMe = new TokenBasedRememberMeServices(
        [InMemoryUserProviderFactory::create()],
        'remember_me_crud',
        'remember_me_auth_provider',
        [
            'name' => 'remember_crud',
            'path' => '/',
            'domain' => '',
            'always_remember_me' => true,
            'lifetime' => 100000,
            'secure' => false,
            'httponly' => true
        ]
    );
    return $rememberMe;
}