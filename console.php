<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.09.15
 * Time: 22:32
 */

namespace {
    use DG\SymfonyCert\Command\CssSelectorTestCommand;
    use DG\SymfonyCert\Command\DomCrawlerHHTestCommand;
    use DG\SymfonyCert\Command\DomCrawlerTestCommand;
    use DG\SymfonyCert\Command\ExpressionLanguageCommand;
    use DG\SymfonyCert\Command\LockHandlerTestCommand;
    use DG\SymfonyCert\Command\MakesCacheCommand;
    use DG\SymfonyCert\Command\MakesCacheReportCommand;
    use DG\SymfonyCert\Service\ServiceCallsStatisticsReporter;
    use Symfony\Component\Console\Application;
    use Symfony\Component\Console\ConsoleEvents;
    use Symfony\Component\Console\Event\ConsoleCommandEvent;
    use Symfony\Component\Console\Event\ConsoleExceptionEvent;
    use Symfony\Component\Debug\Debug;
    use Symfony\Component\EventDispatcher\EventDispatcher;
    use Symfony\Component\Finder\Finder;

    require __DIR__ . '/app/autoload.php';
    require __DIR__ . '/app/loadConfig.php';
    require __DIR__ . '/app/loadContainer.php';

    Debug::enable(E_ALL, true);

    $container = \DG\App\loadContainer();

    $eventDispatcher = $container->get('event_dispatcher.traceable');

    $app = new Application();

    $app->add(new MakesCacheCommand($container));
    $app->add(new CssSelectorTestCommand());
    $app->add($reportCommand = new MakesCacheReportCommand());
    $app->add(new DomCrawlerTestCommand());
    $app->add(new DomCrawlerHHTestCommand());
    $app->add(new ExpressionLanguageCommand());
    $app->add(new LockHandlerTestCommand());

    $app->setDefaultCommand($reportCommand->getName());
    $app->setDispatcher($eventDispatcher);

    $eventDispatcher->addListener(ConsoleEvents::COMMAND, function(ConsoleCommandEvent $event) use ($reportCommand) {
        $output = $event->getOutput();
        $output->writeln("<info>Run command " . $event->getCommand()->getName() . "</info>");

        if ($event->getCommand() instanceof MakesCacheReportCommand) {
            $finder = new Finder();
            $makesCacheFilesCount = $finder->in(CACHE_PATH)->name('makes*.json')->count();

            if ($makesCacheFilesCount === 0 && $event->getCommand()->getName() === $reportCommand->getName()) {
                $output->writeln("<error>There is no cache files. Command execution stopped</error>");
                $event->disableCommand();
            }
        }
    });

    $eventDispatcher->addListener(ConsoleEvents::EXCEPTION, function(ConsoleExceptionEvent $event) {
        //wrap exception
    });

    $eventDispatcher->addSubscriber(new ServiceCallsStatisticsReporter());

    $app->run();
}
