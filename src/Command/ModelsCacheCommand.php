<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.09.15
 * Time: 22:21
 */

namespace DG\SymfonyCert\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class ModelsCacheCommand extends Command
{
    protected function configure()
    {
        $this->setName('cache:models')
            ->addArgument('state', InputArgument::REQUIRED, 'new, used, future')
            ->addArgument('year', InputArgument::REQUIRED, 'Year is required')
            ->addArgument('view', InputArgument::OPTIONAL, 'full or basic', 'basic')
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        if ($input->getArgument('state') === null) {
            $question = new ChoiceQuestion('Select state', ['new', 'used', 'future'], 'new');
            $state = $questionHelper->ask($input, $output, $question);
            $input->setArgument('state', $state);
        }

        if ($input->getArgument('year') === null) {
            $question = new Question('Enter year: ', 1990);
            $question->setValidator(function($answer) {
                if ($answer < 1990) {
                    throw new \RuntimeException('Year has to be bigger then 1990');
                }

                return $answer;
            });

            $year = $questionHelper->ask($input, $output, $question);
            $input->setArgument('year', $year);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln('Performed!');
    }
}