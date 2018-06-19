<?php

namespace kristijorgji\PhpTestGenerator\Generators;

class GenerateSuiteResponse
{
    /**
     * @var string
     */
    private $suiteName;

    /**
     * @var string[]
     */
    private $generatedFilesPaths = [];

    /**
     * @param string $suiteName
     */
    public function __construct(string $suiteName)
    {
        $this->suiteName = $suiteName;
    }

    /**
     * @return string
     */
    public function getSuiteName(): string
    {
        return $this->suiteName;
    }

    /**
     * @param string $path
     */
    public function addPath(string $path)
    {
        $this->generatedFilesPaths[] = $path;
    }

    /**
     * @return array
     */
    public function getPaths() : array
    {
        return $this->generatedFilesPaths;
    }
}
