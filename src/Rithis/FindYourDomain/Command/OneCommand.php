<?php

namespace Rithis\FindYourDomain\Command;

use Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Command\Command;

use React\Dns\Resolver\Factory as DnsResolverFactory,
    React\EventLoop\Factory as EventLoopFactory,
    React\Socket\Connection as SocketConnection,
    React\Whois\Client as WhoisClient,
    React\EventLoop\LoopInterface,
    Wisdom\Wisdom;

use PronounceableWord_DependencyInjectionContainer,
    PronounceableWord_Generator;

use Rithis\FindYourDomain\Finder;

class OneCommand extends Command
{
    protected function configure()
    {
        $this->setName('one');
        $this->addOption('length', 'l', InputOption::VALUE_REQUIRED, 'Domain name length', 5);
        $this->addOption('tlds', 't', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'TLDs', array('com', 'net'));
        $this->addOption('dns-server', 'd', InputOption::VALUE_REQUIRED, 'DNS server address', '8.8.8.8');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = EventLoopFactory::create();
        $factory = new DnsResolverFactory();
        $resolver = $factory->create($input->getOption('dns-server'), $loop);

        $wisdom = new Wisdom(new WhoisClient($resolver, function ($ip) use ($loop) {
            return new SocketConnection(stream_socket_client("tcp://$ip:43"), $loop);
        }));

        $container = new PronounceableWord_DependencyInjectionContainer();
        $generator = $container->getGenerator();

        $finder = $this->getFinder($wisdom, $generator, $loop);

        $callback = function ($domains) use ($output) {
            $output->writeln(implode(' ', $domains));
        };
        $length = $input->getOption('length');
        $tlds = $input->getOption('tlds');

        $finder->find($callback, $length, $tlds);

        $loop->run();
    }

    protected function getFinder(Wisdom $wisdom, PronounceableWord_Generator $generator, LoopInterface $loop)
    {
        return new Finder($wisdom, $generator);
    }
}
