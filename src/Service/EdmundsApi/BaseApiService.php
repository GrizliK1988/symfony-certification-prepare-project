<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 27.09.15
 * Time: 12:15
 */

namespace DG\SymfonyCert\Service\EdmundsApi;


use DG\SymfonyCert\Service\ServiceCallsStatistics;
use Psr\Log\LoggerInterface;

abstract class BaseApiService implements LoggedServiceInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ServiceCallsStatistics
     */
    protected $statService;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setStatService(ServiceCallsStatistics $statistics)
    {
        $this->statService = $statistics;
    }
}