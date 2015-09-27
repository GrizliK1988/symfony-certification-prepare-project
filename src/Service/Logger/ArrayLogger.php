<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 25.09.15
 * Time: 22:14
 */

namespace DG\SymfonyCert\Service\Logger;


use Psr\Log\AbstractLogger;

class ArrayLogger extends AbstractLogger
{
    private $logs = [];

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $this->logs[] = [$level, $message];
    }

    public function getLogs()
    {
        return $this->logs;
    }
} 