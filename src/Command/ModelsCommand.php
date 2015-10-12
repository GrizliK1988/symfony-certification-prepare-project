<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 12.10.15
 * Time: 20:03
 */

namespace DG\SymfonyCert\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ModelsCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('model:fetch')
            ->addArgument('make', InputArgument::REQUIRED)
            ->addArgument('model', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $model = $this->container->get('api.models')->getModel(
            $input->getArgument('make'), $input->getArgument('model'), [
                'year' => 2000,
                'state' => 'used'
            ]
        );

        print_r($model);
    }
}