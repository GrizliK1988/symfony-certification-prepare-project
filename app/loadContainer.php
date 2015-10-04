<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 30.09.15
 * Time: 19:46
 */

namespace DG\App;

use DG\SymfonyCert\DependencyInjection\SymfonyCert;
use DG\SymfonyCert\Service\ServiceCallsStatistics;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/loadConfig.php';

function loadContainer ()
{
    $config = loadConfig();

    $containerCachePath = CACHE_PATH . 'appContainerCache.php';
    $containerCache = new ConfigCache($containerCachePath, true);

    if (!$containerCache->isFresh()) {
        $di = new SymfonyCert();
        $container = $di->createContainerFromYamlConfig($config);
        $container->compile();

        $dumper = new PhpDumper($container);
        $dump = $dumper->dump([
            'class' => 'AppServiceContainer'
        ]);

        $containerCache->write($dump, $container->getResources());
    }

    require_once $containerCache->getPath();
    $container = new \AppServiceContainer();

    return $container;
}