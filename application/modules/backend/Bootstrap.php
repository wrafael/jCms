<?php

/**
 * Module's bootstrap file.
 * Notice the bootstrap class' name is "Modulename_"Bootstrap.
 * When creating your own modules make sure that you are using the correct namespace
 */
class Backend_Bootstrap extends Zend_Application_Module_Bootstrap {

  protected function _initTranslation() {
    $culture = 'nl_NL';
    $translationsArray = array();
    $query = Zend_Registry::getInstance()->entitymanager->createQuery("select t from Default_Model_Translation t where t.culture = '{$culture}'");
    $translations = $query->getResult();
    foreach($translations as $translation){
      $translationsArray[$translation->getCode()] = $translation->getTranslation();
    }

    $translate = new Zend_Translate(array(
        'adapter'=>'array',
        'content'=>$translationsArray,
        'locale'=>$culture
    ));

    Zend_Validate_Abstract::setDefaultTranslator($translate);

    $registry = Zend_Registry::getInstance();
    $registry->translator = $translate;
  }
}
