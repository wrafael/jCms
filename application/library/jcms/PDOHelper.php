<?php
namespace jcms;

class PDOHelper {

  private static $connection;

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