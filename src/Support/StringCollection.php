<?php

namespace kristijorgji\PhpTestGenerator\Support;

class StringCollection
{
    /**
     * @var string[]
     */
    private $elements = [];

    /**
     * @param string[] $fields
     */
    public function __construct(string... $fields)
    {
        $this->elements = $fields;
    }

    /**
     * @return string[]
     */
    public function all() : array
    {
        return $this->elements;
    }

    /**
     * @return int
     */
    public function count() : int
    {
        return count($this->elements);
    }
}
