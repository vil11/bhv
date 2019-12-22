<?php

/**
 * Loads corresponding app model class if it is called validly.
 * Loads corresponding test class if it is called validly.
 */
class autoloader
{
    public static function autoload($className)
    {
        $path = APP_PATH . 'model' . DS . str_replace('_', DS, $className) . '.php';
        if (file_exists($path)) {
            require_once $path;
        }

        $path = dirname(APP_PATH) . DS . 'qa' . DS . str_replace('_', DS, $className . '.php');
        if (file_exists($path)) {
            require_once $path;
        }
    }
}
