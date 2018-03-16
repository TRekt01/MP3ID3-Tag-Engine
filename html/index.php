<?php
set_include_path (realpath(dirname(__FILE__) . '/../library'));

 // Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));


// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV',(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


// Typischerweise wird man das eigene library/ Verzeichnis zum include_path
// hinzufügen wollen, speziell wenn es die ZF Installation enthält
//set_include_path(implode(PATH_SEPARATOR, array(dirname(dirname(__FILE__)) . '/library/PEAR/', get_include_path(),)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');

$application->bootstrap()->run();

?>