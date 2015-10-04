<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 03.10.15
 * Time: 23:14
 */

namespace DG\SymfonyCert\Service;


use DG\SymfonyCert\Service\EdmundsApi\BaseApiService;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;

class ApiServiceConfigurator
{
    public static function configure(BaseApiService $apiService)
    {
        $dispatcher = $apiService->getDispatcher();
        if ($dispatcher && !($dispatcher instanceof TraceableEventDispatcher)) {
            $apiService->setDispatcher(
                new TraceableEventDispatcher($dispatcher, new Stopwatch())
            );
        }
    }
} 