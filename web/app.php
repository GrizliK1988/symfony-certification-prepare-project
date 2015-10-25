<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 22.09.15
 * Time: 22:41
 */

namespace {
    use Symfony\Component\Debug\Debug;
    use Symfony\Component\HttpFoundation\Cookie;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Generator\UrlGenerator;
    use Symfony\Component\Security\Core\Exception\AccessDeniedException;
    use Symfony\Component\Security\Core\Exception\AuthenticationException;

    require_once __DIR__ . '/../app/autoload.php';
    require __DIR__ . '/../app/constants.php';
    require __DIR__ . '/../app/loadConfig.php';
    require __DIR__ . '/../app/loadContainer.php';
    require __DIR__ . '/../app/loadTranslator.php';
    require __DIR__ . '/../app/init.php';
    require __DIR__ . '/../app/initSecurity.php';

    Debug::enable();
    $container = \DG\App\loadContainer();
    $translator = \DG\App\loadTranslator($container);

    $request = Request::createFromGlobals();
    $request->setSession(\DG\App\initSession());
    \DG\App\initTwig($container, $translator);

    $kernel = \DG\App\initKernel($container);
    \DG\App\initSecurity($container, $kernel);

    $response = $kernel->handle($request);
    $response->send();

    $kernel->terminate($request, $response);
}
