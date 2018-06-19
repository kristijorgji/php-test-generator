<?php

namespace kristijorgji\PhpTestGenerator\Config;

use kristijorgji\PhpTestGenerator\Support\StringCollection;

class SuiteConfig
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $sourcePath;

    /**
     * @var StringCollection
     */
    private $excludePatterns;

    /**
     * @var string
     */
    private $baseTestNamespace;

    /**
     * @var string
     */
    private $testPath;

    /**
     * @var string
     */
    private $extends;

    /**
     * @param string $name
     * @param string $sourcePath
     * @param StringCollection $excludePatterns
     * @param string $baseTestNamespace
     * @param string $testPath
     * @param string $extends
     */
    public function __construct(
        string $name,
        string $sourcePath,
        StringCollection $excludePatterns,
        string $baseTestNamespace,
        string $testPath,
        string $extends
    )
    {
        $this->name = $name;
        $this->sourcePath = $sourcePath;
        $this->excludePatterns = $excludePatterns;
        $this->baseTestNamespace = $baseTestNamespace;
        $this->testPath = $testPath;
        $this->extends = $extends;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }

    /**
     * @return StringCollection
     */
    public function getExcludePatterns(): StringCollection
    {
        return $this->excludePatterns;
    }

    /**
     * @return string
     */
    public function getBaseTestNamespace(): string
    {
        return $this->baseTestNamespace;
    }

    /**
     * @return string
     */
    public function getTestPath(): string
    {
        return $this->testPath;
    }

    /**
     * @return string
     */
    public function getExtends(): string
    {
        return $this->extends;
    }
}
