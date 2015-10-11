<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 22.09.15
 * Time: 22:41
 */

namespace {
    use Symfony\Bridge\Twig\Extension\FormExtension;
    use Symfony\Bridge\Twig\Extension\TranslationExtension;
    use Symfony\Bridge\Twig\Form\TwigRenderer;
    use Symfony\Bridge\Twig\Form\TwigRendererEngine;
    use Symfony\Component\Asset\Context\RequestStackContext;
    use Symfony\Component\Debug\Debug;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
    use Symfony\Component\HttpFoundation\Session\Session;
    use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
    use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

    require_once __DIR__ . '/../app/autoload.php';
    require __DIR__ . '/../app/constants.php';
    require __DIR__ . '/../app/loadConfig.php';
    require __DIR__ . '/../app/loadContainer.php';
    require __DIR__ . '/../app/loadTranslator.php';

    Debug::enable();
    $container = \DG\App\loadContainer();
    $translator = \DG\App\loadTranslator($container);

    $storage = new NativeSessionStorage([
        'cookie_lifetime' => 3600,
        'gc_probability' => 1,
        'gc_divisor' => 1,
        'gc_maxlifetime' => 10000,
//        'cache_limiter' => session_cache_limiter()
    ], new NativeFileSessionHandler());
    $session = new Session($storage, new NamespacedAttributeBag());
    $session->start();

    $request = Request::createFromGlobals();
    $request->setSession($session);
    $requestStack = new RequestStack();
    $requestStack->push($request);
    $requestContext = new RequestStackContext($requestStack);

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

    preg_match('/\/(?P<controller>.+)\/(?P<action>.+)/', $request->getPathInfo(), $routingData);

    $controllerClass = sprintf("\\DG\\SymfonyCert\\Controller\\%sController", ucfirst($routingData['controller']));
    $controller = new $controllerClass($container);
    /** @var Response $response */
    $response = $controller->{$routingData['action'] . 'Action'}($request);
    $response->send();
}
