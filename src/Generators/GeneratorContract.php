<?php

namespace kristijorgji\PhpTestGenerator\Generators;

abstract class GeneratorContract
{
    /**
     * @return GenerateResponse
     */
    abstract public function generate() : GenerateResponse;
}
