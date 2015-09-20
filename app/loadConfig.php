<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 20.09.15
 * Time: 12:28
 */

namespace DG\App {
    use DG\SymfonyCert\AppConfiguration;
    use DG\SymfonyCert\Service\ConfigBag;
    use DG\SymfonyCert\Service\FileLoader\YamlConfigLoader;
    use Symfony\Component\Config\ConfigCache;
    use Symfony\Component\Config\Definition\Processor;
    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\Config\Loader\DelegatingLoader;
    use Symfony\Component\Config\Loader\LoaderResolver;
    use Symfony\Component\Config\Resource\FileResource;

    /**
     * @return array
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    function loadConfig()
    {
        $configCachePath = __DIR__ . '/cache/appConfigCache.php';
        $configCache = new ConfigCache($configCachePath, true);
        $configBag = new ConfigBag(['configs' => []]);

        if (!$configCache->isFresh()) {

            $locator = new FileLocator([__DIR__ . '/config']);
            $yamlLoader = new YamlConfigLoader($configBag, $locator);

            $loaderResolver = new LoaderResolver([$yamlLoader]);
            $delegatingLoader = new DelegatingLoader($loaderResolver);
            $delegatingLoader->load('config.yml');
            $delegatingLoader->load('config_extra.yml');

            $resources = [
                new FileResource($locator->locate('config.yml', null, true)),
                new FileResource($locator->locate('config_extra.yml', null, true)),
            ];

            $processor = new Processor();
            $configuration = new AppConfiguration();
            $processedConfig = $processor->processConfiguration($configuration, $configBag->get('configs'));

            $configCache->write(json_encode($processedConfig), $resources);
        } else {
            $path = $configCache->getPath();
            $processedConfig = json_decode(file_get_contents($path), true);
        }

        return $processedConfig;
    }
}