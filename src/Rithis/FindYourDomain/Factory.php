<?php

namespace Rithis\FindYourDomain;

use React\Dns\Resolver\Factory as DnsResolverFactory,
    React\EventLoop\Factory as EventLoopFactory,
    React\Socket\Connection as SocketConnection,
    React\Whois\Client as WhoisClient,
    Wisdom\Wisdom;

use PronounceableWord_DependencyInjectionContainer;

class Factory
{
    public static function factory()
    {
        $loop = EventLoopFactory::create();
        $factory = new DnsResolverFactory();
        $resolver = $factory->create('8.8.8.8', $loop);

        $wisdom = new Wisdom(new WhoisClient($resolver, function ($ip) use ($loop) {
            $fd = stream_socket_client("tcp://$ip:43");

            return new SocketConnection($fd, $loop);
        }));

        $container = new PronounceableWord_DependencyInjectionContainer();
        $generator = $container->getGenerator();

        return new FindYourDomain($loop, $wisdom, $generator);
    }
}
