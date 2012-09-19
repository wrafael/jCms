<?php
/**
 * If you want to fully use the functionality of jCms call the following functions in the init:
 * jcms\Frontend::initFrontend($this->getRequest()); (Initiates the shizzle that jcms needs to be used on the frontend)
 * jcms\Frontend::setViewScriptForContent($this->_helper, $this->getRequest()); (Set's the correct view script for a content object based on the given id in the querystring)
 *
 * By default the indexAction and index view script will be called, unless you add a [objecttypecode]Action function to this controller
 * or add a [objecttypecode].phtml view script in the objecttempletes map.
 *
 * @author Jonathan van Rij
 *
 */
use Doctrine\DBAL\Types\BooleanType;

class FileController extends Zend_Controller_Action {

  public function init() {
    Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session(jcms\Auth::SESSION_FRONTEND_USER));
    jcms\Frontend::initFrontend($this);
    jcms\Frontend::setViewScriptForContent($this->_helper, $this->getRequest());
    $parentContent = Default_Model_Content::getInstanceByCode('CONTENT');
    $mainmenu = jcms\Tree::getInstance()->getChildrenOf($parentContent->getId());
    $object = Default_Model_Content::getInstanceByPk($this->getRequest()->getParam('id'));
  }

  public function indexAction(){
    jcms\Files::serveFile(
      $this->getRequest()->getParam('id',null),
      $this->getRequest()->getParam('field',null),
      $this->getRequest()->getParam('width',null),
      $this->getRequest()->getParam('height',null),
      $this->getRequest()->getParam('crop', 0),
      false
    );
  }

  public function downloadAction(){
    jcms\Files::serveFile(
    $this->getRequest()->getParam('id',null),
    $this->getRequest()->getParam('field',null),
    $this->getRequest()->getParam('width',null),
    $this->getRequest()->getParam('height',null),
    $this->getRequest()->getParam('crop', 0),
    true
    );
  }
}