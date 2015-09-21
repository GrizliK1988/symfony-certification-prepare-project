<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 20.09.15
 * Time: 18:55
 */

namespace DG\SymfonyCert\Application;


use DG\SymfonyCert\Command\MakesCacheReportCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class ReportConsoleApp extends Application
{
    protected function getCommandName(InputInterface $input)
    {
        parent::getCommandName($input);

        return 'makes:cache:report';
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new MakesCacheReportCommand();

        return $commands;
    }

    public function getDefinition()
    {
        $def = parent::getDefinition();

        $def->setArguments();

        return $def;
    }
} 