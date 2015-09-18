<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.09.15
 * Time: 22:32
 */

namespace {
    use DG\SymfonyCert\Command\ModelsCacheCommand;
    use DG\SymfonyCert\Service\ConfigBag;
    use DG\SymfonyCert\Service\FileLoader\YamlConfigLoader;
    use DG\SymfonyCert\Service\FileLoader\XmlConfigLoader;
    use Symfony\Component\Config\ConfigCache;
    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\Config\Loader\DelegatingLoader;
    use Symfony\Component\Config\Loader\LoaderResolver;
    use Symfony\Component\Config\Resource\FileResource;
    use Symfony\Component\Console\Application;

    require __DIR__ . '/app/autoload.php';

    $configCachePath = __DIR__ . '/app/cache/appConfigCache.php';
    $configCache = new ConfigCache($configCachePath, true);
    $configuration = new ConfigBag();

    if (!$configCache->isFresh()) {

        $locator = new FileLocator([__DIR__ . '/app/config']);
        $yamlLoader = new YamlConfigLoader($configuration, $locator);
        $xmlLoader = new XmlConfigLoader($configuration, $locator);

        $loaderResolver = new LoaderResolver([$yamlLoader, $xmlLoader]);

        $delegatingLoader = new DelegatingLoader($loaderResolver);
        $delegatingLoader->load('config.yml');
        $delegatingLoader->load('config.xml');

        $resources = [
            new FileResource($locator->locate('config.yml', null, true)),
            new FileResource($locator->locate('config.xml', null, true)),
        ];
        $code = $configuration->all();

        $configCache->write(json_encode($code), $resources);
    } else {
        $path = $configCache->getPath();
        $config = json_decode(file_get_contents($path), true);
        $configuration->reset($config);
    }

    $app = new Application();
    $app->add(new ModelsCacheCommand());
    $app->run();
}
