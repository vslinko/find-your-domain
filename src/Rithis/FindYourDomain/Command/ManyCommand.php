<?php

namespace Rithis\FindYourDomain\Command;

use Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputInterface;

class ManyCommand extends OneCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('many');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        while (true) {
            $this->doExecute($input, $output);
        }
    }
}
