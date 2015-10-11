<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 22.09.15
 * Time: 22:41
 */

namespace {
    use DG\SymfonyCert\Controller\CustomControllerResolver;
    use Symfony\Component\Debug\Debug;
    use Symfony\Component\EventDispatcher\EventDispatcherInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
    use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
    use Symfony\Component\HttpKernel\Event\GetResponseEvent;
    use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
    use Symfony\Component\HttpKernel\Event\PostResponseEvent;
    use Symfony\Component\HttpKernel\HttpKernel;
    use Symfony\Component\HttpKernel\KernelEvents;

    require_once __DIR__ . '/../app/autoload.php';
    require __DIR__ . '/../app/constants.php';
    require __DIR__ . '/../app/loadConfig.php';
    require __DIR__ . '/../app/loadContainer.php';
    require __DIR__ . '/../app/loadTranslator.php';
    require __DIR__ . '/../app/init.php';

    Debug::enable();
    $container = \DG\App\loadContainer();
    $translator = \DG\App\loadTranslator($container);

    $request = Request::createFromGlobals();
    $request->setSession(\DG\App\initSession());
    \DG\App\initTwig($container, $translator);

    /** @var EventDispatcherInterface $eventDispatcher */
    $eventDispatcher = $container->get('event_dispatcher');
    $kernel = new HttpKernel($eventDispatcher, new CustomControllerResolver($container));

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

    $response = $kernel->handle($request);
    $response->send();

    $kernel->terminate($request, $response);
}
