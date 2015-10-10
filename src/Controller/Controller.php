<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 08.10.15
 * Time: 8:17
 */

namespace DG\SymfonyCert\Controller;


use Symfony\Component\DependencyInjection\ContainerInterface;

class Controller
{
    /**
     * @var ContainerInterface
     */
    private $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function get($serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->get('twig');
    }
}