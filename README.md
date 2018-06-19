# php-test-generator

A framework for generating test boilerplate from the specified suites.

# Table of Contents

- [Installation](#installation)
- [Config](#config)

## Installation

```sh
composer require kristijorgji/php-test-generator
```

Run the following command to initialize phpTestGenerator
```sh
vendor/bin/phpTestGenerator init
```
This command will create in your project root folder the config file `phpTestGenerator.cfg.php`
You need to edit that to your desired settings.

## Config

The config and it's keys try to be as much self explanatory as possible.
Example config:

```php
<?php

return [
    'suites' => [
        'code' => [
            'sourcesPath' => 'app',
            'excludePatterns' => [
                'Console',
                'Constants', 
                '#.*Interface\.php$#',
                '#.*Exception\.php$#',
            ],
            'outputDirectory' => 'tests/unit/app',
            'namespace' => 'UnitTests',
            'extends' => '\Tests\Helpers\TestCase'
        ],
        'database' => [
            'sourcesPath' => 'app/Repositories',
            'excludePatterns' => [
                'Contracts'
            ],
            'outputDirectory' => 'tests/unit/app/Repositories',
            'namespace' => 'UnitTests\App\Repositories',
            'extends' => 'Tests\Helpers\FixtureTestCase'
        ],
    ]
];
```

The exclude patterns can be directory names (relative to suite source paths), or regular expressions.
In the former case, the pattern should be defined within #, like in the example config.

## License

php-test-generator is released under the MIT Licence. See the bundled LICENSE file for details.







