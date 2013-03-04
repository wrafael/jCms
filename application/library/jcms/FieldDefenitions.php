<?php

namespace jcms;

/**
 * This class holds the information of all the custom fields in the content
 * table.
 * - $fields -> all the non system fields
 * - $alternateTypes -> Alternate behaviors the admin can set on a content field
 * - static getForm functions -> We need a function for each type_handler &
 * alternateType
 * that return a form element to handle the specific field/type.
 *
 * So if you want to add your own content field do the following:
 * 1) add the field to the Default_Model_Content class (at the bottom)
 * 2) add the dtb name to the FieldDefenitions::$fields array(DB_NAME =>
 * TYPE_HANDLER)
 * if the type handler is string getFormString wil be called to render the form
 * part of this field
 * 3) add a FieldDefenitions::getForm[dtb_field_name] that returns a Zend_Form
 * element to edit the field
 *
 * If you want to add your own type you need to do the following:
 * 1) add the type name to the FieldDefenitions::$alternateTypes array
 * 2) add a FieldDefenitions::getForm[Typehandler] that returns a Zend_Form
 * element to edit the fields
 * which have this type specified
 *
 * @author Jonathan van Rij - jonathan@vanrij.org
 *        
 */
class FieldDefenitions {
  
  // example of a metadata array
  // array
  // 'fieldName' => string 'url' (length=3)
  // 'type' => string 'string' (length=6)
  // 'length' => string '100' (length=3)
  // 'precision' => int 0
  // 'scale' => int 0
  // 'nullable' => boolean true
  // 'unique' => boolean true
  // 'label' => 'foo bar'
  // 'columnName' => string 'url' (length=3)
  
  // array of field_db_name's as key's and type_handler's as values for the
  // custom fields
  public static $fields = array (
      'content' => 'text',
      'integer1' => 'integer',
      'integer2' => 'integer',
      'integer3' => 'integer',
      'string1' => 'string',
      'string2' => 'string',
      'string3' => 'string',
      'datetime1' => 'datetime',
      'datetime2' => 'datetime',
      'datetime3' => 'datetime',
      'text1' => 'text' 
  );
  
  // public static $fields = array(
  // 'text'=>'content',
  // 'integer'=>'integer1',
  // 'integer'=>'integer2',
  // 'integer'=>'integer3',
  // 'string'=>'string1',
  // 'string'=>'string2',
  // 'string'=>'string3',
  // 'datetime'=>'datetime1',
  // 'datetime'=>'datetime2',
  // 'datetime'=>'datetime3',
  // );
  
  // array of field_db_name's as key's and type_handler's as values for the
  // system fields
  public static $systemfields = array (
      'code' => 'code',
      'title' => 'title',
      'url' => 'url' 
  );
  public static $alternateTypes = array (
      'tinymce' => 'text',
      'file' => 'string',
      'image' => 'string'
  );
  
  /**
   * Get's a form element for the form type string
   */
  public static function getFormString($metadata, $mode, $value = null) {
    $element = new \Zend_Form_Element_Text ( $metadata ['fieldName'] );
    $element->setValue ( $value );
    $element->setLabel ( $metadata ['label'] );
    $element->addDecorators ( FormsHelper::$formDecoratorText );
    return $element;
  }
  
  /**
   * Get's a form element for the form type string
   */
  public static function getFormTitle($metadata, $mode, $value = null) {
    $element = new \Zend_Form_Element_Text ( $metadata ['fieldName'] );
    $element->setValue ( $value );
    $element->setRequired ( true );
    
    $element->setLabel ( $metadata ['label'] );
    
//     $element->removeDecorator ( 'Errors' );
    $element->addErrorMessages ( array (
        'isEmpty' => tr ( 'ERROR_FORM_TITLE_ISEMPTY' ),
        'notEmptyInvalid' => tr ( 'ERROR_FORM_TITLE_ISINVALID_TXT' ) 
    ) );
    
    $element->addDecorators ( FormsHelper::$formDecoratorText );
    
    return $element;
  }
  public static function getFormUrl($metadata, $mode, $value = null) {
    $element = new \Zend_Form_Element_Text ( $metadata ['fieldName'] );
    $element->setValue ( $value );
    $element->setRequired ( ! $metadata ['nullable'] );
    $element->setLabel ( $metadata ['label'] );
    $element->addDecorators ( FormsHelper::$formDecoratorText );
    if ($mode == 'add')
      $element->addValidator ( new ValidatorUnique ( new \Default_Model_Content (), 'url', true ) );
    
//     $element->removeDecorator ( 'Errors' );
    $element->addErrorMessages ( array (
        'isEmpty' => tr ( 'ERROR_FORM_URL_ISEMPTY' ),
        'notEmptyInvalid' => tr ( 'ERROR_FORM_URL_ISINVALID_TXT' ),
        'NOT_UNIQUE' => tr('ERROR_FORM_URL_NOTUNIQUE')
    ) );
    
    return $element;
  }
  public static function getFormCode($metadata, $mode, $value = null) {
    $element = new \Zend_Form_Element_Text ( $metadata ['fieldName'] );
    $element->setValue ( $value );
    $element->setRequired ( ! $metadata ['nullable'] );
    $element->setLabel ( $metadata ['label'] );
    if ($mode == 'add')
      $element->addValidator ( new ValidatorUnique ( new \Default_Model_Content (), 'code' ) );
    $element->addDecorators ( FormsHelper::$formDecoratorText );
    
//     $element->removeDecorator ( 'Errors' );
    $element->addErrorMessages ( array (
        'isEmpty' => tr ( 'ERROR_FORM_CODE_ISEMPTY' ),
        'notEmptyInvalid' => tr ( 'ERROR_FORM_CODE_ISINVALID_TXT' ),
        'NOT_UNIQUE' => tr('ERROR_FORM_CODE_NOTUNIQUE')
    ) );
    
    return $element;
  }
  public static function getFormText($metadata, $mode, $value = null) {
    $element = new \Zend_Form_Element_Textarea ( $metadata ['fieldName'] );
    $element->setValue ( $value );
    $element->setRequired ( ! $metadata ['nullable'] );
    $element->setLabel ( $metadata ['label'] );
    $element->addDecorators ( FormsHelper::$formDecoratorText );
//     $element->removeDecorator ( 'Errors' );
    $element->addErrorMessages ( array (
        'isEmpty' => tr ( 'ERROR_FORM_TEXT_ISEMPTY' ),
        'notEmptyInvalid' => tr ( 'ERROR_FORM_TEXT_ISINVALID_TXT' ) 
    ) );
    return $element;
  }
  public static function getFormDatetime($metadata, $mode, $value = null) {
    $element = new \Zend_Form_Element_Text ( $metadata ['fieldName'] );
    $element->setValue ( $value );
    $element->setLabel ( $metadata ['label'] );
    $element->addDecorators ( FormsHelper::$formDecoratorDatetime );
    return $element;
  }
  public static function getFormTinymce($metadata, $mode, $value = null) {
    $element = new \Zend_Form_Element_Textarea ( $metadata ['fieldName'] );
    $element->setValue ( $value );
    $element->setAttrib ( 'class', 'tinymce' );
    $element->setRequired ( false );
    $element->setLabel ( $metadata ['label'] );
    $element->addDecorators ( FormsHelper::$formDecoratorText );
    return $element;
  }
  public static function getFormFile($metadata, $mode, $value = null) {
    $folder = getcwd () . DS . '..' . DS . \Zend_Registry::getInstance ()->settings ['jcms'] ['uploadfolder'];
    
    $element = new \Zend_Form_Element_File ( $metadata ['fieldName'] );
    $element->setLabel ( $metadata ['label'] );
    $element->setDestination ( $folder );
    $element->addValidator ( 'Count', false, 1 );
    $element->addValidator ( 'Size', false, 3024000 );
    // $element->addValidator('Extension', false, 'jpg,png,gif');
    $_fileElementDecorator = array (
        'File',
        array (
            array (
                'Value' => 'HtmlTag' 
            ),
            array (
                'tag' => 'td' 
            ) 
        ),
        'Errors',
        'Description',
        array (
            'Label',
            array (
                'tag' => 'th',
                'placement' => 'PREPENT' 
            ) 
        ),
        array (
            array (
                'Field' => 'HtmlTag' 
            ),
            array (
                'tag' => 'tr' 
            ) 
        ) 
    );
    
    // set default decorators
    $element->setDecorators ( $_fileElementDecorator );
    
    // set custom decorator to view the file
    // if we are in edit mode, we have a object_id and are able to show a
    // preview
    $controller = \Zend_Controller_Front::getInstance ();
    $params = array ();
    $params ['metadata'] = $metadata;
    $params ['folder'] = $folder;
    if (! is_null ( $controller->getRequest ()->getParam ( 'object_id', null ) )) {
      $params ['object_id'] = $controller->getRequest ()->getParam ( 'object_id', null );
    }
    
    $element->addDecorator ( new FileDecorator ( $params ) );
    
    return $element;
  }
  public static function getFormImage($metadata, $mode, $value = null) {
    $folder = getcwd () . DS . '..' . DS . \Zend_Registry::getInstance ()->settings ['jcms'] ['uploadfolder'];
    
    $element = new \Zend_Form_Element_File ( $metadata ['fieldName'] );
    $element->setLabel ( $metadata ['label'] );
    $element->setDestination ( $folder );
    $element->addValidator ( 'Count', false, 2 );
    $element->addValidator ( 'Size', false, 3024000 );
    // $element->addValidator('Extension', false, 'jpg,png,gif');
    $_fileElementDecorator = array (
        'File',
        array (
            array (
                'Value' => 'HtmlTag' 
            ),
            array (
                'tag' => 'td' 
            ) 
        ),
        'Errors',
        'Description',
        array (
            'Label',
            array (
                'tag' => 'th',
                'placement' => 'PREPENT' 
            ) 
        ),
        array (
            array (
                'Field' => 'HtmlTag' 
            ),
            array (
                'tag' => 'tr' 
            ) 
        ) 
    );
    
    // set default decorators
    $element->setDecorators ( $_fileElementDecorator );
    
    // set custom decorator to view the file
    // if we are in edit mode, we have a object_id and are able to show a
    // preview
    $controller = \Zend_Controller_Front::getInstance ();
    $params = array ();
    $params ['metadata'] = $metadata;
    $params ['folder'] = $folder;
    if (! is_null ( $controller->getRequest ()->getParam ( 'object_id', null ) )) {
      $params ['object_id'] = $controller->getRequest ()->getParam ( 'object_id', null );
    }
    
    $element->addDecorator ( new ImageDecorator ( $params ) );
    
    return $element;
  }
}

?>