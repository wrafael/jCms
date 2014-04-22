<?php
/**
 * This class handles all the needed authentication stuff needed for jCms
 */
namespace jcms;

class Auth implements \Zend_Auth_Adapter_Interface {

  private $username;
  private $ssoKey;
  private $ssoUserId;
  private $password_sha1;
  private $result = null;

  const SESSION_BACKEND_USER = 'backend_user';
  const SESSION_FRONTEND_USER = 'frontend_user';

  /**
   * Set's the cridentials for Auth
   * - username & password for normal Authentication
   * - Single Sign On Key & user ID for Single Sign On Authentication
   *
   * @param string $username
   * @param string $password
   * @param string $ssoKey
   * @param integer $ssoUserId
   */
  public function __construct($username = null, $password = null, $ssoKey = null, $ssoUserId = null) {
    $this->username = $username;
    $this->password_sha1 = sha1($password);
    $this->ssoKey = $ssoKey;
    $this->ssoUserId = $ssoUserId;
  }

  /**
   * Performs an authentication attempt
   *
   * @throws Zend_Auth_Adapter_Exception If authentication cannot
   *         be performed
   * @return Zend_Auth_Result
   */
  public function authenticate() {
    try {
      if (is_null($this->result)) {
        $em = \Zend_Registry::getInstance()->entitymanager;

        if ($this->username && $this->password_sha1) { // normal login
          $query = $em->createQuery('select u from Default_Model_User u where u.username = ?1 AND u.password = ?2 AND u.active = 1');
          $query->setParameter(1, $this->username);
          $query->setParameter(2, $this->password_sha1);
          $user = $query->getResult(); // getSingleScalarResult does not work TODO
        } else if ($this->ssoKey && $this->ssoUserId) { // Single Sign On login
          $user = \Default_Model_User::getInstanceByPk($this->ssoUserId);
          if ($user->checkSsoKey($this->ssoKey)) {
            \Zend_Registry::getInstance()->session->user = $user;
            return new \Zend_Auth_Result(\Zend_Auth_Result::SUCCESS, $this->username);
          } else {
            \Zend_Registry::getInstance()->session->user = null;
            return new \Zend_Auth_Result(\Zend_Auth_Result::FAILURE, $this->username);
          }
        }

        $requiredRole = 'Frontend';
        if (\Zend_Registry::getInstance()->request->getModuleName() == 'backend') {
          $requiredRole = 'Backend';
        }

        if (count($user) > 0) {
          $user = $user[0];
          if ($user->hasRole($requiredRole)) {
            \Zend_Registry::getInstance()->session->user = $user;
            return new \Zend_Auth_Result(\Zend_Auth_Result::SUCCESS, $this->username);
          } else {
            \Zend_Registry::getInstance()->session->user = null;
            return new \Zend_Auth_Result(\Zend_Auth_Result::FAILURE, $this->username);
          }
        } else {
          \Zend_Registry::getInstance()->session->user = null;
          return new \Zend_Auth_Result(\Zend_Auth_Result::FAILURE, $this->username);
        }
      }
    } catch (\Exception $e) {
      throw new \Zend_Auth_Adapter_Exception('Failed to authenticate: ' . $e->getMessage());
    }
  }

  public function getCode() {
    if (is_null($this->result)) {
      $this->authenticate();
    }
    return $this->result->getCode();
  }

  public static function getAuthString() {
    if (\Zend_Auth::getInstance()->hasIdentity()) {
      return 'ingelogd met: ' . \Zend_Auth::getInstance()->getIdentity();
    } else {
      return 'niet ingelogd';
    }
  }

  /**
   * Handles the flow of the login form.
   * If needed, returns a form.
   *
   * @param \Zend_Controller_Request_Abstract $request
   * @return \Zend_Form
   */
  public static function authenticateWithAuthForm(\Zend_Controller_Request_Abstract $request) {
    // check what form to generate
    if (is_null(\Zend_Registry::get('session')->user)) {
      $form = FormsHelper::getLoginForm($request);
    } else {
      $form = FormsHelper::getLogoutForm($request);
    }

    if ($request->isPost()) {
      if ($form->isValid($_POST)) {
        // checks what to do based on type of form
        if ($form->getId() == 'logout') {
          \Zend_Registry::getInstance()->session->user = null;
          \Zend_Auth::getInstance()->clearIdentity(); // clears session
        } else if ($form->getId() == 'login') { // authenticates user
          $values = $form->getValues();
          $result = \Zend_Auth::getInstance()->authenticate(new Auth($values['username'], $values['password']));
        }
      }
    }
    return $form;
  }

  public static function authenticateWithSso(\Zend_Controller_Request_Abstract $request) {
    $key = $request->getParam('ssokey', null);
    $userId = intval($request->getParam('uid', null));

    $result = \Zend_Auth::getInstance()->authenticate(new Auth(null, null, $key, $userId));

    if ($result->getCode() == \Zend_Auth_Result::SUCCESS) {
      $url = str_replace('_', '/', urldecode($request->getParam('url', null)));
      if ($url) {
        header('Location: ' . $url);
      } else {
        header('Location: /');
      }
      exit;
    } else {
      header('Location: /');
      exit;
    }
  }

}
