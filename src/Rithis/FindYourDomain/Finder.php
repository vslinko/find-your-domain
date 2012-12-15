<?php

namespace Rithis\FindYourDomain;

use PronounceableWord_Generator,
    Wisdom\Wisdom;

use React\Promise\ResolverInterface,
    React\Promise\Deferred;

use React\Promise\When;
use React\Curry\Util as Curry;

class Finder
{
    private $wisdom;
    private $generator;

    public function __construct(Wisdom $wisdom, PronounceableWord_Generator $generator)
    {
        $this->wisdom = $wisdom;
        $this->generator = $generator;
    }

    public function find($length = 5, $tlds = array('com', 'net'))
    {
        $searchFactory = function () use ($length, $tlds) {
            return $this->search($length, $tlds);
        };

        $errorHandler = function ($errorHandler) use ($searchFactory) {
            $promises = array();
            for ($i = 0; $i < 3; $i++) {
                $promises[] = $searchFactory();
            }

            return When::any($promises)->then(null, $errorHandler);
        };

        $process = Curry::bind($errorHandler, array($errorHandler));

        return $process();
    }

    protected function search($length, $tlds)
    {
        $name = $this->generator->generateWordOfGivenLength($length);

        $domains = array_map(function ($tld) use ($name) {
            return "$name.$tld";
        }, $tlds);

        return $this->wisdom->checkAll($domains);
    }
}
