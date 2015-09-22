<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 21.09.15
 * Time: 21:29
 */

namespace DG\SymfonyCert\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\Debug\DebugClassLoader;

class CssSelectorTestCommand extends Command
{
    protected function configure()
    {
        $this->setName('css:selector:test')->addArgument('selector', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $selector = $input->getArgument('selector');

        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

        $block = $formatter->formatBlock(CssSelector::toXPath($selector), 'question', true);

        $output->writeln($block);
    }
}