<?php

namespace Rithis\FindYourDomain\Command;

use Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Command\Command;

use Rithis\FindYourDomain\Factory as FindYourDomainFactory;

class OneCommand extends Command
{
    private $finder;

    protected function configure()
    {
        $this->setName('one');
        $this->addOption('length', 'l', InputOption::VALUE_REQUIRED, 'Domain name length', 5);
        $this->addOption('tlds', 't', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'TLDs', array('com', 'net'));

        $this->finder = FindYourDomainFactory::factory();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doExecute($input, $output);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $callback = function ($domains) use ($output) {
            $output->writeln(implode(' ', $domains));
        };

        $length = $input->getOption('length');
        $tlds = $input->getOption('tlds');

        $this->finder->find($callback, $length, $tlds);
    }
}
