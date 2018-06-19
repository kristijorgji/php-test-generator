<?php

namespace kristijorgji\PhpTestGenerator\Generators;

class GenerateResponse
{
    /**
     * @var GenerateSuiteResponse[]
     */
    private $responses = [];

    /**
     * @param GenerateSuiteResponse $generateSuiteResponse
     */
    public function addGenerateSuiteResponse(GenerateSuiteResponse $generateSuiteResponse)
    {
        $this->responses[] = $generateSuiteResponse;
    }

    /**
     * @return GenerateSuiteResponse[]
     */
    public function getResponses() : array
    {
        return $this->responses;
    }
}
