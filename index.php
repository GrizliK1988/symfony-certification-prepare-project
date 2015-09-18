<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.09.15
 * Time: 21:54
 */

namespace {
    use DG\SymfonyCert\Controller\HomeController;

    require_once __DIR__ . '/app/autoload.php';

    $controller = new HomeController();
    $controller->xmlAction()
        ->sendHeaders()
        ->sendContent();
}