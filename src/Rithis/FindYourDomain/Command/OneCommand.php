<?php

namespace Rithis\FindYourDomain\Command;

use Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Command\Command;

use React\Dns\Resolver\Factory as DnsResolverFactory,
    React\EventLoop\Factory as EventLoopFactory,
    React\Whois\ConnectionFactory as WhoisConnectionFactory,
    React\Whois\Client as WhoisClient,
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
        list($loop, $wisdom, $generator) = $this->prepareDependencies($input);

        $length = $input->getOption('length');
        $tlds = $input->getOption('tlds');
        $callback = $this->buildCallback($output);

        $this->createFinder($wisdom, $generator, $length, $tlds, $callback);

        $loop->run();
    }

    protected function createFinder(Wisdom $wisdom, PronounceableWord_Generator $generator, $length, $tlds, $callback)
    {
        $finder = new Finder($wisdom, $generator);
        $finder->find($length, $tlds)->then($callback);
    }

    protected function prepareDependencies(InputInterface $input)
    {
        $loop = EventLoopFactory::create();
        $factory = new DnsResolverFactory();
        $resolver = $factory->create($input->getOption('dns-server'), $loop);

        $wisdom = new Wisdom(new WhoisClient($resolver, new WhoisConnectionFactory($loop)));

        $container = new PronounceableWord_DependencyInjectionContainer();
        $generator = $container->getGenerator();

        return array($loop, $wisdom, $generator);
    }

    protected function buildCallback(OutputInterface $output)
    {
        return function ($domains) use ($output) {
            $output->writeln(implode(' ', $domains));
        };
    }
}
