<?php

error_reporting(E_ALL);
ini_set('display_errors','1');

define('DS',DIRECTORY_SEPARATOR);
define('PATH',dirname(__FILE__));
define('APPLICATION_PATH',PATH . DS . 'application');
define('APPLICATION_ENV','development');

// // Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR,array(
    realpath(PATH . DS . 'application' . DS . 'library'),
    realpath(PATH . DS . 'application'),
    get_include_path()
)));

require_once ('Zend' . DS . 'Application.php');
// Doctrine and Symfony Classes
require_once 'Doctrine/Common/ClassLoader.php';
$classLoader = new \Doctrine\Common\ClassLoader('Doctrine',APPLICATION_PATH . DS . 'library');
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Symfony',APPLICATION_PATH . DS . 'library/Doctrine');
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Entities',APPLICATION_PATH . DS . 'models');
$classLoader->setNamespaceSeparator('_');
$classLoader->register();

$application = new Zend_Application(APPLICATION_ENV,APPLICATION_PATH . '/configs/application.ini');

$application->getBootstrap()->bootstrap('doctrine');

$application->bootstrap()->run();
?>