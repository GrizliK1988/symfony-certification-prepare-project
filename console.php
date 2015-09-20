<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.09.15
 * Time: 22:32
 */

namespace {
    use DG\SymfonyCert\Command\ModelsCacheCommand;
    use DG\SymfonyCert\Service\EdmundsApi\MakesService;
    use Symfony\Component\Console\Application;

    require __DIR__ . '/app/autoload.php';
    require __DIR__ . '/app/loadConfig.php';

    const ROOT_PATH = __DIR__ . '/';
    const CACHE_PATH = ROOT_PATH  . 'app/cache/';
    const CONFIG_PATH = ROOT_PATH  . 'app/config/';

    $config = DG\App\loadConfig();

    $app = new Application();
    $app->add(new ModelsCacheCommand(new MakesService($config['api'], $config['key'])));
    $app->run();
}
