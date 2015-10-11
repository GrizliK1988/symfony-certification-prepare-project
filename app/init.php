<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 11.10.15
 * Time: 16:58
 */

namespace DG\App;

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Translation\TranslatorInterface;

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

function initTwig(ContainerInterface $container, TranslatorInterface $translator)
{
    $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
    $twigLoader = new \Twig_Loader_Filesystem([
        VIEWS_PATH,
        dirname($appVariableReflection->getFileName()) . '/Resources/views/Form'
    ]);
    $twig = new \Twig_Environment($twigLoader);
    $formEngine = new TwigRendererEngine(['bootstrap_3_horizontal_layout.html.twig']);
    $formEngine->setEnvironment($twig);
    $twig->addExtension(new FormExtension(new TwigRenderer($formEngine, $container->get('csrf_token.manager'))));

    $twig->addExtension(new TranslationExtension($translator));
    $container->set('twig', $twig);

    return $twig;
}