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
$callback = function ($domains) use ($output) {
    echo implode(' ', $domains), "\n";
};

$length = 5;
$tlds = array('com', 'net', 'io');

$this->finder->find($callback, $length, $tlds);
// Example output: absof.com absof.net absof.io
```

### Console

After installation you can use our console. Just run:

```./vendor/bin/find-your-domain one -l5 -tcom -tnet -tio```

Or you can run never-ending search, and find best domains from stream:

```./vendor/bin/find-your-domain many -l5 -tcom -tnet -tio```
