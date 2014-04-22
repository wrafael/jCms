<?php
/**
 * This controller handles the rendering and scaling of the images and the serving of all the files.
 */
class Backend_FileController extends Zend_Controller_Action {

  /**
   * initiates the controller
   */
  public function init() {
    $register = Zend_Registry::getInstance();
    $register->session = new \Zend_Session_Namespace(jcms\Auth::SESSION_BACKEND_USER);

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  public function thumbnailAction(){
    jcms\Files::serveThumb($this->getRequest()->getParam('id',null), $this->getRequest()->getParam('field',null));
  }

  public function indexAction(){
    jcms\Files::serveFile(
    $this->getRequest()->getParam('id',null),
    $this->getRequest()->getParam('field',null),
    $this->getRequest()->getParam('width',null),
    $this->getRequest()->getParam('height',null),
    $this->getRequest()->getParam('crop', 0)
    );
  }
}


