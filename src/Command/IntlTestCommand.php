<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 11.10.15
 * Time: 20:57
 */

namespace DG\SymfonyCert\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Intl\Data\Bundle\Compiler\GenrbCompiler;
use Symfony\Component\Intl\Data\Bundle\Reader\BundleEntryReader;
use Symfony\Component\Intl\Data\Bundle\Reader\PhpBundleReader;
use Symfony\Component\Intl\Data\Bundle\Writer\PhpBundleWriter;
use Symfony\Component\Intl\Data\Bundle\Writer\TextBundleWriter;
use Symfony\Component\Intl\Intl;

class IntlTestCommand extends Command
{
    protected function configure()
    {
        $this->setName('intl:test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $textWriter = new TextBundleWriter();
        $textWriter->write(CACHE_PATH . 'intl/text_bundle', 'en', [
            'data' => ['Ok']
        ]);
        $textWriter->write(CACHE_PATH . 'intl/text_bundle', 'ru', [
            'data' => ['Хорошо'],
        ]);

        $phpWriter = new PhpBundleWriter();
        $phpWriter->write(CACHE_PATH . 'intl/php_bundle', 'en', [
            'data' => 'php bundle: Ok',
            'nested' => [
                'message' => 'Hi!'
            ]
        ]);
        $phpWriter = new PhpBundleWriter();
        $phpWriter->write(CACHE_PATH . 'intl/php_bundle', 'ru', [
            'data' => 'php bundle: Хорошо',
            'nested' => [
                'message' => 'Привет!'
            ]
        ]);

        $compiler = new GenrbCompiler();
        $compiler->compile(CACHE_PATH . 'intl/text_bundle', CACHE_PATH . 'intl/compiled');

        $phpReader = new PhpBundleReader();
        $data = $phpReader->read(CACHE_PATH . 'intl/php_bundle', 'en');
        $output->writeln($data['data']);
        $data = $phpReader->read(CACHE_PATH . 'intl/php_bundle', 'ru');
        $output->writeln($data['data']);

        $reader = new BundleEntryReader($phpReader);
        $data = $reader->readEntry(CACHE_PATH . 'intl/php_bundle', 'ru', ['nested', 'message']);
        $output->writeln($data);

        $language = Intl::getLanguageBundle()->getLanguageName('ru', 'RU', 'en');
        $output->writeln($language);
        $language = Intl::getLanguageBundle()->getLanguageName('ru', 'RU', 'de');
        $output->writeln($language);
        $language = Intl::getLanguageBundle()->getLanguageName('ru', 'RU', 'ru');
        $output->writeln($language);

        $currencyName = Intl::getCurrencyBundle()->getCurrencyName('RUB', 'en');
        $output->writeln($currencyName);
        $currencyName = Intl::getCurrencyBundle()->getCurrencySymbol('USD', 'en');
        $output->writeln($currencyName);

        $output->writeln('<comment>Ok</comment>');
    }
}