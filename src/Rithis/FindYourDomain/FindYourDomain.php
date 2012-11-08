<?php

namespace Rithis\FindYourDomain;

use React\EventLoop\LoopInterface,
    PronounceableWord_Generator,
    Wisdom\Wisdom;

class FindYourDomain
{
    const VERSION = "0.0.1-dev";

    private $loop;
    private $generator;
    private $wisdom;

    public function __construct(LoopInterface $loop, Wisdom $wisdom, PronounceableWord_Generator $generator)
    {
        $this->loop = $loop;
        $this->wisdom = $wisdom;
        $this->generator = $generator;
    }

    public function find($callback, $length = 5, $tlds = array('com', 'net'))
    {
        $found = false;

        while (!$found) {
            $name = $this->generator->generateWordOfGivenLength($length);

            $domains = array_map(function ($tld) use ($name) {
                return "$name.$tld";
            }, $tlds);

            $this->wisdom->checkAll($domains)->then(function ($results) use ($callback, &$found, $domains) {
                if (!in_array(false, $results, true)) {
                    $found = true;
                    $callback($domains);
                }
            });

            $this->loop->run();
        }
    }
}
