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
class AccountController extends Zend_Controller_Action {

  public function init() {
    Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session(jcms\Auth::SESSION_FRONTEND_USER));
    jcms\Frontend::initFrontend($this);
    jcms\Frontend::putMenuInLayout($this->getRequest()->getParam('id'));
  }

  public function indexAction() {

    $session = new \Zend_Session_Namespace(jcms\Auth::SESSION_FRONTEND_USER);

    if(Zend_Registry::getInstance()->session->user->getUsername() == 'frontend_guest'){
      $this->_helper->redirector('login');
    }

    $this->_helper->viewRenderer('account/account',null,true);
  }

  public function registerteacherAction(){
  	$form = jcms\FormsHelper::getRegisterForm('/account/registerteacher', true);
  	
  	if($this->_request->isPost() && $form->isValid($_POST)){
  	
  		// set fields
  	
  		$email = $this->getRequest()->getParam('email', null);
  		$username = $this->getRequest()->getParam('username', null);
  		$password = $this->getRequest()->getParam('password', null);
  	
  		$user = new Default_Model_User();
  		$user->setActive(0);
  		$user->setEmail($email);
  		$user->setNote('Registratie van een docent.');
  		$user->setPassword($password, true);
  		$user->setUsername($username);

  		$user->save();
  	
  		// send the admin a mail
  		$admin = Default_Model_User::getInstanceByPk(2);
  		$key = $admin->getNewSsokey();
  	
  		$url = $_SERVER['HTTP_HOST'].'/backend/index/sso/ssokey/'.$key.'/uid/'.$admin->getId().'/url/'.str_replace('%2F', '_', urlencode('/backend/index/useredit/id/'.$user->getId()));
  	
  		$html = str_replace('%url%',$url,Default_Model_Content::getInstanceByCode('USER_REG_MAIL_TEMPLATE', true)->getContent());
  	
  		$mail = new Zend_Mail();
  		$mail->setBodyText($html);
  		$mail->setFrom('info@jcms.nl', 'jcms');
  	
  		$mail->addTo(Zend_Registry::getInstance()->settings['site']['sysadminemail'], 'Admin '.Zend_Registry::getInstance()->settings['site']['title']);
  		$mail->setSubject(trim(Default_Model_Content::getInstanceByCode('USER_REG_MAIL_TITLE', true)->getContent()));
  		$mail->send();
  	
  		$this->_helper->redirector('success');
  	
  	}
  	
  	$query = Zend_Registry::getInstance()->entitymanager->createQuery('select c from Default_Model_Content c WHERE c.code = ?1');
  	$query->setParameter(1,'REGISTERTEACHER_TEXT');

  	$content = $query->getSingleResult();
  	
  	if($content) $this->view->registerText = $content->getContent();
  	$this->view->registerForm = $form;
  }
  
  public function registerAction() {
  	$form = jcms\FormsHelper::getRegisterForm('/account/register', false);
  	
  	if($this->_request->isPost() && $form->isValid($_POST)){
  	
  		// set fields
  	
  		$email = $this->getRequest()->getParam('email', null);
  		$username = $this->getRequest()->getParam('username', null);
  		$password = $this->getRequest()->getParam('password', null);
  		$note = $this->getRequest()->getParam('note', null);
  	
  		$user = new Default_Model_User();
  		$user->setActive(0);
  		$user->setEmail($email);
  		$user->setNote($note);
  		$user->setPassword($password, true);
  		$user->setUsername($username);
  	
  	
  		$user->save();
  	
  		// send the admin a mail
  		$admin = Default_Model_User::getInstanceByPk(2);
  		$key = $admin->getNewSsokey();
  	
  		$url = $_SERVER['HTTP_HOST'].'/backend/index/sso/ssokey/'.$key.'/uid/'.$admin->getId().'/url/'.str_replace('%2F', '_', urlencode('/backend/index/useredit/id/'.$user->getId()));
  	
  		$html = str_replace('%url%',$url,Default_Model_Content::getInstanceByCode('USER_REG_MAIL_TEMPLATE', true)->getContent());
  	
  		$mail = new Zend_Mail();
  		$mail->setBodyText($html);
  		$mail->setFrom('info@jcms.nl', 'jcms');
  	
  		$mail->addTo(Zend_Registry::getInstance()->settings['site']['sysadminemail'], 'Admin '.Zend_Registry::getInstance()->settings['site']['title']);
  		$mail->setSubject(trim(Default_Model_Content::getInstanceByCode('USER_REG_MAIL_TITLE', true)->getContent()));
  		$mail->send();
  	
  		$this->_helper->redirector('success');
  	
  	}
  	
  	$query = Zend_Registry::getInstance()->entitymanager->createQuery('select c from Default_Model_Content c WHERE c.code = ?1');
  	$query->setParameter(1,'REGISTER_TEXT');

  	$content = $query->getSingleResult();
  	
  	if($content) $this->view->registerText = $content->getContent();
  	$this->view->registerForm = $form;
  }

  public function successAction() {
  }

  public function signonAction(){
    Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session(jcms\Auth::SESSION_BACKEND_USER));

  }

  public function logoutAction() {
    // i kind of expected clearitentity to work, but since i work with storage
    // to seporate backend from frontend it did not work, with set storage it
    // also did not work...
    // Zend_Auth::getInstance()->setStorage(new
    // Zend_Auth_Storage_Session(jcms\Auth::SESSION_BACKEND_USER));
    // Zend_Auth::getInstance()->clearIdentity();
    // so i used the oldskool way of doing things...
    unset($_SESSION[jcms\Auth::SESSION_FRONTEND_USER]);
    $this->_helper->redirector('login');
  }

  public function loginAction() {
    $form = jcms\FormsHelper::getLoginForm($this->getRequest());
    $form->setAction('/account/login');

    if($this->getRequest()->isPost()){
      if($form->isValid($_POST)){
        $values = $form->getValues();
        $result = \Zend_Auth::getInstance()->authenticate(new jcms\Auth($values['username'],$values['password']));
        if($result->getCode()){
          $landing = Default_Model_Content::getInstanceByCode(Zend_Registry::getInstance()->settings['jcms']['logon_landing']);
          if($landing){
          	header('Location: '.$landing->getUrl());
          }else{
          	$this->_helper->redirector('index');
          }
        }
      }
    }

    $this->view->loginForm = $form;
  }
}