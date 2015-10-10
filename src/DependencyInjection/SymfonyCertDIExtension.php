<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 26.09.15
 * Time: 17:43
 */

namespace DG\SymfonyCert\DependencyInjection;


use DG\SymfonyCert\AppConfiguration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SymfonyCertDIExtension implements ExtensionInterface, PrependExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array $config An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = new AppConfiguration();
        $processor = new Processor();
        $resultConfig = $processor->processConfiguration($configuration, $config);

        $container->setParameter('api.endpoint', $resultConfig['api']);
        $container->setParameter('api.key', $resultConfig['key']);

        $container->setParameter('root_path', ROOT_PATH);
        $container->setParameter('images_path', IMAGES_PATH);
        $container->setParameter('storage_path', STORAGE_PATH);

        $yamlServiceLoader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $yamlServiceLoader->load('services.yml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig($this->getAlias(), ['secret' => 333]);
    }

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string The XML namespace
     *
     * @api
     */
    public function getNamespace()
    {
        return 'http://localhost/symfony/schema/';
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     *
     * @api
     */
    public function getXsdValidationBasePath()
    {
        return false;
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     *
     * @api
     */
    public function getAlias()
    {
        return 'symfony_cert';
    }
}