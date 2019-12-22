<?php


// 0. preset system settings
set_time_limit(0);
ini_set('display_errors', true);


// 1. define constants
define('DS', DIRECTORY_SEPARATOR);
define('APP_PATH', dirname(realpath((__DIR__))) . DS);


// 2. register libs
require_once APP_PATH . DS . 'lib' . DS . 'hlpr' . DS . 'autoload.php';

$id3VendorPath = APP_PATH . DS . '..' . DS . 'vendor' . DS . 'james-heinrich' . DS . 'getid3' . DS . 'getid3';
require_once $id3VendorPath . DS . 'getid3.php';
require_once $id3VendorPath . DS . 'write.php';


// 3. upload app settings
require_once(APP_PATH . 'config' . DS . 'settings.php');


// 4. register app & test classes
require_once(realpath(__DIR__) . DS . 'autoload.php');
spl_autoload_register(array('autoloader', 'autoload'));
