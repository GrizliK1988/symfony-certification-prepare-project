<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 26.09.15
 * Time: 17:13
 */

namespace DG\SymfonyCert\Service;


class ServiceCallsStatistics
{
    private static $calls = null;

    public function initCalls()
    {
        self::$calls = [];
    }

    public function add(...$params)
    {
        self::$calls[] = $params;
    }

    public static function getCalls()
    {
        return self::$calls;
    }
} 