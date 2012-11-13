# Find Your Domain

Find best domains in your TLDs

## Installation

Add requirement in your composer.json:

```json
{
    "require": {
        "rithis/find-your-domain": "@dev"
    }
}
```

After that run `composer update rithis/find-your-domain`.

## Usage

### Library

```php
<?php

use React\Dns\Resolver\Factory as DnsResolverFactory,
    React\EventLoop\Factory as EventLoopFactory,
    React\Socket\Connection as SocketConnection,
    React\Whois\Client as WhoisClient,
    Wisdom\Wisdom;

$callback = function ($domains) use ($output) {
    echo implode(' ', $domains), "\n";
};

$length = 5;
$tlds = array('com', 'net', 'io');

$loop = EventLoopFactory::create();
$factory = new DnsResolverFactory();
$resolver = $factory->create($input->getOption('dns-server'), $loop);

$wisdom = new Wisdom(new WhoisClient($resolver, function ($ip) use ($loop) {
    return new SocketConnection(stream_socket_client("tcp://$ip:43"), $loop);
}));

$container = new PronounceableWord_DependencyInjectionContainer();
$generator = $container->getGenerator();

$finder = $this->getFinder($wisdom, $generator, $loop);
$finder->find($callback, $length, $tlds);

$loop->run();
```

Example output: `absof.com absof.net absof.io`

### Console

After installation you can use our console. Just run:

```./vendor/bin/find-your-domain one -l5 -tcom -tnet -tio```

Or you can run never-ending search, and find best domains from stream:

```./vendor/bin/find-your-domain many -l5 -tcom -tnet -tio```
