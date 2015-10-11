<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 11.10.15
 * Time: 16:58
 */

namespace DG\App;

use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

function initSession()
{
    $storage = new NativeSessionStorage([
        'cookie_lifetime' => 3600,
        'gc_probability' => 1,
        'gc_divisor' => 1,
        'gc_maxlifetime' => 10000,
//        'cache_limiter' => session_cache_limiter()
    ], new NativeFileSessionHandler());
    $session = new Session($storage, new NamespacedAttributeBag());
    $session->start();

    return $session;
}