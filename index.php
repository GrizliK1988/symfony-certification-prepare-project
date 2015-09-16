<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 15.09.15
 * Time: 20:40
 */

namespace {
    require_once __DIR__ . '/vendor/autoload.php';

    use DG\SymfonyCert\Controller\HomeController;
    use DGHelper\TestHelper;
    use Symfony\Component\Asset\Context\RequestStackContext;
    use Symfony\Component\Asset\Packages;
    use Symfony\Component\Asset\PathPackage;
    use Symfony\Component\Asset\UrlPackage;
    use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
    use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;
    use Symfony\Component\ClassLoader\ClassLoader;
    use Symfony\Component\ClassLoader\Psr4ClassLoader;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\RequestStack;

    $requestStack = new RequestStack();
    $requestStack->push(Request::createFromGlobals());
    $requestStackContext = new RequestStackContext($requestStack);

    ////////////////////
    ////// Assets
    ////////////////////

    $jsAssetsPackage = new PathPackage('/asset/js/', new StaticVersionStrategy('v1', '%1$s?%2$s'), $requestStackContext);
    $cssAssetsPackage = new PathPackage('/asset/css/', new StaticVersionStrategy('v1', '%1$s?%2$s'), $requestStackContext);
    $imgAssetsPackage = new PathPackage('/asset/img/', new StaticVersionStrategy('v1', '%1$s?%2$s'), $requestStackContext);
    $jqueryPackage = new UrlPackage([
        'https://code.jquery.com/',
        'http://code.jquery.com/',
    ], new EmptyVersionStrategy(), $requestStackContext);

    $namedPackages = [
        'js' => $jsAssetsPackage,
        'jquery-cdn' => $jqueryPackage,
        'css' => $cssAssetsPackage,
        'img' => $imgAssetsPackage
    ];
    $packages = new Packages(null, $namedPackages);

    echo '<pre>';
    echo $packages->getUrl('react.js', 'js'), "\n";
    echo $packages->getUrl('jquery-2.1.4.min.js', 'jquery-cdn'), "\n";

    echo $packages->getUrl('style.css', 'css'), "\n";
    echo $packages->getUrl('image.png', 'img'), "\n\n\n";

    ////////////////////
    ////// Class loaders
    ////////////////////

    $loader = new ClassLoader();
    //Search in include path too
    $loader->setUseIncludePath(true);
    $loader->addPrefix('DGHelper', __DIR__ . '/helper');
    $loader->register();

    $psr4Loader = new Psr4ClassLoader();
    $psr4Loader->addPrefix('DG\\SymfonyCert\\', __DIR__ . '/src');
    $psr4Loader->register();

    TestHelper::help();

    $homeController = new HomeController();
    echo $homeController->indexAction()->getContent(), "\n";
}
