<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 27.09.15
 * Time: 18:07
 */

namespace DG\SymfonyCert\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DomCrawler\Crawler;

class DomCrawlerHHTestCommand extends Command
{
    protected function configure()
    {
        $this->setName('dom:crawler:hh:test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

        $html = file_get_contents('http://spb.hh.ru/search/vacancy?text=php&clusters=true&enable_snippets=true');
        $crawler = new Crawler($html, 'http://spb.hh.ru/search/vacancy?text=php&clusters=true&enable_snippets=true');

        $output->writeln($formatter->formatBlock('Vacancies', 'question', true));
        $crawler->filterXPath(CssSelector::toXPath('body a.search-result-item__name'))->each(function(Crawler $node) use ($output) {
            $output->writeln(sprintf('<comment>%s</comment>', $node->text()));
        });

        $data = $crawler->filter('body a.search-result-item__name')->extract(['_text', 'class']);
        print_r($data[0]);

        $output->writeln($formatter->formatBlock('Link test', 'question', true));
        $link = $crawler->selectLink('PHP developer')->link();
        $output->writeln($link->getUri());

        $crawler = new Crawler(file_get_contents('http://spb.hh.ru/login'), 'http://spb.hh.ru/login');
        $output->writeln($formatter->formatBlock('Form test', 'question', true));
        $form = $crawler->selectButton('Войти')->form([
            'username' => 'name',
            'password' => 'pass'
        ]);
        $output->writeln($form->getUri());

        $form['remember']->tick();
        $output->writeln(print_r($form->getPhpValues()));
    }
}