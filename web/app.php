<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 22.09.15
 * Time: 22:41
 */

namespace {
    use Symfony\Component\Debug\Debug;
    use Symfony\Component\HttpFoundation\Request;

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

    $kernel = \DG\App\initKernel($container);

    $response = $kernel->handle($request);
    $response->send();

    $kernel->terminate($request, $response);
}
