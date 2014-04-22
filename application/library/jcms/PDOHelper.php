<?php
/**
 * PDO helper for jCms.
 * 
 * Bridge between PDO and jCms configuration
 */
namespace jcms;

class PDOHelper {

  private static $connection;

  /**
   * Builds a connection with PDO based on the jCms configuration settings
   * 
   * @return PDO
   */
  public static function getConnection()
  {
    if (!isset(self::$connection)) {
      $host = \Zend_Registry::getInstance()->settings['doctrine']['conn']['host'];
      $name = \Zend_Registry::getInstance()->settings['doctrine']['conn']['dbname'];
      $user = \Zend_Registry::getInstance()->settings['doctrine']['conn']['user'];
      $pass = \Zend_Registry::getInstance()->settings['doctrine']['conn']['pass'];
      $driv = \Zend_Registry::getInstance()->settings['doctrine']['conn']['driv'];

      $dsn = "mysql:dbname={$name};host={$host}";

      self::$connection = new \PDO($dsn, $user, $pass);
    }
    return self::$connection;
  }

}

?>