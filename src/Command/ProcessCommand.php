<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 12.10.15
 * Time: 21:19
 */

namespace DG\SymfonyCert\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Process\Process;

class ProcessCommand extends Command
{
    protected function configure()
    {
        $this->setName('process:test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

//        $process = new Process('wkhtmltopdf http://google.com/ test.pdf');
//        $process->run();
//        $process->stop(3, SIGINT);

//        $process->mustRun(function ($type, $message) use ($output, $formatter) {
//            if ($type === Process::ERR) {
//                $output->writeln($formatter->formatBlock($message, 'error'));
//            } else {
//                $output->writeln($formatter->formatBlock($message, 'comment'));
//            }
//        });

        $process = new PhpProcess(<<<EOF
        <?php
            sleep(5);
            echo "OK\n";
        ?>
EOF
);
        $process->start();
        $process->stop(2, SIGINT);

        if (!$process->isSuccessful()) {
            $output->writeln($formatter->formatBlock($process->getErrorOutput(), 'error', true));
            return ;
        }
        $output->writeln($formatter->formatSection('success', $process->getOutput()));
    }
}