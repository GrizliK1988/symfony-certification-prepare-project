<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.09.15
 * Time: 22:21
 */

namespace DG\SymfonyCert\Command;


use DG\SymfonyCert\Event\ApiCallEvent;
use DG\SymfonyCert\Event\MakesCacheEvent;
use DG\SymfonyCert\Service\EdmundsApi\MakesService;
use DG\SymfonyCert\Service\EdmundsApi\ModelsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;

class MakesCacheCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('cache:models')
            ->addArgument('state', InputArgument::REQUIRED, 'new, used, future')
            ->addArgument('year', InputArgument::REQUIRED, 'Year is required')
            ->addArgument('view', InputArgument::OPTIONAL, 'full or basic', 'basic')
            ->addOption('exclude-brand', 'e', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED)
            ->addOption('progress', 'p', InputOption::VALUE_NONE)
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $input->setOption('exclude-brand', $input->getOption('exclude-brand') ?: []);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        if ($input->getArgument('state') === null) {
            $output->writeln('<info>State required</info>');

            $question = new ChoiceQuestion('<question>Select state</question>', ['new', 'used', 'future'], 'new');
            $state = $questionHelper->ask($input, $output, $question);
            $input->setArgument('state', $state);
        }

        if ($input->getArgument('year') === null) {
            $output->writeln('<info>Year required</info>');

            $question = new Question('<question>Enter year:</question> ', 1990);
            $question->setValidator(function($answer) {
                if ($answer < 1990) {
                    throw new \RuntimeException('Year has to be bigger then 1990');
                }

                return $answer;
            });

            $year = $questionHelper->ask($input, $output, $question);
            $input->setArgument('year', $year);
        }

        $style = new OutputFormatterStyle('black', 'white', ['bold', 'underscore']);
        $output->getFormatter()->setStyle('thanks', $style);
        $output->writeln('<thanks>Thanks!</thanks>');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cachePath = CACHE_PATH . sprintf('makes_%s_%s_%s.json', $input->getArgument('state'), $input->getArgument('year'), $input->getArgument('view'));

        /** @var MakesService $makesService */
        $makesService = $this->container->get('api.makes');
        $makesJson = $makesService->getMakes(
            $input->getArgument('state'),
            $input->getArgument('year'),
            $input->getArgument('view')
        );
        $makes = $makesJson;

        $makes['makes'] = array_values(array_filter($makes['makes'], function (array $make) use ($input, &$makes) {
            $ok = !in_array($make['name'], $input->getOption('exclude-brand'));

            if (!$ok) {
                $makes['makesCount']--;
            }

            return $ok;
        }));

        file_put_contents($cachePath, json_encode($makes, JSON_PRETTY_PRINT));

        /** @var ContainerAwareEventDispatcher $dispatcher */
        $dispatcher = $this->container->get('event_dispatcher.traceable');

        $dispatcher->addListener(ApiCallEvent::EVENT_NAME, function (GenericEvent $event) use ($output) {
            $output->writeln('<comment>Api call from ' . get_class($event->getSubject()) . '</comment>');
        });

        $dispatcher->dispatch(MakesService::EVENT_CACHE_COMPLETE,
            new MakesCacheEvent($makes, $input->getArgument('state'), $input->getArgument('year'), $input->getArgument('view'))
        );

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $output->writeln('<info>Total count = ' . $makes['makesCount'] . '</info>');
        }
    }
}