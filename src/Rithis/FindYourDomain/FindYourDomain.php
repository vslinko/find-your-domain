<?php

namespace Rithis\FindYourDomain;

use React\EventLoop\Factory as EventLoopFactory;
use React\Dns\Resolver\Factory as DnsResolverFactory;
use React\Socket\Connection as SocketConnection;
use Wisdom\Wisdom;
use React\Whois\Client as WhoisClient;
use PronounceableWord_DependencyInjectionContainer;

class FindYourDomain
{
    const VERSION = "0.0.1-dev";

    private $loop;
    private $generator;
    private $wisdom;

    public function __construct()
    {
        $this->loop = $loop = EventLoopFactory::create();
        $factory = new DnsResolverFactory();
        $resolver = $factory->create('8.8.8.8', $loop);

        $this->wisdom = new Wisdom(new WhoisClient($resolver, function ($ip) use ($loop) {
            $fd = stream_socket_client("tcp://$ip:43");

            return new SocketConnection($fd, $loop);
        }));

        $container = new PronounceableWord_DependencyInjectionContainer();
        $this->generator = $container->getGenerator();
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
