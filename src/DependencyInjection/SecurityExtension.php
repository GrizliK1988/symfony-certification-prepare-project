<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 18.10.15
 * Time: 8:31
 */

namespace DG\SymfonyCert\DependencyInjection;


use DG\SymfonyCert\DependencyInjection\Compiler\ApiUsersCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SecurityExtension extends Extension
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
        $configuration = new SecurityConfiguration();
        $resultConfig =  $this->processConfiguration($configuration, $config);

        $container->setParameter('firewalls', $resultConfig['firewalls']);

        $yamlServiceLoader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/services'));
        $yamlServiceLoader->load('security.yml');
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
        return 'security';
    }
}