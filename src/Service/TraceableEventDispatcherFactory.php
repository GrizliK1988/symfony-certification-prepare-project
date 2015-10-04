<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 03.10.15
 * Time: 23:26
 */

namespace DG\SymfonyCert\Service;


use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class TraceableEventDispatcherFactory
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    private $traceableEventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create()
    {
        if ($this->traceableEventDispatcher === null) {
            $this->traceableEventDispatcher = new TraceableEventDispatcher($this->eventDispatcher, new Stopwatch());
        }

        return $this->traceableEventDispatcher;
    }
} 