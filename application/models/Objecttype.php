<?php

/**
 * @Entity
 * @Table(name="objecttype")
 */
use jcms\FieldDefenitions;

class Default_Model_Objecttype {

  /**
   * @Id @Column(type="integer")
   * @GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @Column(type="string")
   */
  private $name;

  /**
   * @Column(type="string")
   */
  private $code;

  /**
   * @Column(type="string")
   */
  private $description;

  /**
   * @Column(type="string")
   */
  private $icon;

  public function getId() {
    return $this->id;
  }

  public function setId($int) {
    $this->id = $int;
  }

  public function setName($string) {
    $this->name = $string;
    return true;
  }

  public function getName() {
    return $this->name;
  }

  public function setCode($string) {
    $this->code = $string;
    return true;
  }

  public function getCode() {
    return $this->code;
  }

  public function setDescription($string) {
    $this->description = $string;
    return true;
  }

  public function getDescription() {
    return $this->description;
  }

  public function setIcon($string) {
    $this->icon = $string;
    return true;
  }

  public function getIcon() {
    return $this->icon;
  }

  /**
   * Get's a Objecttype object based on the given Primairy Key
   *
   * @param Integer $id
   */
  public static function getInstanceByPk($id) {
    return jcms\ModelHelper::getInstanceByPkForModeltype($id, 'Default_Model_Objecttype');
  }

  /**
   * Get's all the objecttypefields of this objecttype
   */
  public function getFields() {
    $query = Zend_Registry::getInstance()->entitymanager->createQuery('select f from Default_Model_Objecttypefield f where f.objecttype_id = ?1 order by f.sort');
    $query->setParameter(1,$this->getId());
    $fields = $query->getResult();
    return $fields;
  }

  /**
   * Get's all the objecttypefields available
   */
  public static function getAllFields() {
    die('depricate this dude'); // user FieldDefenitions::$fields
    $query = Zend_Registry::getInstance()->entitymanager->createQuery('select f from Default_Model_Objecttypefield f group by f.db_name');
    $fields = $query->getResult();
    return $fields;
  }

  /**
   * Returns an array with 2 arrays, one with the used fields and one with the field names that can be used
   */
  public function getAddableFields(){
    $fields = $this->getFields(); // fields this objecttype uses

    $fieldObjectsArray = array();
    $fieldsArray = array();

    foreach($fields as $field){
      $fieldsArray[$field->getDbName()] = $field->getDbName();
    }
    foreach($fields as $field){
      $fieldObjectsArray[$field->getDbName()] = $field;
    }

    $fieldsUsed = array();
    $fieldsUsable = array();
    foreach(FieldDefenitions::$fields as $field=>$type){
      if(array_key_exists($field, $fieldsArray)){
        $fieldsUsed[$field] = $fieldObjectsArray[$field];
      }else{
        $fieldsUsable[$field] = $type;
      }
    }

    return array('used'=>$fieldsUsed,'usable'=>$fieldsUsable);
  }


  /**
   * Get's all the objecttypes
   */
  public static function getAllTypes() {
    $query = Zend_Registry::getInstance()->entitymanager->createQuery('select t from Default_Model_Objecttype t group by t.name');
    return $query->getResult();
  }

  /**
   * Saves object to dtb
   */
  public function save() {
    if(Zend_Registry::getInstance()->session->user->hasRole('god')){
      Zend_Registry::getInstance()->entitymanager->persist($this);
      Zend_Registry::getInstance()->entitymanager->flush();
    }
  }

  /**
   * Deletes the objecttype
   */
  public function delete() {
    if($this->canDelete()){
      if(Zend_Registry::getInstance()->session->user->hasRole('god')){
        $query = Zend_Registry::getInstance()->entitymanager->createQuery('delete from Default_Model_Objecttype o where o.id = ?1');
        $query->setParameter(1,$this->getId());
        return $query->execute();
      }
    }
  }

  /**
   * Determs if this objecttype can be deleted.
   */
  public function canDelete(){
    $query = Zend_Registry::getInstance()->entitymanager->createQuery('select c from Default_Model_Content c WHERE c.objecttype_id = ?1');
    $query->setParameter(1,$this->getId());
    $contents = $query->getResult();
    if(count($contents)){
      return false;
    }
    return true;
  }
}