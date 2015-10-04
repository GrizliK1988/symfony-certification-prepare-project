<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 22.09.15
 * Time: 22:41
 */

namespace {
    use DG\SymfonyCert\DependencyInjection\SymfonyCert;
    use DG\SymfonyCert\Service\ServiceCallsStatistics;
    use Symfony\Component\Asset\Context\RequestStackContext;
    use Symfony\Component\Asset\PathPackage;
    use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;
    use Symfony\Component\Config\ConfigCache;
    use Symfony\Component\Debug\Debug;
    use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\Templating\Loader\FilesystemLoader;
    use Symfony\Component\Templating\PhpEngine;
    use Symfony\Component\Templating\TemplateNameParser;

    require_once __DIR__ . '/../app/autoload.php';
    require __DIR__ . '/../app/constants.php';
    require __DIR__ . '/../app/loadConfig.php';
    require __DIR__ . '/../app/loadContainer.php';

    Debug::enable();
    $container = \DG\App\loadContainer();

    $requestStack = new RequestStack();
    $requestStack->push(Request::createFromGlobals());
    $requestContext = new RequestStackContext($requestStack);

    $loader = new FilesystemLoader(SRC_PATH . 'Resources/views/%name%');
    $templating = new PhpEngine(new TemplateNameParser(), $loader);

    $assetJsLoader = new PathPackage('/js/', new StaticVersionStrategy('v2', '%1$s?%2$s'), $requestContext);
    $templating->addGlobal('JsAssets', $assetJsLoader);

    echo $templating->render('hello.php', ['name' => 'Dima']);
}
