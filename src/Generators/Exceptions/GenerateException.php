<?php

namespace kristijorgji\PhpTestGenerator\Generators\Exceptions;

use kristijorgji\PhpTestGenerator\Generators\GenerateResponse;

class GenerateException extends \Exception
{
    /**
     * @var GenerateResponse
     */
    private $partialResponse;

    /**
     * @param string $message
     * @param \Exception|null $previous
     * @param GenerateResponse $partialResponse
     */
    public function __construct(string $message, ?\Exception $previous, GenerateResponse $partialResponse)
    {
        parent::__construct($message, -177, $previous);
        $this->partialResponse = $partialResponse;
    }

    /**
     * @return GenerateResponse
     */
    public function getPartialResponse(): GenerateResponse
    {
        return $this->partialResponse;
    }
}
