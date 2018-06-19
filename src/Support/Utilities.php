<?php

namespace kristijorgji\PhpTestGenerator\Support;

class Utilities
{
    /**
     * @param string|null $path
     * @return string
     */
    public static function basePath(?string $path = null) : string
    {
        $basePath = __DIR__ . '/../../';

        if ($path !== null) {
            return $basePath . '/' . $path;
        }

        return $basePath;
    }
}
