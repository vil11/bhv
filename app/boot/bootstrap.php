<?php


// preset system settings
set_time_limit(0);
ini_set('display_errors', true);


// define constants
define('DS', DIRECTORY_SEPARATOR);

define('PATH_APP', dirname(realpath((__DIR__))) . DS);
define('PATH_QA', dirname(PATH_APP) . DS . 'qa' . DS);
define('PATH_VENDOR', dirname(PATH_APP) . DS . 'vendor' . DS);

// vendor lib
$id3VendorPath = PATH_VENDOR . 'james-heinrich' . DS . 'getid3' . DS . 'getid3' . DS;
require_once $id3VendorPath . 'getid3.php';
require_once $id3VendorPath . 'write.php';

// upload app settings
require_once(PATH_APP . 'config' . DS . 'settings.php');

// register vendor & app classes
require_once(PATH_VENDOR . 'autoload.php');
require_once(realpath(__DIR__) . DS . 'autoload.php');
spl_autoload_register(array('autoloader', 'autoload'));
