<?php


// 0. preset system settings
set_time_limit(0);
ini_set('display_errors', true);


// 1. define constants
define('DS', DIRECTORY_SEPARATOR);

define('PATH_APP', dirname(realpath((__DIR__))) . DS);
define('PATH_QA', dirname(PATH_APP) . DS . 'qa' . DS);
define('PATH_VENDOR', dirname(PATH_APP) . DS . 'vendor' . DS);

// 2. upload app settings
require_once(PATH_APP . 'config' . DS . 'settings.php');

// 3. register app & vendor classes
require_once(PATH_VENDOR . 'autoload.php');
require_once(realpath(__DIR__) . DS . 'autoload.php');
spl_autoload_register(array('autoloader', 'autoload'));
