<?php
/**
 * This controller is used for a set of ajax calls made from the backend of jCms
 */
class Backend_AjaxController extends Zend_Controller_Action {
  /**
   * initiates the controller
   */
  public function init() {
    Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session(jcms\Auth::SESSION_BACKEND_USER));

    $register = Zend_Registry::getInstance();
    $register->settings = $this->getInvokeArg('bootstrap')->getOptions();
    $register->request = $this->getRequest();
    $register->session = new \Zend_Session_Namespace(jcms\Auth::SESSION_BACKEND_USER);
    $this->em = $register->entitymanager;
    $this->cache = $register->cache;
    $this->layout = Zend_Layout::getMvcInstance();

    $this->layout->setLayout('ajax');

    if(! $this->_request->isXmlHttpRequest()){
      throw new Zend_Exception('action was not called by a ajax request');
    }
  }

  /**
   * Action to render tinymceimages
   * Action does nothing
   */
  public function tinymceimagesAction(){

  }

  /**
   * Reloads the tree
   * 
   * @return JSON string of the content tree
   */
  public function reloadtreeAction() {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $response = $this->getResponse();
    $response->setHeader('Content-type','application/json',true);
    return $response->setBody(Zend_Json::encode(jcms\Tree::getInstance()->getTree()));
    exit();
  }

  /**
   * Removes a tree part
   */
  public function removeAction() {

    $this->layout->setLayout('ajax');
    $id = $this->getRequest()->getParam('id',0);
    $parent = $this->getRequest()->getParam('parent',0);
    $content = Default_Model_Content::getInstanceByPk($id);
    if($content->getChildren()){
      $this->view->assign('content',0);
    }else{
      $this->view->assign('content',$content->delete());
    }

  }

  /**
   * Changes a content object's position in the tree
   */
  public function changecontentpositionAction(){
    $json = array();
    $json['success'] = false;

    $position = $this->getRequest()->getParam('position', null);
    $movedId = $this->getRequest()->getParam('moved_id', null);
    $previousParentId = $this->getRequest()->getParam('previous_parent_id', null);
    $targetId = $this->getRequest()->getParam('target_id', null);

    $target = Default_Model_Content::getInstanceByPk($targetId);
    $moved = Default_Model_Content::getInstanceByPk($movedId);

    $tree = jcms\Tree::getInstance();

    if($moved->isAllowedByUser(null, Default_Model_Permission::PERMISSION_MOVE)){

      switch($position){
        case 'inside':
          // set new parent and sort the children of the parent
          if(! $target->getFolder()){
            $target->setFolder(1);
            $target->save(true);
          }
          $moved->setParent($target->getId());
          $moved->setSort(0);
          $moved->save(true);

          // put the new object at the first place in the sort of the folder
          $sort = 1;
          foreach($target->getChildren() as $child){
            if($child->getId() != $moved->getId()){
              $child->setSort($sort ++);
              $child->save(true);
            }
          }

          $json['success'] = true;
        break;
        default: // after & before

          // target need to have a parent, else it's the root item, can't be a sibling of that one...
          if($target->getParent()){

            // set before/after the target, set same parent and order

            $moved->setParent($target->getParent());

            $childrenBySort = $tree->getChildrenOf($target->getParent(),true);

            // create a array with the new object placed in the correct position
            // if the object is placed on a position on the array the array key will
            // be the new order value
            $set = false;
            $keysWithSort = array();
            $keyCounter = 1;
            foreach($childrenBySort as $child){
              if($child->getId() == $moved->getId()){
                continue;
              }

              if($position == 'before' && $child->getId() == $target->getId()){
                // we are before the target and the moved needs to be places before it, let's place it and move the sort index by 1
                $moved->setSort($keyCounter);
                $keysWithSort[$keyCounter] = $moved->getTitle();
                $moved->save();
                $keyCounter ++;
                $set = true;
              }

              $keysWithSort[$keyCounter] = $child->getTitle();
              $child->setSort($keyCounter);
              $child->save();
              $keyCounter ++;

              if($position == 'after' && $child->getId() == $target->getId()){
                // we are after the target and the moved needs te be placed after it, let's place it and move up the sort index
                $moved->setSort($keyCounter);
                $keysWithSort[$keyCounter] = $moved->getTitle();
                $moved->save();
                $keyCounter ++;
                $set = true;
              }

            }
            if(!$set){
              $moved->setSort(0);
              $moved->save(true);
            }
            $json['success'] = true;
          }
        break;
      }
    }

    $this->getResponse()->setHeader('X-JSON',json_encode($json));

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  /*
   * This action removes a objecttypefield based on the objecttypefield_id in the request
   */
  public function removecontenttypefieldAction(){
    if($this->getRequest()->getParam('objecttypefield_id',null)){
      $objecttypefield = Default_Model_Objecttypefield::getInstanceByPk($this->getRequest()->getParam('objecttypefield_id',null));
      $objecttypefield->delete();
    }

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  /*
   * This action adds a objecttypefield
   */
  public function addobjecttypefieldAction(){

    $objecttypeId = $this->getRequest()->getParam('objecttype_id',null);
    $dbName = $this->getRequest()->getParam('db_name',null);
    $label = $this->getRequest()->getParam('label',null);
    $altType = $this->getRequest()->getParam('alt_type',null);

    $field = new Default_Model_Objecttypefield();
    if($altType != '') $field->setAlternativeType($altType);
    $field->setDbName($dbName);
    $field->setLabel($label);
    $field->setObjecttypeId($objecttypeId);
    $field->save();

    $field->setSort($field->getId());
    $field->save();

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  /**
   * This action shows the objecttype edit pop-up
   */
  public function objecttypefieldsAction(){
    $objecttypeId = $this->getRequest()->getParam('id',null);
    if(!$objecttypeId) exit;
    $objecttype = Default_Model_Objecttype::getInstanceByPk($objecttypeId);

    $fields = $objecttype->getAddableFields();

    $this->view->assign('used',$fields['used']);
    $this->view->assign('usable',$fields['usable']);
    $this->view->assign('objecttype',$objecttype);
  }
}


