<?php

namespace Rithis\FindYourDomain;

use PronounceableWord_Generator,
    Wisdom\Wisdom;

use React\Promise\ResolverInterface,
    React\Promise\Deferred;

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
        $deferred = new Deferred();

        $progressHandler = function () use ($deferred, $length, $tlds) {
            $this->search($deferred->resolver(), $length, $tlds);
        };

        $progressHandler();

        $deferred->then(null, null, $progressHandler);

        return $deferred->promise();
    }

    protected function search(ResolverInterface $resolver, $length, $tlds)
    {
        $name = $this->generator->generateWordOfGivenLength($length);

        $domains = array_map(function ($tld) use ($name) {
            return "$name.$tld";
        }, $tlds);

        $this->wisdom->checkAll($domains)->then($this->buildCallback($resolver));
    }

    protected function buildCallback(ResolverInterface $resolver)
    {
        return function ($result) use ($resolver) {
            if (!in_array(false, $result, true)) {
                $resolver->resolve(array_keys($result));
            } else {
                $resolver->progress();
            }
        };
    }
}
