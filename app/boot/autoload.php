<?php

/**
 * Loads corresponding app model class if it is called validly.
 * Loads corresponding test class if it is called validly.
 */
class autoloader
{
    /** @param string $className */
    public static function autoload(string $className)
    {
        $suffix = DS . str_replace('_', DS, $className) . '.php';

        $path = PATH_APP . 'model' . $suffix;
        if (file_exists($path)) {
            require_once $path;
        }

        $path = PATH_QA . 'tests' . $suffix;
        if (file_exists($path)) {
            require_once $path;
        }
    }
}
