<?php

namespace Rithis\FindYourDomain;

use PronounceableWord_Generator,
    Wisdom\Wisdom;

class Finder
{
    private $wisdom;
    private $generator;

    private $userCallback;
    private $length;
    private $tlds;
    private $found;

    public function __construct(Wisdom $wisdom, PronounceableWord_Generator $generator)
    {
        $this->wisdom = $wisdom;
        $this->generator = $generator;
    }

    public function find($callback, $length = 5, $tlds = array('com', 'net'))
    {
        $this->userCallback = $callback;
        $this->length = $length;
        $this->tlds = $tlds;
        $this->found = false;

        $this->search();
    }

    protected function search()
    {
        $name = $this->generator->generateWordOfGivenLength($this->length);

        $domains = array_map(function ($tld) use ($name) {
            return "$name.$tld";
        }, $this->tlds);

        $this->wisdom->checkAll($domains)->then($this->buildCallback($this->userCallback));
    }

    protected function buildCallback($userCallback)
    {
        return function ($results) use ($userCallback) {
            if (!in_array(false, $results, true) && !$this->found) {
                $this->found = true;
                $userCallback(array_keys($results));
            } else if (!$this->found) {
                $this->search();
            }
        };
    }
}
