<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 25.09.15
 * Time: 23:05
 */

namespace DG\SymfonyCert\DependencyInjection;


use DG\SymfonyCert\DependencyInjection\Compiler\AddEventDispatcherCompilerPass;
use DG\SymfonyCert\DependencyInjection\Compiler\RegisterSerializersCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

class SymfonyCert
{
    public function createContainerFromYamlConfig($config)
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addCompilerPass(new RegisterSerializersCompilerPass());
        $containerBuilder->addCompilerPass(new AddEventDispatcherCompilerPass());
        $containerBuilder->addCompilerPass(new RegisterListenersPass());
        $containerBuilder->registerExtension(new SymfonyCertDIExtension());

        $xmlLoader = new XmlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../../app/config'));
        $xmlLoader->load('config.xml');

        $containerBuilder->loadFromExtension('symfony_cert', $config);

        return $containerBuilder;
    }

    public function createContainerFromXmlConfig(array $config)
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->setParameter('api.endpoint', $config['api']);
        $containerBuilder->setParameter('api.key', $config['key']);

        $loader = new XmlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        return $containerBuilder;
    }

    public function createContainerFromPhpConfig(array $config)
    {
        $container = new ContainerBuilder();

        $container->setParameter('api.endpoint', $config['api']);
        $container->setParameter('api.key', $config['key']);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        return $container;
    }
}