<?php

namespace kristijorgji\PhpTestGenerator\Generators;

use kristijorgji\PhpTestGenerator\Support\StringCollection;
use kristijorgji\PhpTestGenerator\Config\Config;
use kristijorgji\PhpTestGenerator\Config\SuiteConfig;
use kristijorgji\PhpTestGenerator\FileSystem\FileSystem;

class GeneratorFactory
{
    /**
     * @param array $config
     * @return GeneratorContract
     */
    public function get(array $config) : GeneratorContract
    {
        $fileSystem = new FileSystem();
        return new Generator(
            $fileSystem,
            new Config(
                ... array_map(function (string $suiteName, array $suiteConfig) {
                    return new SuiteConfig(
                        $suiteName,
                        $suiteConfig['sourcesPath'],
                        new StringCollection(...$suiteConfig['excludePatterns']),
                        $suiteConfig['namespace'],
                        $suiteConfig['outputDirectory'],
                        $suiteConfig['extends']
                    );
                }, array_keys($config['suites']), $config['suites'])
            )
        );
    }
}
