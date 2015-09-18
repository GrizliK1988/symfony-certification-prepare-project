<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.09.15
 * Time: 23:28
 */

namespace DG\SymfonyCert\Service\FileLoader;


use DG\SymfonyCert\Service\ConfigBag;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class YamlConfigLoader extends FileLoader
{
    private $configBag;

    public function __construct(ConfigBag $configBag, FileLocatorInterface $locator)
    {
        $this->configBag = $configBag;
        parent::__construct($locator); // TODO: Change the autogenerated stub
    }

    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string|null $type The resource type or null if unknown
     *
     * @throws \Exception If something went wrong
     */
    public function load($resource, $type = null)
    {
        $configFile = $this->locator->locate($resource, null, true);

        $configs = $this->configBag->get('configs');
        $configs[] = Yaml::parse(file_get_contents($configFile));
        $this->configBag->set('configs', $configs);
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && pathinfo($resource, PATHINFO_EXTENSION) === 'yml';
    }
}