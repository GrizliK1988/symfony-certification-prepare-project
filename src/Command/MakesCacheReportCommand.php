<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 20.09.15
 * Time: 18:49
 */

namespace DG\SymfonyCert\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\ProcessBuilder;

class MakesCacheReportCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('makes:cache:report')
            ->addOption('progress', 'p', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

        $message = $formatter->formatSection('Section', 'Hello!', 'comment');
        $output->writeln($message);

        $blockMessage = $formatter->formatBlock(['Good luck!'], 'bg=black;fg=white', true);
        $output->writeln($blockMessage);

        /** @var ProcessHelper $processHelper */
        $processHelper = $this->getHelper('process');
        $process = ProcessBuilder::create(['figlet', 'Started!'])->getProcess();

        $processHelper->run($output, $process, 'Something went wrong');

        $finder = new Finder();
        $files = $finder->in(CACHE_PATH)->name('makes*json')->files();

        $progressHelper = new ProgressBar($output);
        $progressHelper->setEmptyBarCharacter('.');
        $progressHelper->setBarCharacter('<comment>+</comment>');

        if ($input->getOption('progress')) {
            $progressHelper->start($files->count());
        }

        $table = new Table($output);
        $table->setStyle('default');

        $style = new TableStyle();
        $style->setBorderFormat('<comment>%s</comment>');
        $table->setStyle($style);

        foreach ($files as $file) {
            /** @var SplFileInfo $file */
            $makes = json_decode($file->getContents(), true);

            $table->setHeaders(['Make Name', 'Models Count']);
            foreach ($makes['makes'] as $make) {
                $table->addRow([$make['name'], count($make['models'])]);
            }
//            $table->render($output);

            if ($input->getOption('progress')) {
                $progressHelper->advance();
            }

        }

        if ($input->getOption('progress')) {
            $progressHelper->finish();
            $output->writeln('');
        }
    }
}