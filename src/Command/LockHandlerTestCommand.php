<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 05.10.15
 * Time: 20:20
 */

namespace DG\SymfonyCert\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

class LockHandlerTestCommand extends Command
{
    protected function configure()
    {
        $this->setName('lock:test:run');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lockHandler = new LockHandler('lock:test:run.lock', CACHE_PATH);
        var_dump($lockHandler->lock());
        sleep(10);
        $lockHandler->release();
    }
}