<?php

namespace Rithis\FindYourDomain;

use Evenement\EventEmitter;

class StreamFinder extends EventEmitter
{
    private $finder;
    private $length;
    private $tlds;

    public function __construct(Finder $finder, $length = 5, $tlds = array('com', 'net'))
    {
        $this->finder = $finder;
        $this->length = $length;
        $this->tlds = $tlds;
    }

    public function start()
    {
        $this->findRecursively();
    }

    private function findRecursively()
    {
        $this->finder->find($this->length, $this->tlds)->then(function ($domains) {
            $this->emit('domains-found', array($domains));
            $this->findRecursively();
        });
    }
}
