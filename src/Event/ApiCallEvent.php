<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 30.09.15
 * Time: 22:09
 */

namespace DG\SymfonyCert\Event;


use Symfony\Component\EventDispatcher\GenericEvent;

class ApiCallEvent extends GenericEvent
{
    const EVENT_NAME = 'api.call';
}