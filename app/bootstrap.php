<?php

// system settings
set_time_limit(0);
ini_set('display_errors', true);

// constants
define('APP_PATH', realpath(__DIR__));
define('DS', DIRECTORY_SEPARATOR);

// autoloader
require_once('autoload.php');
spl_autoload_register(array('bhvAutoloader', 'autoload'));
