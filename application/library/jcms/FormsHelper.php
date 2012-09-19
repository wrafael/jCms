<?php
namespace jcms;

/**
 * A helper class that generates forms
 *
 * @author Jonathan van Rij - jonathan@vanrij.org
 *
 */
class FormsHelper extends FieldDefenitions {

//   public static $errorMessages = array(
//       'isEmpty'=>'is niet ingevuld',
//       'uniqueNotUnique'=>'moet een unieke waarde bevatten',
//       'typoCheck'=>' is verkeerd getyped'
//   );

  public static $formDecoratorForm = array(
      'FormElements',
      array(
          'HtmlTag',
          array(
              'tag'=>'table',
          )
      ),
      'Form'
  );

  public static $formDecoratorCheckbox = array(
      'ViewHelper',
      'Errors',
      'Description',
      array(
          'HtmlTag',
          array(
              'tag'=>'td'
          )
      ),
      array(
          'Label',
          array(
              'tag'=>'td',
              'class'=>'element'
          )
      ),
      array(
          array(
              'row'=>'HtmlTag'
          ),
          array(
              'tag'=>'tr'
          )
      )
  );

  public static $formDecoratorMultiselect = array(
      'ViewHelper',
      'Errors',
      'Description',
      array(
          'HtmlTag',
          array(
              'tag'=>'td'
          )
      ),
      array(
          'Label',
          array(
              'tag'=>'td',
              'class'=>'element'
          )
      ),
      array(
          array(
              'row'=>'HtmlTag'
          ),
          array(
              'tag'=>'tr'
          )
      )
  );

  public static $formDecoratorText = array(
      'ViewHelper',
      'Errors',
      'Description',
      array(
          'HtmlTag',
          array(
              'tag'=>'td'
          )
      ),
      array(
          'Label',
          array(
              'tag'=>'th'
          )
      ),
      array(
          array(
              'row'=>'HtmlTag'
          ),
          array(
              'tag'=>'tr'
          )
      )
  );

  public static $formDecoratorDatetime = array(
      'ViewHelper',
      'Errors',
      'Description',
      array(
          'HtmlTag',
          array(
              'tag'=>'td'
          )
      ),
      array(
          'Label',
          array(
              'tag'=>'th'
          )
      ),
      array(
          array(
              'row'=>'HtmlTag'
          ),
          array(
              'tag'=>'tr',
              'class'=>'datepicker'
          )
      )
  );

  public static $formDecoratorButton = array(
      'ViewHelper',
      array(
          array(
              'data'=>'HtmlTag'
          ),
          array(
              'tag'=>'td',
              'class'=>'form-submit'
          )
      ),
      array(
          array(
              'label'=>'HtmlTag'
          ),
          array(
              'tag'=>'td',
              'placement'=>'prepend'
          )
      ),
      array(
          array(
              'row'=>'HtmlTag'
          ),
          array(
              'tag'=>'tr'
          )
      )
  );

  public static $formDecoratorHidden = array(
      'ViewHelper',
      array(
          array(
              'data'=>'HtmlTag'
          ),
          array(
              'tag'=>'td',
              'class'=>'hidden'
          )
      ),
      array(
          array(
              'label'=>'HtmlTag'
          ),
          array(
              'tag'=>'td',
              'placement'=>'prepend'
          )
      ),
      array(
          array(
              'row'=>'HtmlTag'
          ),
          array(
              'tag'=>'tr',
              'class'=>'hidden'
          )
      )
  );

  /**
   * Get's a objecttype selector form for selecting the type of object to add
   * on a parent
   *
   * @param Integer $parent
   */
  public static function getObjecttypeSelectorForm($parent) {

    $form = new \Zend_Form();
    $form->setAction('/backend/iframe/contentedit')->setMethod('post');
    $form->setAttrib('id','content');
    $form->setAttrib('class','hidden');

    $parentElement = new \Zend_Form_Element_Hidden('parent_id');
    $parentElement->setValue($parent);
    $parentElement->setAttrib('class','hidden');
    $parentElement->addDecorators(self::$formDecoratorHidden);

    $modeElement = new \Zend_Form_Element_Hidden('mode');
    $modeElement->setValue('add');
    $modeElement->setAttrib('class','hidden');
    $modeElement->addDecorators(self::$formDecoratorHidden);

    $objecttypeElement = new \Zend_Form_Element_Hidden('objecttype_id');
    $objecttypeElement->setAttrib('class','hidden');
    $objecttypeElement->addDecorators(self::$formDecoratorHidden);

    $saveElement = new \Zend_Form_Element_Hidden('save');
    $saveElement->setValue(false);
    $saveElement->setAttrib('class','hidden');
    $saveElement->addDecorators(self::$formDecoratorHidden);

    $form->addElement($saveElement);
    $form->addElement($objecttypeElement);
    $form->addElement($parentElement);
    $form->addElement($modeElement);

    $form->addDecorators(self::$formDecoratorForm);

    return $form;
  }

  /**
   * Gives a form to upload multiple files
   *
   * @param Integer $parent
   */
//   public static function getMultipleFileUploadForm($action) {

//     $form = new \Zend_Form();
//     $form->setAction($action)->setMethod('post');

//     $files = new \Zend_Form_Element_File('images', array('label' => 'Images:', 'multiple' => 'multiple'));
//     $files->setIsArray(true);

//     $form->addElement($files);

//     $submit = new \Zend_Form_Element_Submit('submit');
//     $submit->setLabel(tr('SAVE'));
//     $submit->setDecorators(self::$formDecoratorButton);

//     $form->addElement($submit);

//     $form->addDecorators(self::$formDecoratorForm);

//     return $form;
//   }

  /**
   * Generates a form to handle a Content object
   *
   * @param jcms\Default_Model_Content $content
   * @param String $action
   * @param String $mode
   */
  public static function getContentForm(\Default_Model_Content $content, $mode = 'add', $action = '#') {

    $form = new \Zend_Form();

    $objecttype = $content->getObjecttype();

    $elements = array();

    $parentElement = new \Zend_Form_Element_Hidden('parent_id');
    $parentElement->setValue($content->getParent());
    $parentElement->addDecorators(FormsHelper::$formDecoratorHidden);
    $elements[] = $parentElement;

    $modeElement = new \Zend_Form_Element_Hidden('mode');
    $modeElement->setValue($mode);
    $modeElement->addDecorators(FormsHelper::$formDecoratorHidden);
    $elements[] = $modeElement;

    $objecttypeElement = new \Zend_Form_Element_Hidden('objecttype_id');
    $objecttypeElement->setValue($objecttype->getId());
    $objecttypeElement->addDecorators(FormsHelper::$formDecoratorHidden);
    $elements[] = $objecttypeElement;

    $objectid = new \Zend_Form_Element_Hidden('object_id');
    $objectid->setValue($content->getId());
    $objectid->addDecorators(FormsHelper::$formDecoratorHidden);
    $elements[] = $objectid;

    foreach($objecttype->getFields() as $objecttypefield){
      $metadata = $objecttypefield->getMetaData();
      $metadata['label'] = $objecttypefield->getLabel();

      $getContentGetter = $objecttypefield->getContentGetterString();

      $getFormGetter = $objecttypefield->getFormGetterString();
      switch ($mode) {
        case 'edit' :
          $elements[] = parent::$getFormGetter($metadata,$mode,$content->$getContentGetter(), $content);
//           if('getFormFile' == $getFormGetter || 'getFormImage' == $getFormGetter){
//             $form->setAttrib('enctype', 'multipart/form-data');
//           }
          break;
        case 'add' :
          $elements[] = parent::$getFormGetter($metadata,$mode, null, $content);
          break;
        default : // view is the default
                  // echo '<p>'.$metadata['label'].':
                  // '.$content->$getContentGetter().'<p />';
          break;
      }
    }

    $submit = new \Zend_Form_Element_Submit('submit');
    $submit->setLabel(tr('SAVE'));
    $submit->setName('save_content');
    $submit->setDecorators(self::$formDecoratorButton);

    $elements[] = $submit;

    $form->setAction($action)->setMethod('post');
    $form->setAttrib('id','content_form');

    $form->addDecorators(self::$formDecoratorForm);

    foreach($elements as $element){
      $form->addElement($element);
    }

    return $form;
  }


  /**
   * Get's a login form
   *
   * @param \Zend_Controller_Request_Abstract $request
   */
  public static function getLoginForm(\Zend_Controller_Request_Abstract $request) {

    $checkboxDecorator = array(
        'ViewHelper',
        'Errors',
        'Description',
        array(
            'HtmlTag',
            array(
                'tag'=>'td'
            )
        ),
        array(
            'Label',
            array(
                'tag'=>'td',
                'class'=>'element'
            )
        ),
        array(
            array(
                'row'=>'HtmlTag'
            ),
            array(
                'tag'=>'tr'
            )
        )
    );
    $elementDecorators = array(
        'ViewHelper',
        'Errors',
        'Description',
        array(
            'HtmlTag',
            array(
                'tag'=>'td'
            )
        ),
        array(
            'Label',
            array(
                'tag'=>'th',
                'class'=>'element'
            )
        ),
        array(
            array(
                'row'=>'HtmlTag'
            ),
            array(
                'tag'=>'tr'
            )
        )
    );
    $buttonDecorators = array(
        'ViewHelper',
        array(
            'HtmlTag',
            array(
                'tag'=>'td'
            )
        ),
        array(
            'Label',
            array(
                'tag'=>'th'
            )
        ),
        array(
            array(
                'row'=>'HtmlTag'
            ),
            array(
                'tag'=>'tr'
            )
        )
    );

    $form = new \Zend_Form();
    $form->setAction('/backend/index/login')->setMethod('post');
    $form->setAttrib('id','login');

    $fieldset = new \Zend_Form_Decorator_Fieldset();
    $fieldset->setLegend(Auth::getAuthString());

    $username = new \Zend_Form_Element_Text('username');
    $username->setRequired(true);
    $username->addFilter('StringtoLower');
    $username->addFilter('StringTrim');
    $username->setLabel(tr('USERNAME'));
    $username->setAttrib('class','login-inp');
    $username->setDecorators($elementDecorators);

    $password = new \Zend_Form_Element_Password('password');
    $password->setRequired(true);
    $password->addFilter('StringTrim');
    $password->addFilter('StringtoLower');
    $password->setLabel(tr('PASSWORD'));
    $password->setAttrib('class','login-inp');
    $password->setDecorators($elementDecorators);

    $submit = new \Zend_Form_Element_Submit('submit');
    $submit->setLabel(tr('LOGIN'));
    $submit->setAttrib('class','submit-login');
    $submit->setDecorators(array(
        'ViewHelper',
        array(
            array(
                'data'=>'HtmlTag'
            ),
            array(
                'tag'=>'td',
                'class'=>'element'
            )
        ),
        array(
            array(
                'label'=>'HtmlTag'
            ),
            array(
                'tag'=>'td',
                'placement'=>'prepend'
            )
        ),
        array(
            array(
                'row'=>'HtmlTag'
            ),
            array(
                'tag'=>'tr'
            )
        )
    ));

    $form->addDecorator($fieldset);
    $form->addElement($username);
    $form->addElement($password);
    $form->addElement($submit);

    $form->setDecorators(array(
        'FormElements',
        array(
            'HtmlTag',
            array(
                'tag'=>'table'
            )
        ),
        'Form'
    ));

    return $form;
  }

  /**
   * Get's a logout form
   *
   * @param \Zend_Controller_Request_Abstract $request
   * @return \Zend_Form
   */
  public static function getLogoutForm(\Zend_Controller_Request_Abstract $request) {
    $form = new \Zend_Form();
    $form->setAction('/backend/index/logout')->setMethod('post');
    $form->setAttrib('id','logout');

    $submit = new \Zend_Form_Element_Submit('submit');
    $submit->setLabel(tr('LOGOUT'));

    $form->addElement($submit);

    return $form;
  }

  /**
   * Get's a register form by modifying the UserForm
   *
   * @return \Zend_Form
   */
  public static function getRegisterForm($action) {
    $form = new \Zend_Form();
    $form->setAction($action)->setMethod('post');
    $form->setAttrib('id','user');
    $form->setDecorators(self::$formDecoratorForm);

    $username = new \Zend_Form_Element_Text('username');
    $username->setRequired(true);
    $username->addFilter('StringtoLower');
    $username->addFilter('StringTrim');
    $username->setLabel(tr('USERNAME'));
    $username->setDecorators(self::$formDecoratorText);
    $username->addValidator(new ValidatorUnique(new \Default_Model_User(),'username'));
    $form->addElement($username);

    $password = new \Zend_Form_Element_Password('password');
    $password->setRequired(true);
    $password->addFilter('StringTrim');
    $password->addFilter('StringtoLower');
    $password->setLabel(tr('PASSWORD'));
    $password->addValidator(new ValidatorPassword('password','password_repeat'));
    $password->setDecorators(self::$formDecoratorText);
    $form->addElement($password);

    $passwordRepeat = new \Zend_Form_Element_Password('password_repeat');
    $passwordRepeat->addFilter('StringTrim');
    $passwordRepeat->addFilter('StringtoLower');
    $passwordRepeat->setLabel(tr('PASSWORD_REPEAT'));
    $passwordRepeat->addValidator(new ValidatorPassword('password','password_repeat'));
    $passwordRepeat->setDecorators(self::$formDecoratorText);
    $form->addElement($passwordRepeat);

    $email = new \Zend_Form_Element_Text('email');
    $email->setRequired(true);
    $email->addFilter('StringtoLower');
    $email->addFilter('StringTrim');
    $email->setLabel(tr('EMAIL'));
    $email->addValidator(new \Zend_Validate_EmailAddress());
    $email->setDecorators(self::$formDecoratorText);
    $form->addElement($email);

    $note = new \Zend_Form_Element_Textarea('note');
    $note->setRequired(true);
    $note->setLabel(tr('ACCOUNT_REASON'));
    $note->setDecorators(self::$formDecoratorText);
    $form->addElement($note);

    $submit = new \Zend_Form_Element_Submit('submit');
    $submit->setLabel(tr('SAVE'));
    $submit->setName('save_user');
    $submit->setDecorators(self::$formDecoratorButton);
    $form->addElement($submit);

    return $form;
  }

  public static function getFormUser(\Default_Model_User $user = null, $remove = false) {

    $form = new \Zend_Form();
    $form->setAction('/backend/index/useredit')->setMethod('post');
    $form->setAttrib('id','user');
    $form->setDecorators(self::$formDecoratorForm);

    if($user){
      $id = new \Zend_Form_Element_Hidden('id');
      $id->setValue($user->getId());
      $id->addDecorators(FormsHelper::$formDecoratorHidden);
      $form->addElement($id);
    }

    $username = new \Zend_Form_Element_Text('username');
    $username->setRequired(true);
    if($user)
      $username->setValue($user->getUsername());
    $username->addFilter('StringtoLower');
    $username->addFilter('StringTrim');
    $username->setLabel(tr('USERNAME'));
    $username->setDecorators(self::$formDecoratorText);
    if($user)
      $username->addValidator(new ValidatorUnique(new \Default_Model_User(),'username',$user->getId()));
    if(! $user)
      $username->addValidator(new ValidatorUnique(new \Default_Model_User(),'username'));
    $form->addElement($username);

    $checkedRoles = array();
    if($user){
      foreach($user->getRoles() as $role){
        $checkedRoles[] = $role->getId();
      }
    }
    $roles = new \Zend_Form_Element_MultiCheckbox('roles');
    foreach(\Default_Model_Role::getAllRoles() as $role){
      $roles->addMultiOption($role->getId(),$role->getName());
    }
    $roles->setValue($checkedRoles);
    $roles->setDecorators(self::$formDecoratorMultiselect);
    $form->addElement($roles);

    $password = new \Zend_Form_Element_Password('password');
    if(! $user || $user->getId() == null)
    $password->setRequired(true); //
    $password->addFilter('StringTrim');
    $password->addFilter('StringtoLower');
    $password->setLabel(tr('PASSWORD'));
    $password->addValidator(new ValidatorPassword('password','password_repeat'));
    $password->setDecorators(self::$formDecoratorText);
    $form->addElement($password);

    $passwordRepeat = new \Zend_Form_Element_Password('password_repeat');
    $passwordRepeat->addFilter('StringTrim');
    $passwordRepeat->addFilter('StringtoLower');
    $passwordRepeat->setLabel(tr('PASSWORD_REPEAT'));
    $passwordRepeat->addValidator(new ValidatorPassword('password','password_repeat'));
    $passwordRepeat->setDecorators(self::$formDecoratorText);
    $form->addElement($passwordRepeat);

    $email = new \Zend_Form_Element_Text('email');
    $email->setRequired(true);
    $email->addFilter('StringtoLower');
    $email->addFilter('StringTrim');
    $email->setLabel(tr('EMAIL'));
    if($user)
      $email->setValue($user->getEmail());
    $email->addValidator(new \Zend_Validate_EmailAddress());
    $email->setDecorators(self::$formDecoratorText);
    $form->addElement($email);

    $note = new \Zend_Form_Element_Textarea('note');
    $note->setRequired(true);
    $note->setLabel(tr('ACCOUNT_NOTE'));
    $note->setDecorators(self::$formDecoratorText);
    if($user)
      $note->setValue($user->getNote());
    $form->addElement($note);

    $active = new \Zend_Form_Element_Checkbox('active');
    $active->setLabel(tr('ACTIVE'));
    $active->setDecorators(self::$formDecoratorText);
    if($user && $user->getActive())
      $active->setChecked(true);
    $form->addElement($active);

    $submit = new \Zend_Form_Element_Submit('submit');
    $submit->setLabel(tr('SAVE'));
    $submit->setName('save_user');
    $submit->setDecorators(self::$formDecoratorButton);
    $form->addElement($submit);

    if($remove){
      $remove = new \Zend_Form_Element_Submit('remove');
      $remove->setLabel(tr('REMOVE'));
      $remove->setName('delete_user');
      $remove->setValue('delete');
      $remove->setDecorators(self::$formDecoratorButton);
      $form->addElement($remove);
    }

    return $form;
  }

  /**
   * Get's a form to add or edit a role
   *
   * @param \Default_Model_Role $role
   */
  public static function getFormRole(\Default_Model_Role $role = null, $remove = false) {

    $form = new \Zend_Form();
    $form->setAction('/backend/index/roleedit')->setMethod('post');
    $form->setAttrib('id','role');
    $form->setDecorators(self::$formDecoratorForm);

    $id = new \Zend_Form_Element_Hidden('id');
    if($role)
      $id->setValue($role->getId());
    $id->addDecorators(FormsHelper::$formDecoratorHidden);
    $form->addElement($id);

    $name = new \Zend_Form_Element_Text('name');
    $name->setRequired(true);
    $name->addFilter('StringtoLower');
    $name->setLabel(tr('NAME'));
    if($role)
      $name->setValue($role->getName());
    $name->setDecorators(self::$formDecoratorText);
    $form->addElement($name);

    $submit = new \Zend_Form_Element_Submit('save');
    $submit->setLabel(tr('SAVE'));
    $submit->setName('save_role');
    $submit->setValue('edit');
    $submit->setDecorators(self::$formDecoratorButton);
    $form->addElement($submit);

    if($remove && $role && $role->canDelete()){
      $remove = new \Zend_Form_Element_Submit('remove');
      $remove->setLabel(tr('REMOVE'));
      $remove->setName('delete_role');
      $remove->setValue('delete');
      $remove->setDecorators(self::$formDecoratorButton);
      $form->addElement($remove);
    }

    return $form;
  }

  /**
   * Get's a form to add or edit a role
   *
   * @param \Default_Model_Role $role
   */
  public static function getFormObjecttype(\Default_Model_Objecttype $objecttype = null, $remove = false) {

    $form = new \Zend_Form();
    $form->setAction('/backend/index/objecttypeedit')->setMethod('post');
    $form->setAttrib('id','objecttype');
    $form->setDecorators(self::$formDecoratorForm);

    $id = new \Zend_Form_Element_Hidden('id');
    if($objecttype)
      $id->setValue($objecttype->getId());
    $id->addDecorators(FormsHelper::$formDecoratorHidden);
    $form->addElement($id);

    $name = new \Zend_Form_Element_Text('name');
    $name->setRequired(true);
    $name->setLabel(tr('TITLE'));
    if($objecttype)
      $name->setValue($objecttype->getName());
    $name->setDecorators(self::$formDecoratorText);
    $form->addElement($name);

    $code = new \Zend_Form_Element_Text('code');
    $code->setRequired(true);
    $code->setLabel(tr('CODE'));
    if($objecttype)
      $code->setValue($objecttype->getCode());
    $code->setDecorators(self::$formDecoratorText);
    $form->addElement($code);

    $icon = new \Zend_Form_Element_Select('icon');
    $map = getcwd().DS.'img'.DS.'backend'.DS.'icons';
    $noFiles = array('.','..');
    if ($handle = opendir($map)) {
      while (false !== ($entry = readdir($handle))) {
        if(!in_array($entry, $noFiles)){
          $icon->addMultiOption($entry, str_replace('.png','',$entry));
        }
      }
      closedir($handle);
    }
    if($objecttype) $icon->setValue($objecttype->getIcon());
    $icon->setRequired(true);
    $icon->setLabel(tr('ICON'));
    $icon->setDecorators(self::$formDecoratorText);
    $form->addElement($icon);

    $description = new \Zend_Form_Element_Textarea('description');
    $description->setRequired(true);
    $description->setLabel(tr('DESCRIPTION'));
    if($objecttype)
      $description->setValue($objecttype->getDescription());
    $description->setDecorators(self::$formDecoratorText);
    $form->addElement($description);

    $submit = new \Zend_Form_Element_Submit('save');
    $submit->setLabel(tr('SAVE'));
    $submit->setName('save_objecttype');
    $submit->setValue('edit');
    $submit->setDecorators(self::$formDecoratorButton);
    $form->addElement($submit);
    if($remove){
      $remove = new \Zend_Form_Element_Submit('remove');
      $remove->setLabel(tr('REMOVE'));
      $remove->setName('delete_objecttype');
      $remove->setValue('delete');
      $remove->setDecorators(self::$formDecoratorButton);
      $form->addElement($remove);
    }
    return $form;
  }

  public static function getPermissionsForm(\Default_Model_Role $role) {
    $f = '';
    $objecttypes = \Default_Model_Objecttype::getAllTypes();

    $permissions = \Default_Model_Permission::getPermissionsForRole($role);

    $f .= '<form method="post" action="/backend/index/permissionsedit" id="persmissions" >';
    $f .= '<input type="hidden" value="' . $role->getId() . '" name="id" />';
    $f .= '<table id="permissions_table" class="minimal">';
    $f .= '<thead>';
    $f .= '<tr><th>&nbsp;</th><th>'.tr('TYPE_OF_CONTENT').'</th><th>'.tr('READ_RIGHTS').'</th><th>'.tr('CHANGE_RIGHTS').'</th><th>'.tr('DELETE_ADD_RIGHTS').'</th><th>'.tr('ADDCHILD_RIGHTS').'</th><th>'.tr('MOVE_RIGHTS').'</th></tr>';
    $f .= '</thead>';
    $f .= '<tbody>';
    $selected = ' checked="checked" ';
    foreach($objecttypes as $type){
      $name = $type->getName();
      $f .= '<tr>';
      $f .= '<td>';
      $f .= '<img class="icon" src="/img/backend/icons/'.$type->getIcon().'" alt="'.$type->getName().'" />';
      $f .= '</td>';
      $f .= '<td>';
      $f .= $type->getName();
      $f .= '</td>';
      $f .= '<td>';
      $pRead = (isset($permissions[$type->getId()][\Default_Model_Permission::PERMISSION_READ])) ? $selected : '';
      $f .= '<input type="checkbox" name="permissions[]" value="permission_' . $type->getId() . '_' . \Default_Model_Permission::PERMISSION_READ . '" ' . $pRead . '>';
      $f .= '</td>';
      $f .= '<td>';
      $pEdit = (isset($permissions[$type->getId()][\Default_Model_Permission::PERMISSION_EDIT])) ? $selected : '';
      $f .= '<input type="checkbox" name="permissions[]" value="permission_' . $type->getId() . '_' . \Default_Model_Permission::PERMISSION_EDIT . '" ' . $pEdit . '>';
      $f .= '</td>';
      $f .= '<td>';
      $pExecute = (isset($permissions[$type->getId()][\Default_Model_Permission::PERMISSION_EXECUTE])) ? $selected : '';
      $f .= '<input type="checkbox" name="permissions[]" value="permission_' . $type->getId() . '_' . \Default_Model_Permission::PERMISSION_EXECUTE . '" ' . $pExecute . '>';
      $f .= '</td>';
      $f .= '<td>';
      $pExecute = (isset($permissions[$type->getId()][\Default_Model_Permission::PERMISSION_ADDCHILD])) ? $selected : '';
      $f .= '<input type="checkbox" name="permissions[]" value="permission_' . $type->getId() . '_' . \Default_Model_Permission::PERMISSION_ADDCHILD . '" ' . $pExecute . '>';
      $f .= '</td>';
      $f .= '<td>';
      $pExecute = (isset($permissions[$type->getId()][\Default_Model_Permission::PERMISSION_MOVE])) ? $selected : '';
      $f .= '<input type="checkbox" name="permissions[]" value="permission_' . $type->getId() . '_' . \Default_Model_Permission::PERMISSION_MOVE . '" ' . $pExecute . '>';
      $f .= '</td>';
      $f .= '</tr>';
    }
    $f .= '</tbody>';
    $f .= '</table>';
    $f .= '<input type="submit" value="'.tr('SAVE_RIGHTS').'" />';
    $f .= '</form>';
    return $f;
  }
}

?>