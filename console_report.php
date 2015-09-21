<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 20.09.15
 * Time: 18:58
 */

namespace {
    use DG\SymfonyCert\Application\ReportConsoleApp;

    require_once __DIR__ . '/app/autoload.php';

    $app = new ReportConsoleApp();
    $app->run();
}