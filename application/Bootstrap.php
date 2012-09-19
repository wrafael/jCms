<?php

use jcms\ErrorHelper;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

  /**
   * Register namespace Default_
   *
   * @return Zend_Application_Module_Autoloader
   */
  protected function _initAutoload() {
    $autoloader = new Zend_Application_Module_Autoloader(array(
        'namespace'=>'Default_',
        'basePath'=>dirname(__FILE__)
    ));
    return $autoloader;
  }

  protected function _initSiteModules() {
    // Don't forget to bootstrap the front controller as the resource may not
    // been created yet...
    $this->bootstrap("frontController");
    $front = $this->getResource("frontController");

    // Add modules dirs to the controllers for default routes...
    $front->addModuleDirectory(APPLICATION_PATH . '/modules');
  }

  /**
   * Initialize Doctrine
   *
   * @return Doctrine_Manager
   */
  public function _initDoctrine() {
    // include and register Doctrine's class loader
    require_once ('Doctrine' . DS . 'Common' . DS . 'ClassLoader.php');
    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine',APPLICATION_PATH . DS . 'library' . DS);
    $classLoader->register();

    // create the Doctrine configuration
    $config = new \Doctrine\ORM\Configuration();

    // setting the cache ( to ArrayCache. Take a look at
    // the Doctrine manual for different options ! )
    $cache = new \Doctrine\Common\Cache\ArrayCache();
    $config->setMetadataCacheImpl($cache);
    $config->setQueryCacheImpl($cache);

    // choosing the driver for our database schema
    // we'll use annotations
    $driver = $config->newDefaultAnnotationDriver(APPLICATION_PATH . DS . 'models');
    $config->setMetadataDriverImpl($driver);

    // set the proxy dir and set some options
    $config->setProxyDir(APPLICATION_PATH . DS . 'models' . DS . 'Proxies');
    $config->setAutoGenerateProxyClasses(true);
    $config->setProxyNamespace('App\Proxies');

    // now create the entity manager and use the connection
    // settings we defined in our application.ini
    $connectionSettings = $this->getOption('doctrine');
    $conn = array(
        'driver'=>$connectionSettings['conn']['driv'],
        'user'=>$connectionSettings['conn']['user'],
        'password'=>$connectionSettings['conn']['pass'],
        'dbname'=>$connectionSettings['conn']['dbname'],
        'host'=>$connectionSettings['conn']['host']
    );
    $entityManager = \Doctrine\ORM\EntityManager::create($conn,$config);

    // push the entity manager into our registry for later use
    $registry = Zend_Registry::getInstance();
    $registry->entitymanager = $entityManager;

    return $entityManager;
  }

  public function _initCache() {
    // front end options, cache for 1 minute
    $frontendOptions = array(
        'lifetime'=>1,
        'automatic_serialization'=>true
    );
    // backend options
    $backendOptions = array(
        'cache_dir'=>PATH . DS . 'application' . DS . 'variable' . DS . 'cache',
        'automatic_serialization'=>true
    );
    // make cache object
    $cache = Zend_Cache::factory('Output','File',$frontendOptions,$backendOptions);

    $registry = Zend_Registry::getInstance();
    $registry->cache = $cache;
  }

  /**
   * Get's the corrensponding content object by a given unknow request uri and
   * adds that route to the Zend Router
   */
  public function _initRouting() {
    $router = Zend_Controller_Front::getInstance()->getRouter();

    $defaultUrls = array(
        '/',
        '/account/login',
        '/account/logout',
        '/account/register',
        '/account/success',
        '/account/index'
    );

    if((bool)! strstr($_SERVER["REQUEST_URI"],'/file') && (bool)! strstr($_SERVER["REQUEST_URI"],'/backend') && ! in_array($_SERVER["REQUEST_URI"],$defaultUrls)){

      $info = jcms\Route::getRouteInfoByUrl($_SERVER["REQUEST_URI"]);

      if($info == false){
        header('location: /');
        exit;
      }

      $router->addRoute('user',new Zend_Controller_Router_Route($_SERVER["REQUEST_URI"],array(
          'controller'=>'index',
          'action'=>$info['objecttypecode'],
          'id'=>$info['id']
      )));
    }

  }
}

/**
 * Echo's the given translated string
 *
 * @param string $val
 */
function t($val) {
  jcms\Translator::t($val);
}

/**
 * Return's the given translated string
 *
 * @param string $val
 */
function tr($val) {
  return jcms\Translator::tr($val);
}