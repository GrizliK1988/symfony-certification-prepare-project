<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 08.10.15
 * Time: 8:17
 */

namespace DG\SymfonyCert\Controller;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\User;

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

    /**
     * @return User
     */
    public function getUser()
    {
        /** @var TokenStorageInterface $tokenStorage */
        $tokenStorage = $this->container->get('token_storage');
        return $tokenStorage->getToken()->getUser();
    }
}