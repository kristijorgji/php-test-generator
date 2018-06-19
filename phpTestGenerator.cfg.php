<?php

return [
    'suites' => [
        'code' => [
            'sourcesPath' => 'Samples/app',
            'excludePatterns' => [
                'Console',
                'Constants',
                'Entities',
                'Events',
                'Exceptions',
                'Http',
                'Listeners',
                'Providers',
                'Repositories',
                'helpers.php',
                '#.*Exception\.php$#'
            ],
            'outputDirectory' => 'Output/unit/app',
            'namespace' => 'UnitTests',
            'extends' => '\Tests\Helpers\TestCase'
        ],
        'database' => [
            'sourcesPath' => 'Samples/app/Repositories',
            'excludePatterns' => [
                'Contracts'
            ],
            'outputDirectory' => 'Output/unit/app/Repositories',
            'namespace' => 'UnitTests\App\Repositories',
            'extends' => 'Tests\Helpers\FixtureTestCase'
        ],
    ]
];
