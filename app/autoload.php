<?php

require_once(APP_PATH . '/features.php');
require_once(APP_PATH . '/config/settings.php');

class bhvAutoloader
{
    public static function autoload($className)
    {
        $inApp = APP_PATH . '/model/' . str_replace('_', '/', $className) . '.php';
        if (file_exists($inApp)) {
            require_once $inApp;
        }

//        $inTest = APP_PATH . '/../qa/' . str_replace('_', '/', $className) . '.php';
//        if (file_exists($inTest)) {
//            require_once $inTest;
//        }

        $hlprLibPath = APP_PATH . '/lib/hlpr/autoload.php';
        require_once $hlprLibPath;

        $id3VendorPath = APP_PATH . '/../vendor/james-heinrich/getid3/getid3';
        require_once $id3VendorPath . '/getid3.php';
        require_once $id3VendorPath . '/write.php';
    }
}
