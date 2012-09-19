<?php
namespace jcms;

class FileDecorator extends \Zend_Form_Decorator_Abstract implements \Zend_Form_Decorator_Marker_File_Interface {

  protected $_options = array();

  public function __construct($options = array()) {
    $this->_options = $options;
  }

  public function render($content) {

    if(isset($this->_options['object_id']) && $this->_options['object_id'] != ''){

      // get file info
      $content_object = \Default_Model_Content::getInstanceByPk($this->_options['object_id']);

      $field = \Default_Model_Objecttypefield::getInstanceByDBName($this->_options['metadata']['fieldName'], $content_object->getObjecttypeId());

      $getter = $field->getContentGetterString();
      $value = $content_object->$getter();

      $info = json_decode($value);

      if(!is_null($value)){
        return $content . '<tr><th></th><td>'.$info->name.'</td></tr>';
      }else{
        return $content;
      }
    }
    return $content;
  }
}