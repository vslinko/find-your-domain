<?php

namespace Rithis\FindYourDomain\Command;

use PronounceableWord_Generator,
    Wisdom\Wisdom;

use Rithis\FindYourDomain\StreamFinder,
    Rithis\FindYourDomain\Finder;

class ManyCommand extends OneCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('many');
    }

    protected function createFinder(Wisdom $wisdom, PronounceableWord_Generator $generator, $length, $tlds, $callback)
    {
        $finder = new StreamFinder(new Finder($wisdom, $generator), $length, $tlds);
        $finder->on('domains-found', $callback);
        $finder->start();
    }
}
