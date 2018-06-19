<?php

if (!function_exists('basePath')) {

    /**
     * @param string|null $path
     * @return string
     */
    function basePath(?string $path = null) : string
    {
        $basePath = __DIR__ . '/../';

        if ($path !== null) {
            return $basePath . '/' . $path;
        }

        return $basePath;
    }
}
