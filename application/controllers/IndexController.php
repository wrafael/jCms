<?php

/**
 * If you want to fully use the functionality of jCms call the following functions in the init:
 * jcms\Frontend::initFrontend($this->getRequest()); (Initiates the shizzle that jcms needs to be used on the frontend)
 * jcms\Frontend::setViewScriptForContent($this->_helper, $this->getRequest()); (Set's the correct view script for a content object based on the given id in the querystring)
 *
 * By default the indexAction and index view script will be called, unless you add a [objecttypecode]Action function to this controller
 * or add a [objecttypecode].phtml view script in the objecttempletes map.
 *
 * @author Jonathan
 *
 */
class IndexController extends Zend_Controller_Action {

  public function init() {
    Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session(jcms\Auth::SESSION_FRONTEND_USER));

    jcms\Frontend::initFrontend($this);
    jcms\Frontend::setViewScriptForContent($this->_helper,$this->getRequest());
    jcms\Frontend::putMenuInLayout();

    $newsHolder = Default_Model_Content::getInstanceByCode('NEWS', true);
    if($newsHolder){
      $query = Zend_Registry::getInstance()->entitymanager->createQuery("SELECT c FROM Default_Model_Content c WHERE c.active = 1 AND c.parent = " . $newsHolder->getId() . " AND c.datetime1<CURRENT_TIMESTAMP() AND (c.datetime2>CURRENT_TIMESTAMP() OR c.datetime2 is null) ORDER BY c.datetime1 DESC")->setMaxResults(5);
      \Zend_Layout::getMvcInstance()->assign('news',$query->getResult());
    }
  }

  /*
   * Default action of the index controller.
   * Get's the currently used object and pushes it to the template so the tamplate can do it's magic
   */
  public function indexAction() {
  	$this->view->object = jcms\Frontend::getCurrentObject();
  }

  // if there is no action for the gives contenttype, call the index
  public function __call($functionname, $params) {
    $this->view->object = jcms\Frontend::getCurrentObject();
    jcms\Frontend::triggerControllerFunction($this,jcms\Frontend::getCurrentObject());
  }

  /*
   * Handles the childreference content type
   * 
   * This objecttype references to an other url
   */
  public function childreferenceAction() {
    $object = Default_Model_Content::getInstanceByPk($this->getRequest()->getParam('id'));

    $child = $object->getFirstChild();
    if($child){
      if($child->getObjecttype()->getName() != 'childreference'){
        header('Location: ' . $child->getUrl());
        exit();
      }
    }
  }

  /*
   * Handles the newslist contenttype rendering
   */
  public function newslistAction() {
    \Zend_Layout::getMvcInstance()->assign('showsubmenu',false);

    $object = jcms\Frontend::getCurrentObject();
    $checkedChildren = array();

    $query = Zend_Registry::getInstance()->entitymanager->createQuery('select c from Default_Model_Content c WHERE c.parent = ?1 and c.datetime1<CURRENT_TIMESTAMP() AND (c.datetime2>CURRENT_TIMESTAMP() OR c.datetime2 is null) order by c.datetime1 asc')->setMaxResults(10);
    $query->setParameter(1, $object->getId());
    $children = $query->getResult();

    foreach($children as $child){
      $child->setACL();
    }

    foreach($children as $child){
      if($child->isAllowedByUser()){
        $checkedChildren[] = $child;
      }
    }

    $this->view->children = $checkedChildren;
    $this->view->object = $object;
  }

  /*
   * Handles the agenda contenttype rendering
   */
  public function agendaAction(){
    \Zend_Layout::getMvcInstance()->assign('showsubmenu',false);
  }
}