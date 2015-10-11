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
    require __DIR__ . '/../app/init.php';

    Debug::enable();
    $container = \DG\App\loadContainer();
    $translator = \DG\App\loadTranslator($container);

    $request = Request::createFromGlobals();
    $request->setSession(\DG\App\initSession());
    \DG\App\initTwig($container, $translator);

    preg_match('/\/(?P<controller>.+)\/(?P<action>.+)/', $request->getPathInfo(), $routingData);

    $controllerClass = sprintf("\\DG\\SymfonyCert\\Controller\\%sController", ucfirst($routingData['controller']));
    $controller = new $controllerClass($container);
    /** @var Response $response */
    $response = $controller->{$routingData['action'] . 'Action'}($request);
    $response->send();
}
