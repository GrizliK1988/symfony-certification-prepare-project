<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 11.10.15
 * Time: 17:11
 */

namespace DG\SymfonyCert\Controller;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class CustomControllerResolver implements ControllerResolverInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var UrlMatcher
     */
    private $urlMatcher;

    public function __construct(ContainerInterface $container, UrlMatcher $urlMatcher)
    {
        $this->container = $container;
        $this->urlMatcher = $urlMatcher;
    }

    /**
     * Returns the Controller instance associated with a Request.
     *
     * As several resolvers can exist for a single application, a resolver must
     * return false when it is not able to determine the controller.
     *
     * The resolver must only throw an exception when it should be able to load
     * controller but cannot because of some errors made by the developer.
     *
     * @param Request $request A Request instance
     *
     * @return callable|false A PHP callable representing the Controller,
     *                        or false if this resolver is not able to determine the controller
     *
     * @throws \LogicException If the controller can't be found
     *
     * @api
     */
    public function getController(Request $request)
    {
        $route = $this->urlMatcher->matchRequest($request);

        $controllerClass = sprintf("\\DG\\SymfonyCert\\Controller\\%sController", ucfirst($route['_controller']));
        $controller = new $controllerClass($this->container);

        $attributes = [];
        foreach ($route as $key => $value) {
            if (strpos($key, '_') !== 0) {
                $attributes[$key] = $value;
            } else {
                $request->attributes->set($key, $value);
            }

            if ($key === '_locale') {
                $request->getSession()->set('locale', $value);
            }
        }
        $request->attributes->set('data', $attributes);

        return [$controller, $route['_action'] . 'Action'];
    }

    /**
     * Returns the arguments to pass to the controller.
     *
     * @param Request $request A Request instance
     * @param callable $controller A PHP callable
     *
     * @return array An array of arguments to pass to the controller
     *
     * @throws \RuntimeException When value for argument given is not provided
     *
     * @api
     */
    public function getArguments(Request $request, $controller)
    {
        return [$request];
    }
}