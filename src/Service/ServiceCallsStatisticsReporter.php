<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 26.09.15
 * Time: 17:13
 */

namespace DG\SymfonyCert\Service;


use DG\SymfonyCert\Event\ApiCallEvent;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ServiceCallsStatisticsReporter implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::TERMINATE => [
                ['report', 10]
            ]
        ];
    }

    public function report(ConsoleEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $output = $event->getOutput();

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $stat = ServiceCallsStatistics::getCalls();
            $tableFormatter = new Table($output);

            $tableFormatter->setHeaders(['Class Name', 'Api Path', 'Parameters']);
            foreach ($stat as $row) {
                $tableFormatter->addRow([
                    $row[0],
                    $row[0]::API_METHOD,
                    json_encode($row[1])
                ]);
            }
            $tableFormatter->render();

            if ($dispatcher instanceof TraceableEventDispatcher) {
                print_r($dispatcher->getCalledListeners());
            }
        }

        $event->stopPropagation();
    }
}