<?php

namespace Rithis\FindYourDomain\Command;

use React\EventLoop\LoopInterface,
    PronounceableWord_Generator,
    Wisdom\Wisdom;

use Rithis\FindYourDomain\StreamFinder;

class ManyCommand extends OneCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('many');
    }

    protected function getFinder(Wisdom $wisdom, PronounceableWord_Generator $generator, LoopInterface $loop)
    {
        return new StreamFinder($wisdom, $generator, $loop);
    }
}
