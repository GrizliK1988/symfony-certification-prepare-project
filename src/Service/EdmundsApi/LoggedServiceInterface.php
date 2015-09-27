<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 27.09.15
 * Time: 12:05
 */

namespace DG\SymfonyCert\Service\EdmundsApi;


use Psr\Log\LoggerInterface;

interface LoggedServiceInterface
{
    public function setLogger(LoggerInterface $logger);
} 