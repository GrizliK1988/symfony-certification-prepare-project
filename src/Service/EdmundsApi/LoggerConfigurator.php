<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 27.09.15
 * Time: 12:06
 */

namespace DG\SymfonyCert\Service\EdmundsApi;


use Psr\Log\LoggerInterface;

class LoggerConfigurator
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function configure(LoggedServiceInterface $loggedService)
    {
        $loggedService->setLogger($this->logger);
    }
} 