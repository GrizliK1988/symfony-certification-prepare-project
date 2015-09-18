<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.09.15
 * Time: 22:32
 */

namespace {
    use DG\SymfonyCert\AppConfiguration;
    use DG\SymfonyCert\Command\ModelsCacheCommand;
    use DG\SymfonyCert\Service\ConfigBag;
    use DG\SymfonyCert\Service\FileLoader\YamlConfigLoader;
    use Symfony\Component\Config\ConfigCache;
    use Symfony\Component\Config\Definition\Processor;
    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\Config\Loader\DelegatingLoader;
    use Symfony\Component\Config\Loader\LoaderResolver;
    use Symfony\Component\Config\Resource\FileResource;
    use Symfony\Component\Console\Application;

    require __DIR__ . '/app/autoload.php';

    $configCachePath = __DIR__ . '/app/cache/appConfigCache.php';
    $configCache = new ConfigCache($configCachePath, true);
    $configBag = new ConfigBag(['configs' => []]);

    if (!$configCache->isFresh()) {

        $locator = new FileLocator([__DIR__ . '/app/config']);
        $yamlLoader = new YamlConfigLoader($configBag, $locator);

        $loaderResolver = new LoaderResolver([$yamlLoader]);
        $delegatingLoader = new DelegatingLoader($loaderResolver);
        $delegatingLoader->load('config.yml');
        $delegatingLoader->load('config_extra.yml');

        $resources = [
            new FileResource($locator->locate('config.yml', null, true)),
        ];

        $processor = new Processor();
        $configuration = new AppConfiguration();
        $processedConfig = $processor->processConfiguration($configuration, $configBag->get('configs'));

        $configCache->write(json_encode($processedConfig), $resources);
    } else {
        $path = $configCache->getPath();
        $processedConfig = json_decode(file_get_contents($path), true);
    }

    $app = new Application();
    $app->add(new ModelsCacheCommand($processedConfig));
    $app->run();
}
