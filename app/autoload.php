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

        $id3LibPath = APP_PATH . '../vendor/james-heinrich/getid3';
//        require_once $id3LibPath . '/getid3.php';
//        require_once $id3LibPath . '/write.php';

        $hlprLibPath = APP_PATH . '/lib/hlpr/autoload.php';
        if (file_exists($hlprLibPath)) {
             require_once $hlprLibPath;
        }
    }
}
