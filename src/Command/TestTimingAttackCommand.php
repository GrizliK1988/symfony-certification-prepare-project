<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 25.10.15
 * Time: 21:03
 */

namespace DG\SymfonyCert\Command;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestTimingAttackCommand extends Command
{
    protected function configure()
    {
        $this->setName('test:timing:attack');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = 'http://localhost:8000/app.php/api/admin';
        $f = fopen(CACHE_PATH . 'test.csv', 'wb');

        $client = new Client();

        $row = [];
        for ($len = 15; $len <= 25; ++$len) {
            $row[] = $len;
        }
        fputcsv($f, $row);

        $progress = new ProgressBar($output, 1000);
        for ($i = 0; $i < 1000; ++$i) {
            $row = [];
            for ($len = 15; $len <= 25; ++$len) {
                $start = \microtime(true);

                $request = new Request('GET', $url, [
                    'X-Authentication-Key' => '654321',
                    'X-Test' => str_pad('', $len, 'b')
                ]);
                $response = $client->send($request);

                $row[] = \microtime(true) - $start;
            }
            fputcsv($f, $row);
            $progress->advance();
        }

        fclose($f);
    }
}