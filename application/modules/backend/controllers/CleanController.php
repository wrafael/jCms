<?php
/**
 * All the actions in this controller will be rendered with a clean template.
 */
class Backend_CleanController extends Zend_Controller_Action {

  /**
   * initiates the controller
   */
  public function init() {
    Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session(jcms\Auth::SESSION_BACKEND_USER));

    $register = Zend_Registry::getInstance();
    $register->request = $this->getRequest();
    $register->session = new \Zend_Session_Namespace(jcms\Auth::SESSION_BACKEND_USER);
    $this->em = $register->entitymanager;
    $this->cache = $register->cache;
    $this->layout = Zend_Layout::getMvcInstance();

    $this->layout->setLayout('ajax');
  }

  /**
   * Get's a list of images for the backend wysiwyg editor
   */
  public function tinymceimagesAction(){
    $this->view->assign('files',$this->getTinyMceList('image'));
  }

  /**
   * Get's a list of links for the backend wysiwyg editor
   */
  public function tinymcelinksAction(){
    $this->view->assign('files',$this->getTinyMceList('file'));
  }

  /**
   * Get's a list of files
   * 
   * @param String Fieldtype
   * @return array list of files
   */
  private function getTinyMceList($type){

    $files = array();

    $sql = <<<EOT
 select o from Default_Model_Objecttypefield o where o.alternative_type = '{$type}'
EOT;

    $query = Zend_Registry::getInstance ()->entitymanager->createQuery($sql);
    $objecttypefields = $query->getResult();
    $objecttypes = '0';
    $fields = array();

    foreach($objecttypefields as $objecttypefield){
      $fields[$objecttypefield->getObjecttypeId()] = $objecttypefield->getDbName();
      $objecttypes .= ','.$objecttypefield->getObjecttypeId();
    }

    $sql = <<<EOT
select c from Default_Model_Content c where c.objecttype_id in ({$objecttypes}) and c.active = 1
EOT;

    $query = Zend_Registry::getInstance ()->entitymanager->createQuery($sql);
    $contents = $query->getResult();

    foreach($contents as $content){
      $dbName = $fields[$content->getObjecttypeId()];
      $strArray = explode('_',$dbName);
      $newStr = '';
      foreach($strArray as $str){
        $newStr .= ucfirst($str);
      }
      $getter = 'get'.$newStr;
      $info = json_decode($content->$getter());

      if($info->batch == false){
        $files["/file/index/id/{$content->getId()}/field/{$fields[$content->getObjecttypeId()]}"] = $content->getTitle().'/'.$info->name;
      }
    }
    return $files;
  }

  public function reloadtreeAction() {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $response = $this->getResponse();
    $response->setHeader('Content-type','application/json',true);
    return $response->setBody(Zend_Json::encode(jcms\Tree::getInstance()->getTree()));
    exit();
  }

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

    switch($position){
      case 'inside':
        // set new parent and sort the children of the parent
        if(! $target->getFolder()){
          $target->setFolder(1);
          $target->save();
        }
        $moved->setParent($target->getId());
        $moved->setSort(0);
        $moved->save();

        // put the new object at the first place in the sort of the folder
        $sort = 1;
        foreach($target->getChildren() as $child){
          if($child->getId() != $moved->getId()){
            $child->setSort($sort ++);
            $child->save();
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
            $moved->save();
          }
          $json['success'] = true;
        }
      break;
    }

    $this->getResponse()->setHeader('X-JSON',json_encode($json));

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  public function removecontenttypefieldAction(){
    if($this->getRequest()->getParam('objecttypefield_id',null)){
      $objecttypefield = Default_Model_Objecttypefield::getInstanceByPk($this->getRequest()->getParam('objecttypefield_id',null));
      $objecttypefield->delete();
    }

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

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


