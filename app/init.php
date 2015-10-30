<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 11.10.15
 * Time: 16:58
 */

namespace DG\App;

use DG\SymfonyCert\Controller\CustomControllerResolver;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Loader\ClosureLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Translation\TranslatorInterface;

function initSession()
{
    $storage = new NativeSessionStorage([
        'cookie_lifetime' => 3600,
        'gc_probability' => 1,
        'gc_divisor' => 1,
        'gc_maxlifetime' => 10000,
//        'cache_limiter' => session_cache_limiter()
    ], new NativeFileSessionHandler());
    $session = new Session($storage, new NamespacedAttributeBag());
    $session->start();

    return $session;
}

function initTwig(ContainerInterface $container, TranslatorInterface $translator)
{
    $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
    $twigLoader = new \Twig_Loader_Filesystem([
        VIEWS_PATH,
        dirname($appVariableReflection->getFileName()) . '/Resources/views/Form'
    ]);
    $twig = new \Twig_Environment($twigLoader);
    $formEngine = new TwigRendererEngine(['bootstrap_3_horizontal_layout.html.twig']);
    $formEngine->setEnvironment($twig);
    $twig->addExtension(new FormExtension(new TwigRenderer($formEngine, $container->get('csrf_token.manager'))));

    $twig->addExtension(new TranslationExtension($translator));
    $container->set('twig', $twig);

    return $twig;
}

function initKernel(ContainerInterface $container)
{
    /** @var EventDispatcherInterface $eventDispatcher */
    $eventDispatcher = $container->get('event_dispatcher');
    $kernel = new HttpKernel($eventDispatcher, new CustomControllerResolver($container, initRouting($container)));

    $stat = [];
    $eventDispatcher->addListener(KernelEvents::REQUEST, function (GetResponseEvent $event) use (&$stat) {
        $stat[] = sprintf("Request came in %s", $event->getRequest()->getPathInfo());
    });
    $eventDispatcher->addListener(KernelEvents::CONTROLLER, function (FilterControllerEvent $event) use (&$stat) {
        $stat[] = sprintf("Controller %s::%s selected", get_class($event->getController()[0]), $event->getController()[1]);
    });
    $eventDispatcher->addListener(KernelEvents::EXCEPTION, function (GetResponseForExceptionEvent $event) use (&$stat) {
        $stat[] = sprintf("Exception thrown");
    });
    $eventDispatcher->addListener(KernelEvents::RESPONSE, function (FilterResponseEvent $event) use (&$stat) {
        $content = $event->getResponse()->getContent();
        $newContent = str_replace('</body>', '<pre>'.print_r($stat, 1).'</pre></body>', $content);
        $event->getResponse()->setContent($newContent);
    });
    $eventDispatcher->addListener(KernelEvents::TERMINATE, function (PostResponseEvent $event) use (&$stat) {
        //some heavy logic
    });

    return $kernel;
}

/**
 * @param ContainerInterface $container
 * @return UrlMatcher
 */
function initRouting(ContainerInterface $container)
{
    $routeCollection = new RouteCollection();

    $viewRoute = new Route('/view', [
        '_controller' => 'Crud',
        '_action' => 'view'
    ]);
    $routeCollection->add('crud_view', $viewRoute);

    $addRoute = new Route('/add', [
        '_controller' => 'Crud',
        '_action' => 'add'
    ]);
    $routeCollection->add('crud_add', $addRoute);

    $editRoute = new Route('/edit/{id}', [
        '_controller' => 'Crud',
        '_action' => 'edit'
    ], [
        'id' => '.+'
    ]);
    $routeCollection->add('crud_edit', $editRoute);
    $routeCollection->addPrefix('/{_locale}/crud');
    $routeCollection->addDefaults(['_locale' => 'ru']);
    $routeCollection->addRequirements(['_locale' => 'ru|en']);

    $locator = new FileLocator([SRC_PATH . 'Resources/config']);
    $loader = new YamlFileLoader($locator);
    $collection = $loader->load('routes.yml');
    $routeCollection->addCollection($collection);

    $closureLoader = new ClosureLoader();
    $collection = $closureLoader->load(function () {
        return new RouteCollection();
    });

    $requestContext = new RequestContext();
    $requestContext->fromRequest(Request::createFromGlobals());
    $matcher = new UrlMatcher($routeCollection, $requestContext);
    $container->set('url_matcher', $matcher);

    $urlGenerator = new UrlGenerator($routeCollection, $requestContext);
    $container->set('url_generator', $urlGenerator);

    return $matcher;
}