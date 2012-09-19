<?php
namespace jcms;

class ValidatorUnique extends \Zend_Validate_Abstract {

  const NOT_UNIQUE = 'uniqueNotUnique';

  protected $_messageTemplates = array(
      self::NOT_UNIQUE => 'NOT_UNIQUE'
  );

  protected $table;

  protected $current;

  protected $column;

  public function __construct($table, $column, $currentId = null, $allowNull = false) {
    $this->table = $table;
    $this->column = $column;
    $this->current = $currentId;
    $this->allowNull = $allowNull;

    $this->_messageTemplates[self::NOT_UNIQUE] = tr('NOT_UNIQUE');

  }

  public function isValid($value) {
    if($this->allowNull && $value == '')
      return true;

    $class = get_class($this->table);

    if($this->current)
      $query = \Zend_Registry::getInstance()->entitymanager->createQuery("select r from {$class} r where r.{$this->column} = ?1 and r.id != ?2");
    if(! $this->current)
      $query = \Zend_Registry::getInstance()->entitymanager->createQuery("select r from {$class} r where r.{$this->column} = ?1");

    $query->setParameter(1,$value);
    if($this->current)
      $query->setParameter(2,$this->current);
    $result = $query->getResult();

    if(count($result) > 0){
      $this->_error(self::NOT_UNIQUE);
      return false;
    }

    return true;

  }
}