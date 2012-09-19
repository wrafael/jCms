<?php

use jcms\PDOHelper;

class Backend_IndexController extends Zend_Controller_Action {

  public function init() {
    Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session(jcms\Auth::SESSION_BACKEND_USER));

    $this->layout = Zend_Layout::getMvcInstance();

    $register = Zend_Registry::getInstance();
    $register->settings = $this->getInvokeArg('bootstrap')->getOptions();
    $register->request = $this->getRequest();
    $register->session = new \Zend_Session_Namespace(jcms\Auth::SESSION_BACKEND_USER);
    $this->em = $register->entitymanager;
    $this->cache = $register->cache;

    $this->layout->setLayout('backend');

    // check if the user is logged in, if not, redirect the user to the
    // login page
    if($this->getRequest()->getActionName() != 'login' && $this->getRequest()->getActionName() != 'sso'){
      if(is_null(Zend_Registry::getInstance()->session->user)){
        $this->_helper->redirector('login','index','backend');
      }
    }

    $user = Zend_Registry::getInstance()->session->user;

    $menu = array();
    if(Zend_Registry::getInstance()->settings['site']['backend']['homebutton']) $menu ['/backend/index/index'] = 'Dashboard';
    // $menu['/backend/index/content'] = 'Content';
    if(isset($user) && $user->hasRole('God')){
      $menu['/backend/index/objecttype'] = 'Typebeheer';
      $menu['/backend/index/role'] = 'Rollen';
    }
    if(isset($user) && ($user->hasRole('Admin') || $user->hasRole('God'))){
      $menu['/backend/index/user'] = 'Gebruikers';
      if(Zend_Registry::getInstance()->settings['site']['enableimport']) $menu['/backend/index/import'] = 'Import';
      if(Zend_Registry::getInstance()->settings['site']['enablegroupmove']) $menu['/backend/index/movegroup'] = 'Groep opties';
    }

    $menu['/backend/index/logout'] = 'Uitloggen';

    $this->layout->assign('tree',jcms\Tree::getInstance());

    $this->layout->assign('menu',$menu);
  }

  public function importAction(){

    if(!Zend_Registry::getInstance()->settings['site']['enableimport'])
    {
      throw new Exception('this module is turned uff');
    }

    $somethingImported = false;
    $files = array();
    $importedFiles = array();
    $filesToImport = $this->getRequest()->getParam('importfiles', array());
    $folder =  getcwd().'\\..\\'.Zend_Registry::getInstance()->settings['jcms']['importfolder'].'\\';

    if(Zend_Registry::getInstance()->session->user->hasRole('Admin') || Zend_Registry::getInstance()->session->user->hasRole('God')){
      foreach($filesToImport as $toImport){

        $parentCode = $this->getRequest()->getParam('parent_code', null);

        if($parentCode){
          $parentObject = Default_Model_Content::getInstanceByCode($parentCode);

          if($parentObject){
            $contenttypeId = Zend_Registry::getInstance()->settings['jcms']['imageimport']['objecttype_id'];
            $setter = 'set'.ucfirst(Zend_Registry::getInstance()->settings['jcms']['imageimport']['field']);
            jcms\Files::saveImportImage($folder, $toImport, $contenttypeId, $setter, $parentObject);
            $importedFiles[] = $toImport;
          }
        }
      }

      if ($handle = opendir($folder)) {
        while (false !== ($file = readdir($handle))) {
          if($file != '..' && $file != '.'){
            $files[] = $file;
          }
        }
        closedir($handle);
      }
    }
    if(count($importedFiles)) $this->layout->assign('tree',jcms\Tree::getInstance());
    $this->view->files = $files;
    $this->view->importedFiles = $importedFiles;
  }

  public function indexAction() {
  }

  public function userAction() {
    $this->view->users = Default_Model_User::getAllUsers();
  }

  public function roleAction() {
    if(Zend_Registry::getInstance()->session->user->hasRole('God')){

      // $this->view->form = jcms\FormsHelper::getFormRole(new
      // Default_Model_Role());

      $this->view->roles = Default_Model_Role::getAllRoles();
    }
  }

  public function roleeditAction() {
    if(Zend_Registry::getInstance()->session->user->hasRole('God')){
      $id = $this->getRequest()->getParam('id',null);

      if(! $id){
        $role = new Default_Model_Role();
        $form = jcms\FormsHelper::getFormRole($role);
      }else{
        $role = Default_Model_Role::getInstanceByPk($id);
        $form = jcms\FormsHelper::getFormRole($role,true);
      }

      if(! $role->canEdit())
        $this->_helper->redirector('role','index','backend');

      if($this->getRequest()->getParam('delete_role',null)){
        $role->delete();
        $this->_helper->redirector('role','index','backend');
      }else{

        if($this->_request->isPost()){
          if($form->isValid($_POST)){

            $role->setName($this->getRequest()->getParam('name',null));
            $role->save();

            $this->_helper->redirector('role','index','backend');
          }
        }
      }
      $this->view->content = $form;
      if($id){
        $this->view->permissions = jcms\FormsHelper::getPermissionsForm($role);
      }else{
        $this->view->permissions = 'Sla de rol eerst op voor dat u de rechten kan bepalen.';
      }
    }else{
      throw new Zend_Exception('User does not have the rights to edit the role.');
    }
  }

  public function permissionseditAction() {
    if(Zend_Registry::getInstance()->session->user->hasRole('God')){
      $id = $this->getRequest()->getParam('id',null);
      $role = Default_Model_Role::getInstanceByPk($id);

      if(! $role->canEdit())
        $this->_helper->redirector('role','index','backend');

      $query = Zend_Registry::getInstance()->entitymanager->createQuery('delete from Default_Model_Permission p where p.role_id = ?1');
      $query->setParameter(1,$id);
      $query->execute();

      foreach($this->getRequest()->getParam('permissions',array()) as $p){
        $arr = explode('_',$p);
        $objecttype = $arr[1];
        $permission = $arr[2];


        $object = new Default_Model_Permission();
        $object->setObjecttypeId($objecttype);
        $object->setRoleId($id);
        $object->setType($permission);

        Zend_Registry::getInstance()->entitymanager->persist($object);
        Zend_Registry::getInstance()->entitymanager->flush();
      }

    }

    $this->_helper->redirector('role','index','backend');
  }

  public function usereditAction() {
    if(Zend_Registry::getInstance()->session->user->hasRole('God') || Zend_Registry::getInstance()->session->user->hasRole('Admin')){
      $id = $this->getRequest()->getParam('id',null);

      if(! $id){
        $user = new Default_Model_User();
        $form = jcms\FormsHelper::getFormUser($user);
      }else{
        $user = Default_Model_User::getInstanceByPk($id);
        $form = jcms\FormsHelper::getFormUser($user,true);
      }

      if($this->getRequest()->getParam('delete_user',null)){
        $user->delete();
        $this->_helper->redirector('user','index','backend');
      }else{

        if($this->_request->isPost()){
          if($form->isValid($_POST)){

            if($user->getActive() == 0 && $this->getRequest()->getParam('active') == '1'){
              $user->setActive(1);
              $user->sendActivationMail();
            }else if($this->getRequest()->getParam('active') == '0'){
              $user->setActive(0);
            }

            $user->setEmail($this->getRequest()->getParam('email'));

            if(strlen(trim($this->getRequest()->getParam('password'))) > 0){
              $user->setPassword($this->getRequest()->getParam('password',true));
            }

            $user->setUsername($this->getRequest()->getParam('username'));
            $user->setNote($this->getRequest()->getParam('note'));

            $user->save();

            // get the roles the user has before this post
            $query = Zend_Registry::getInstance()->entitymanager->createQuery('select ur from Default_Model_UserRole ur WHERE ur.user_id = ?1');
            $query->setParameter(1,$user->getId());
            $userRoles = $query->getResult();

            // roles the user needs to get
            $postRoles = $this->getRequest()->getParam('roles',array());

            foreach($userRoles as $userRole){
              $key = array_search($userRole->getRoleId(),$postRoles);

              // var_dump($userRole->getRoleId(), $postRoles);

              if($key === false){
                // if a user_role is not in the post, remove it
                Zend_Registry::getInstance()->entitymanager->remove($userRole);
                Zend_Registry::getInstance()->entitymanager->flush();
              }else{
                // if a user_role is in the post keep it in the dtb and remove
                // it
                // from the post array
                unset($postRoles[$key]);
              }
            }

            // post array only has the ones left that are still not in the dtb
            foreach($postRoles as $role){
              $ur = new Default_Model_UserRole();
              $ur->setRoleId($role);
              $ur->setUserId($user->getId());
              $ur->save();
            }

            $this->_helper->redirector('user','index','backend');
          }
        }
        $this->view->content = $form;
      }
    }
    $this->renderScript('index/default.phtml');
  }

  public function objecttypeeditAction() {
    if(Zend_Registry::getInstance()->session->user->hasRole('God')){
      $id = $this->getRequest()->getParam('id',null);

      if(! $id){
        $objecttype = new Default_Model_Objecttype();
        $form = jcms\FormsHelper::getFormObjecttype($objecttype);
      }else{
        $objecttype = Default_Model_Objecttype::getInstanceByPk($id);

        if($objecttype->canDelete()){
          $form = jcms\FormsHelper::getFormObjecttype($objecttype,true);
        }else{
          $form = jcms\FormsHelper::getFormObjecttype($objecttype,false);
        }
      }

      if($this->_request->isPost()){
        if($form->isValid($_POST)){
          if($this->getRequest()->getParam('delete_objecttype',null)){
            $objecttype->delete();
          }else{

            $objecttype->setName($this->getRequest()->getParam('name'));
            $objecttype->setCode($this->getRequest()->getParam('code'));
            $objecttype->setIcon($this->getRequest()->getParam('icon'));
            $objecttype->setDescription($this->getRequest()->getParam('description'));

            $objecttype->save();

            if(! $id){
              $of = new Default_Model_Objecttypefield();
              $of->setDbName('title');
              $of->setLabel('Titel');
              $of->setObjecttypeId($objecttype->getId());
              $of->setSort($objecttype->getId());
              $of->save();

              $of = new Default_Model_Objecttypefield();
              $of->setDbName('code');
              $of->setLabel('Code');
              $of->setObjecttypeId($objecttype->getId());
              $of->setSort($objecttype->getId() + 1);
              $of->save();

              $of = new Default_Model_Objecttypefield();
              $of->setDbName('url');
              $of->setLabel('Url');
              $of->setObjecttypeId($objecttype->getId());
              $of->setSort($objecttype->getId() + 2);
              $of->save();
            }
          }
          $this->_helper->redirector('objecttype','index','backend');
        }
      }
      $this->view->content = $form;
    }
    $this->renderScript('index/default.phtml');
  }

  public function objecttypeAction() {
    $this->view->assign('objecttypes',\Default_Model_User::getAvailableExecObjecttypes());
  }

  public function permissionAction() {
    $this->view->content = null;
  }

  public function loginAction() {

    $this->layout->setLayout('backend_auth');

    $form = jcms\Auth::authenticateWithAuthForm($this->getRequest());

    if(\Zend_Registry::get('session')->user){
      $this->_helper->redirector('index','index','backend');
    }else{
      $this->view->assign('content',$form);
    }

    $this->renderScript('index/default.phtml');
  }

  public function logoutAction() {
    // i kind of expected clearitentity to work, but since i work with storage to seporate backend from frontend it did not work, with set storage it also did not work...
    //Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session(jcms\Auth::SESSION_BACKEND_USER));
    //Zend_Auth::getInstance()->clearIdentity();
    // so i used the oldskool way of doing things...
    unset($_SESSION[jcms\Auth::SESSION_BACKEND_USER]);
    $this->_helper->redirector('index','index','backend');
  }

  public function ssoAction(){
    jcms\Auth::authenticateWithSso($this->getRequest());
  }

  public function movegroupAction(){
    if(!Zend_Registry::getInstance()->settings['site']['enablegroupmove'])
    {
      throw new Exception('this module is turned uff');
    }

    $user = Zend_Registry::getInstance()->session->user;

    if(isset($user) && ($user->hasRole('Admin') || $user->hasRole('God'))){
      if($this->getRequest()->isPost()){
        $fromRole = $this->getRequest()->getParam('from_role');
        $toRole = $this->getRequest()->getParam('to_role');

        $fromRoleObject = Default_Model_Role::getInstanceByPk($fromRole);
        $toRoleObject = Default_Model_Role::getInstanceByPk($toRole);

        $users = $fromRoleObject->getUsers();

        $conn = jcms\PDOHelper::getConnection();

        foreach($users as $user){
          // remove old role
          $conn->exec("DELETE FROM user_role WHERE user_id = {$user->getId()} and role_id = {$fromRole}");

          if($toRoleObject){
            // add new role
            $ur = new Default_Model_UserRole();
            $ur->setRoleId($toRoleObject->getId());
            $ur->setUserId($user->getId());
            $ur->save();
          }
        }
        $this->view->assign('changedUsers',$users);
        $this->view->assign('fromRole',$fromRoleObject);
        $this->view->assign('toRole',$toRoleObject);
      }
    }

    $this->view->assign('roles',Default_Model_Role::getRoles());
  }
}
