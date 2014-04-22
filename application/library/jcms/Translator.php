<?php
/**
 * This is ths jCms translator.
 */
namespace jcms;

class Translator {

  /**
   * Translates a string
   * 
   * @param string $code
   * @return string Translated
   */
  public static function tr($code) {
    $registry = \Zend_Registry::getInstance();
    $retrieved = $registry->translator->_($code);
    if($retrieved == $code || $retrieved == ''){
      $trans = \Default_Model_Translation::getInstanceByCode($code);
      if(! $trans){
        $t = new \Default_Model_Translation();
        $t->setCode($retrieved);
        $t->setCulture('nl_NL');
        \Zend_Registry::getInstance()->entitymanager->persist($t);
        \Zend_Registry::getInstance()->entitymanager->flush();
      }
    }
    if($retrieved != ''){
      return $retrieved;
    }else{
      $code;
    }
  }

  /**
   * Translates and outputs a string
   * 
   * This is a wrappper so you won't need to echo everything you translate in the templates.
   * 
   * @param string $code
   */
  public static function t($code) {
    echo self::tr($code);
  }
}

?>