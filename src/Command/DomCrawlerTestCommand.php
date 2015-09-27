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
use Symfony\Component\DomCrawler\Crawler;

class DomCrawlerTestCommand extends Command
{
    protected function configure()
    {
        $this->setName('dom:crawler:test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

        $crawlerXml = new Crawler(file_get_contents(__DIR__ . '/test.xml'));
        $output->writeln($formatter->formatBlock('Sibling test', 'question', true));
        $crawlerXml->filter('node')->siblings()->each(function(Crawler $node) use ($output) {
            $output->writeln(sprintf('<info>%s</info>', $node->text()));
        });

        $output->writeln($formatter->formatBlock('Test adding content', 'question', true));
        $crawler = new Crawler('<root><node id="1" /><node id="2" /></root>');
        $document = new \DOMDocument();
        $document->loadXML('<root><node id="3" /><node id="4" /></root>');
        $crawler->addDocument($document);
        $crawler->filter('node')->each(function(Crawler $node) use ($output) {
            $output->writeln($node->nodeName() . ' ' . $node->attr('id'));
        });
    }
}