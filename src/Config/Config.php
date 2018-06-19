<?php

namespace kristijorgji\PhpTestGenerator\Config;

class Config
{
    /**
     * @var SuiteConfig[]
     */
    private $suiteConfigs;

    /**
     * @param SuiteConfig[] ...$suiteConfigs
     */
    public function __construct(
        SuiteConfig ... $suiteConfigs
    )
    {

        $this->suiteConfigs = $suiteConfigs;
    }

    /**
     * @return SuiteConfig[]
     */
    public function getSuiteConfigs(): array
    {
        return $this->suiteConfigs;
    }
}
