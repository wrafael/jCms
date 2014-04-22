<?php
/**
 * This class has functions to get the frontend up and running with jCms
 */
namespace jcms;

class Frontend {

  /**
   * Initiates the shizzle that jcms needs to be used on the frontend
   *
   * @param \Zend_Controller_Request_Http $request
   * @throws \Exception
   */
  public static function initFrontend(\Zend_Controller_Action $controller) {

    $request = $controller->getRequest();

    \Zend_Registry::getInstance()->settings = $controller->getInvokeArg('bootstrap')->getOptions();

    \Zend_Auth::getInstance()->setStorage(new \Zend_Auth_Storage_Session(Auth::SESSION_FRONTEND_USER));

    $register = \Zend_Registry::getInstance();
    $register->request = $request;
    $register->session = new \Zend_Session_Namespace(Auth::SESSION_FRONTEND_USER);

    if(! \Zend_Auth::getInstance()->hasIdentity()){
      \Zend_Auth::getInstance()->authenticate(new Auth('frontend_guest','frontend_guest'));
    }

    if(! \Zend_Auth::getInstance()->hasIdentity()){
      throw new \Exception('No User with frontend rights available');
    }
  }

 /**
  * Triggers the correct controller function, checks if the contenttype has one of it's own, else calls the indexAction
  *
  * @param \Default_Model_Content $content
  */
  public static function triggerControllerFunction(\IndexController $controller, \Default_Model_Content $content){
    $controllerFunction = $content->getObjecttype()->getCode().'Action';
    if(method_exists($controller, $controllerFunction)){
      $controller->$controllerFunction();
      return;
    }
    $controller->indexAction();
  }

  /**
   * Set's the correct view script for a content object based on the given id in the querystring, falls back on the index view script if the given one does not exists
   * A call from the controller looks like this: jcms\Frontend::setViewScriptForContent($this->_helper, $this->getRequest());
   *
   * @param \Zend_Controller_Action_HelperBroker $helper
   * @param \Zend_Controller_Request_Abstract $request
   */
  public static function setViewScriptForContent(\Zend_Controller_Action_HelperBroker $helper, \Zend_Controller_Request_Abstract $request){
    // let's make index the default fallback if a object type does not have a view script
    $helper->viewRenderer('objecttypetemplates/index', null, true);
    $content = Frontend::getCurrentObject();

    if($content){
      $file = APPLICATION_PATH . DS . 'views' . DS . 'scripts' . DS . 'objecttypetemplates' . DS . $content->getObjecttype()->getCode().'.phtml';
      if(file_exists($file)){
        $helper->viewRenderer('objecttypetemplates/'.$content->getObjecttype()->getCode(), null, true);
      }
    }
  }
  
  /*
   * Get's the current object based on the querystring, if it's not there gives the homepage object.
   */
  public static function getCurrentObject(){
  	// get the current page
  	$id = \Zend_Registry::getInstance()->request->getParam('id', false);
  	if(!$id){
  		$content = \Default_Model_Content::getInstanceByCode(\Zend_Registry::getInstance()->settings['jcms']['homepage_code']);
  	}else{
  		$content = \Default_Model_Content::getInstanceByPk($id);
  	}
  	// if we can't find a page for the given id, get the error page
  	if(!$content){
  		$content = \Default_Model_Content::getInstanceByCode(\Zend_Registry::getInstance()->settings['jcms']['errorpage_code']);
  		if(!$content){
  			die(t('CAN_NOT_FIND_ERROR_PAGE'));
  		}
  	}
  	return $content;
  }

  /**
   * Put's the menu information in the Layout:
   * - Array mainmenu
   * - Integer activeSubmenuId
   * - Integer activeMainmenuId
   * - Boolean showsubmenu
   * - Array submenu
   * And return's the current Default_Model_Content.
   *
   * @param Array of title=>link with extra menu items
   */
  public static function putMenuInLayout(){
    $parentContent = \Default_Model_Content::getInstanceByCode('CONTENT');

    $illigalContentTypes = array('news', 'agenda');
    
    $mainmenu = array();
    if($parentContent){
      $mainmenuItems = Tree::getInstance()->getChildrenOf($parentContent->getId());
      foreach($mainmenuItems as $mainmenuItem){
      	if(!in_array(strtolower($mainmenuItem->getObjecttype()->getCode()), $illigalContentTypes)){
      	  $mainmenu[] = $mainmenuItem;
      	} 
      }
    }

    $object = self::getCurrentObject();

    if(! $object){ // no page selected, get first page of main menu
      $object = reset($mainmenu);
    }

    // get menu's
    $submenuParents = array();
    $submenuItems = array();
    $activeSubmenuId = 0;
    $activeMainmenuId = 0;
    $levelsCounter = 0;
    foreach($mainmenu as $menuItem){
      $mainMenuIds[] = $menuItem->getId();
    }
    $tempObject = $object;
    while ( count($submenuItems) == 0 && $levelsCounter < 10 ){
      if(! $tempObject)
        break;
      if(in_array($tempObject->getParent(),$mainMenuIds)){ // my parent is part of the submenu
        $activeSubmenuId = $tempObject->getId();
        $selectedMainMenu = \Default_Model_Content::getInstanceByPk($tempObject->getParent());
        $activeMainmenuId = $selectedMainMenu->getId();
        $submenuItems = $selectedMainMenu->getChildren();
      }else if(in_array($tempObject->getId(),$mainMenuIds)){ // i am part of the main menu
        $activeMainmenuId = $tempObject->getId();
        $submenuItems = $tempObject->getChildren();
      }else{ // i am my parent are not part of the submenu, lets check my parent
        $tempObject = \Default_Model_Content::getInstanceByPk($tempObject->getParent());
      }
      $levelsCounter ++;
    }
    
    $submenu = array();
    foreach($submenuItems as $submenuItem){
    	if(!in_array(strtolower($submenuItem->getObjecttype()->getCode()), $illigalContentTypes)){
    		$submenu[] = $submenuItem;
    	}
    }

    \Zend_Layout::getMvcInstance()->assign('mainmenu',$mainmenu);
    \Zend_Layout::getMvcInstance()->assign('activeSubmenuId',$activeSubmenuId);
    \Zend_Layout::getMvcInstance()->assign('activeMainmenuId',$activeMainmenuId);
    \Zend_Layout::getMvcInstance()->assign('showsubmenu',count($submenu));
    \Zend_Layout::getMvcInstance()->assign('submenu',$submenu);
  }
}

?>