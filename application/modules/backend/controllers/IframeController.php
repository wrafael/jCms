<?php

class Backend_IframeController extends Zend_Controller_Action {

  public function init() {
    Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session(jcms\Auth::SESSION_BACKEND_USER));

    $register = Zend_Registry::getInstance();
    $register->settings = $this->getInvokeArg('bootstrap')->getOptions();
    $register->request = $this->getRequest();
    $register->session = new \Zend_Session_Namespace(jcms\Auth::SESSION_BACKEND_USER);
    $this->em = $register->entitymanager;
    $this->cache = $register->cache;
    $this->layout = Zend_Layout::getMvcInstance();

    $this->layout->setLayout('backend_iframe');

    // check if the user is logged in, if not, redirect the user to the
    // login page
    if(! Zend_Auth::getInstance()->hasIdentity()){
      throw new Zend_Exception(jcms\Translator::tr('NOT_LOGGED_IN'));
    }

    $userSession = new \Zend_Session_Namespace(jcms\Auth::SESSION_BACKEND_USER);
    $this->user = $userSession->object;
  }

  public function contenteditAction() {

    $info = array();

    $objecttypeId = $this->getRequest()->getParam('objecttype_id',null);

    $parentId = $this->getRequest()->getParam('parent_id',null);
    $objectId = $this->getRequest()->getParam('object_id',null);

    if(is_null($this->getRequest()->getParam('mode',null))){
      if(is_null($objectId) && ! is_null($parentId)){
        $info['mode'] = 'add';
      }else{
        $info['mode'] = 'edit';
      }
    }else{
      $info['mode'] = $this->getRequest()->getParam('mode',null);
    }

    // fill the content object with info to work with
    if($info['mode'] == 'add'){
      $content = new Default_Model_Content();
      $content->setParent($parentId);
      if(! is_null($objecttypeId)){
        $content->setObjecttypeId($objecttypeId);
        $objecttype = $content->getObjecttype();
        $info['objecttype'] = $objecttype;
      }
      $info['parent_id'] = $parentId;
    }else{
      $content = Default_Model_Content::getInstanceByPk($objectId);
      $objecttype = $content->getObjecttype();
      $info['content'] = $content;
      $info['objecttype'] = $objecttype;
      $info['parent_id'] = $content->getParent();
    }

    // if we don't have a objecttype in the content we are adding and need to
    // chose one first
    if($content->getObjecttypeId() == null){
      $form = jcms\FormsHelper::getObjecttypeSelectorForm($content->getParent());
      $this->view->objecttypes = \Default_Model_User::getAvailableExecObjecttypes();
    }else{
      // check if the current use is allowed to handle the action
      switch ($info['mode']) {
        case 'edit' :
          if(! $content->isAllowedByUser(null,Default_Model_Permission::PERMISSION_EDIT)){
            throw new Zend_Exception('not allowed');
          }
          break;
        case 'view' :
          if(! $content->isAllowedByUser(null,Default_Model_Permission::PERMISSION_READ)){
            throw new Zend_Exception('not allowed');
          }
          break;
        case 'add' :
          $parent = Default_Model_Content::getInstanceByPk($parentId);
          if(! $parent->isAllowedByUser(null,Default_Model_Permission::PERMISSION_READ)){
            throw new Zend_Exception('not allowed');
          }
          break;
      }

      $form = jcms\FormsHelper::getContentForm($content,$info['mode'],'/backend/iframe/contentedit');

      if($this->getRequest()->isPost() && $this->getRequest()->getParam('save',true)){
        if($form->isValid($_POST)){

          // post is valid, let's fill up the object
          $fields = $objecttype->getFields();

          foreach($fields as $field){
            $formElement = $field->getDbName();

            switch ($field->getFieldType()) {
              case 'file' :
                jcms\Files::saveContentFile($field,$form,$content);
                break;
              case 'image' :
                jcms\Files::saveContentImage($field,$form,$content);
                break;
              default :
                $value = $this->getRequest()->getParam($field->getDbName());

                if($formElement == 'url'){
                  if(trim($value) == '' || !strstr($value, '/')){
                    $value = $content->getRoutePathString();

                    if($value == ''){
                      $value = '/'.preg_replace ('/[^A-Za-z0-9]/', '_', $content->getTitle());
                    }

                    $url = $form->getElement('url');
                    $url->setValue($value);
                  }else{
                    $value = $this->getRequest()->getParam('url',null);
                  }

                }
                $setter = $field->getContentSetterString();
                if(trim($value) != '')
                  $content->$setter($value);
                break;
            }
          }

          try{

//             if($info['mode'] == 'add'){
//               $info['savesuccessmessage'] = jcms\Translator::tr('FILES_PREVIEW_ONLY_WHEN_REOPEND');
//             }

            if(is_null($content->getSort()))
              $content->setSortValue(false);
            $content->save(); // save it so we have id's to work with, sort can't be null to save

            $mode = $form->getElement('mode');
            $mode->setValue('edit');
            $object_id = $form->getElement('object_id');
            $object_id->setValue($content->getId());

            $info['sort'] = $content->getSort();
            $info['icon'] = $content->getObjecttype()->getIcon();
            $info['label'] = $content->getTitle();
            $info['permissions'] = $content->getPermissionsString();
            $info['title'] = $content->getTitle();
            $info['content'] = $content;
            $info['savesuccess'] = true;
          }catch(Exception $e){
            throw new Zend_Exception($e->getMessage());
          }
        }
      }
    }

    $this->view->form = $form;
    $this->view->info = $info;
  }
}
