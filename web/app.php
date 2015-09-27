<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 22.09.15
 * Time: 22:41
 */

namespace {
    use DG\SymfonyCert\DependencyInjection\SymfonyCert;
    use DG\SymfonyCert\Service\Logger\ArrayLogger;
    use DG\SymfonyCert\Service\EdmundsApi\MakesService;
    use DG\SymfonyCert\Service\ServiceCallsStatistics;
    use Symfony\Component\Config\ConfigCache;
    use Symfony\Component\Debug\Debug;
    use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

    require_once __DIR__ . '/../app/autoload.php';
    require __DIR__ . '/../app/loadConfig.php';

    const ROOT_PATH = __DIR__ . '/../';
    const CACHE_PATH = ROOT_PATH  . 'app/cache/';
    const CONFIG_PATH = ROOT_PATH  . 'app/config/';
    const SRC_PATH = ROOT_PATH  . 'src/';

    Debug::enable();

    $cacheFile = CACHE_PATH . 'appContainerCache.php';
    $containerCache = new ConfigCache($cacheFile, true);

    if (!$containerCache->isFresh()) {
        $di = new SymfonyCert();
        $containerBuilder = $di->createContainerFromYamlConfig(DG\App\loadConfig());
        $containerBuilder->compile();

        $dumper = new PhpDumper($containerBuilder);
        $containerCache->write($dumper->dump([
            'class' => 'AppServiceContainer'
        ]), $containerBuilder->getResources());
    }

    require_once $containerCache->getPath();
    $container = new AppServiceContainer();

    $stat = new ServiceCallsStatistics();
    $stat->initCalls();
    $container->set('service.stat.calls', $stat);

    /** @var MakesService $makesService */
    $makesService = $container->get('api.makes');
    $makes = $makesService->getMakes('used', 2015);

    /** @var ArrayLogger $arrayLoggerService */
    $arrayLoggerService = $container->get('array_logger');

    print '<pre>';

    print_r($arrayLoggerService->getLogs());
    print_r(ServiceCallsStatistics::getCalls());
    print_r($makes);

    print '</pre>';
}
