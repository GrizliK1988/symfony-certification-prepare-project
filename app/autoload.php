<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.09.15
 * Time: 21:55
 */

namespace {
    use Symfony\Component\ClassLoader\Psr4ClassLoader;

    require_once __DIR__ . '/../vendor/autoload.php';

    $loader = new Psr4ClassLoader();
    $loader->addPrefix('DG\\SymfonyCert\\', __DIR__ . '/../src');
    $loader->register();
}