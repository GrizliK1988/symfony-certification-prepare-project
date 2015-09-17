<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.09.15
 * Time: 9:27
 */

namespace {
    require_once __DIR__ . '/vendor/autoload.php';

    use Dima\Legacy\TestClass as TestLoadedByMap;
    use Dima\Test as TestPsr0;
    use Symfony\Component\ClassLoader\ClassLoader;
    use Symfony\Component\ClassLoader\ClassMapGenerator;
    use Symfony\Component\ClassLoader\MapClassLoader;
    use Symfony\Component\ClassLoader\Psr4ClassLoader;
    use Symfony\Component\ClassLoader\XcacheClassLoader;
    use DG\SymfonyCert\Controller\HomeController;

    $psr0Loader = new ClassLoader();
    $psr0Loader->addPrefix('Dima', __DIR__ . '/srcPsr0');

    $cachedLoader = new XcacheClassLoader(sha1(__FILE__), $psr0Loader);
    $cachedLoader->register();

    $psr0Loader->unregister();

    $psr4Loader = new Psr4ClassLoader();
    $psr4Loader->addPrefix('DG\\SymfonyCert\\', __DIR__ . '/src');
    $psr4Loader->register();

    $mapLoader = new MapClassLoader([
        'Dima\\Legacy\\TestClass' => __DIR__ . '/srcPsr0/TestClass.php'
    ]);
    $mapLoader->register();

    ClassMapGenerator::dump(__DIR__ . '/srcThirdParty', __DIR__ . '/class_map.php');

    $classMap = include __DIR__ . '/class_map.php';
    $mapLoader2 = new MapClassLoader($classMap);
    $mapLoader2->register();

    //Psr0 loader + XCache
    TestPsr0::output("Hello from Psr0 loaded class!");

    //Map loader
    TestLoadedByMap::test();

    $homeController = new HomeController();
    echo $homeController->indexAction()->getContent(), "\n";
}