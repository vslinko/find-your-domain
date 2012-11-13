<?php

namespace Rithis\FindYourDomain;

use React\EventLoop\LoopInterface,
    PronounceableWord_Generator,
    Wisdom\Wisdom;

class StreamFinder extends Finder
{
    private $loop;
    private $period;
    private $queueLimit;
    private $queue = array();

    public function __construct(Wisdom $wisdom, PronounceableWord_Generator $generator, LoopInterface $loop,
                                $period = 1, $queueLimit = 10)
    {
        parent::__construct($wisdom, $generator);

        $this->loop = $loop;
        $this->period = $period;
        $this->queueLimit = $queueLimit;
    }

    public function find($callback, $length = 5, $tlds = array('com', 'net'))
    {
        parent::find(function () {}, $length, $tlds);

        $this->loop->addPeriodicTimer($this->period, function () use ($callback) {
            $result = array_shift($this->queue);

            if ($result) {
                $callback(array_keys($result));
            }
        });
    }

    protected function search()
    {
        if (count($this->queue) < $this->queueLimit) {
            parent::search();
        } else {
            $this->loop->addTimer($this->period, function () {
                parent::search();
            });
        }
    }

    protected function buildCallback($userCallback)
    {
        return function ($results) {
            if (!in_array(false, $results, true)) {
                $this->queue[] = $results;
            }

            $this->search();
        };
    }
}
