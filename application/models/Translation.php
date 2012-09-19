<?php

/**
 * @Entity
 * @Table(name="translation")
 */
class Default_Model_Translation {

  /**
   * @Id @Column(type="integer")
   * @GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @Column(type="string")
   */
  private $culture;

  /**
   * @Column(type="string")
   */
  private $translation;

  /**
   * @Column(type="string")
   */
  private $code;

  public function setCulture($string) {
    $this->culture = $string;
    return true;
  }

  public function getCulture() {
    return $this->culture;
  }

  public function setCode($string) {
    $this->code = $string;
    return true;
  }

  public function getCode() {
    return $this->code;
  }

  public function setTranslation($string) {
    $this->translation = $string;
    return true;
  }

  public function getTranslation() {
    return $this->translation;
  }

  /**
   * Get's a Translation object based on the given Code & Culture
   *
   * @param $code String
   */
  public static function getInstanceByCode($code, $culture = 'nl_NL') {
    $query = Zend_Registry::getInstance ()->entitymanager->createQuery ( 'select t from Default_Model_Translation t WHERE t.code = ?1 and t.culture = ?2' );
    $query->setParameter ( 1, $code );
    $query->setParameter ( 2, $culture );
    $translations = $query->execute();
    if($translations){
      return $translations[0];
    }
    return false;
  }
}