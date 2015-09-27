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
    use DG\SymfonyCert\Command\MakesCacheCommand;
    use DG\SymfonyCert\Command\MakesCacheReportCommand;
    use DG\SymfonyCert\Service\EdmundsApi\MakesService;
    use DG\SymfonyCert\Service\Serializer\DelegatingSerializer;
    use DG\SymfonyCert\Service\Serializer\JsonToArraySerializer;
    use DG\SymfonyCert\Service\Serializer\JsonToStdClassSerializer;
    use Symfony\Component\Console\Application;
    use Symfony\Component\Console\ConsoleEvents;
    use Symfony\Component\Console\Event\ConsoleCommandEvent;
    use Symfony\Component\Console\Event\ConsoleExceptionEvent;
    use Symfony\Component\Console\Event\ConsoleTerminateEvent;
    use Symfony\Component\Debug\Debug;
    use Symfony\Component\EventDispatcher\EventDispatcher;
    use Symfony\Component\Finder\Finder;

    require __DIR__ . '/app/autoload.php';
    require __DIR__ . '/app/loadConfig.php';

    const ROOT_PATH = __DIR__ . '/';
    const CACHE_PATH = ROOT_PATH  . 'app/cache/';
    const CONFIG_PATH = ROOT_PATH  . 'app/config/';

    Debug::enable(E_ALL, true);

    $config = DG\App\loadConfig();

    $eventDispatcher = new EventDispatcher();

    $app = new Application();

    $serializer = new DelegatingSerializer();
    $serializer->addSerializer(new JsonToStdClassSerializer(), 'json_to_stdClass');
    $serializer->addSerializer(new JsonToArraySerializer(), 'json_to_array');

    $app->add(new MakesCacheCommand(new MakesService($config['api'], $config['key'], $serializer)));
    $app->add(new CssSelectorTestCommand());
    $app->add($reportCommand = new MakesCacheReportCommand());
    $app->add(new DomCrawlerTestCommand());
    $app->add(new DomCrawlerHHTestCommand());

    $app->setDefaultCommand($reportCommand->getName());
    $app->setDispatcher($eventDispatcher);

    $eventDispatcher->addListener(ConsoleEvents::COMMAND, function(ConsoleCommandEvent $event) use ($reportCommand) {
        $output = $event->getOutput();
        $output->writeln("<info>Run command " . $event->getCommand()->getName() . "</info>");

        $finder = new Finder();
        $makesCacheFilesCount = $finder->in(CACHE_PATH)->name('makes*.json')->count();

        if ($makesCacheFilesCount === 0 && $event->getCommand()->getName() === $reportCommand->getName()) {
            $output->writeln("<error>There is no cache files. Command execution stopped</error>");
            $event->disableCommand();
        }
    });

    $eventDispatcher->addListener(ConsoleEvents::TERMINATE, function(ConsoleTerminateEvent $event) {
        $output = $event->getOutput();

        $output->writeln("<info>" . $event->getCommand()->getName() . " execution stopped</info>");
    });

    $eventDispatcher->addListener(ConsoleEvents::EXCEPTION, function(ConsoleExceptionEvent $event) {
        //wrap exception
    });

    $app->run();
}
