<?php

/**
 * @Entity
 * @Table(name="user")
 */
class Default_Model_User {
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;

	private $cache;

	/**
	 * @Column(type="string")
	 */
	private $username;

	/**
	 * @Column(type="string")
	 */
	private $password;

    /**
     * @Column @Column(type="boolean", length=1, unique=false, nullable=false)
     */
    private $active;

	/**
	 * @Column(type="string")
	 */
	private $note;

	/**
	 * @Column(type="string")
	 */
	private $ssokey;

	/**
	 * @Column(type="integer")
	 */
	private $ssottl;

	/**
	 * @Column(type="string")
	 */
	private $email;

	public function getId() {
		return $this->id;
	}

	public function setUsername($string) {
		$this->username = $string;
		return true;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setNote($string) {
	  $this->note = $string;
	  return true;
	}

	public function getNote() {
	  return $this->note;
	}

	public function setPassword($string, $encode = true) {
		if ($encode)
			$this->password = sha1 ( $string );
		else
			$this->password = $string;
		return true;
	}

	public function getPassword() {
		return $this->content;
	}

	public function setEmail($string) {
		$this->email = $string;
		return true;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setSsottl($string) {
	  $this->ssottl = $string;
	  return true;
	}

	public function getSsottl() {
	  return $this->ssottl;
	}

	public function setSsokey($string) {
	  $this->ssokey = $string;
	  return true;
	}

	public function getSsokey() {
	  return $this->ssokey;
	}

	public function setActive($boolean) {
	  $this->active = $boolean;
	  return true;
	}

	public function getActive() {
	  return $this->active;
	}

	/**
	 * Get's the roles thie user has
	 * TODO: optimize this function, does not need to have multiple querys
	 */
	public function getRoles() {
		$roles = array();

		$query = Zend_Registry::getInstance ()->entitymanager->createQuery ( 'select ur from Default_Model_UserRole ur WHERE ur.user_id = ?1' );
		$query->setParameter ( 1, $this->getId () );
		$userRoles = $query->getResult ();
		foreach ( $userRoles as $userRole ) {
			$query = Zend_Registry::getInstance ()->entitymanager->createQuery ( 'select r from Default_Model_Role r WHERE r.id = ?1' );
			$query->setParameter ( 1, $userRole->getRoleId () );
			$roleObject = $query->getSingleResult ();
			$roles [$roleObject->getRoleId()] = $roleObject;
		}
		return $roles;
	}

	/**
	 * Returns true if the user has the given role
	 *
	 * @param $roleuid String
	 * @return boolean
	 */
	public function hasRole($roleuid) {
		foreach ( $this->getRoles () as $role ) {
			if (strtolower ( $role->getRoleId () ) == strtolower ( $roleuid ))
				return true;
		}
		return false;
	}

	/**
	 * Get's a User object based on the given Primairy Key
	 *
	 * @param $id Integer
	 */
	public static function getInstanceByPk($id) {
	  return jcms\ModelHelper::getInstanceByPkForModeltype($id, 'Default_Model_User');
	}

	/*
	 * Get's all the executable objecttypes for the current user
	 */
	public static function getAvailableExecObjecttypes() {
		$em = \Zend_Registry::getInstance ()->entitymanager;
		$user = \Zend_Registry::getInstance()->session->user;

		if (! $user) {
			return false;
		}

		$rsm = new \Doctrine\ORM\Query\ResultSetMapping ();
		$rsm->addEntityResult ( 'Default_Model_Objecttype', 'c' );
		$rsm->addFieldResult ( 'c', 'id', 'id' );
		$rsm->addFieldResult ( 'c', 'name', 'name' );
		$rsm->addFieldResult ( 'c', 'icon', 'icon' );
		$rsm->addFieldResult ( 'c', 'description', 'description' );

		$sqlUserCheck = '';
		if($user->hasRole('god')){
			$sql = 'SELECT objecttype.id, objecttype.name, objecttype.description, objecttype.icon FROM objecttype';
		}else{
			$sql = <<<EOT
				SELECT objecttype.id, objecttype.description, objecttype.name, objecttype.icon FROM user
				JOIN user_role on (user_role.user_id = user.id)
				JOIN role on (user_role.role_id = role.id)
				JOIN permission on (permission.role_id = role.id)
				JOIN objecttype on (permission.objecttype_id = objecttype.id)
				WHERE permission.`type` in ('X') AND user.id = ?
EOT;
		}


		$query = $em->createNativeQuery ( $sql, $rsm );
		if(!$user->hasRole('god')) $query->setParameter ( 1, $user->getId () );
		return $query->getResult ();
	}

	/**
	 * Saves object to dtb
	 */
	public function save(){
		Zend_Registry::getInstance()->entitymanager->persist($this);
		Zend_Registry::getInstance()->entitymanager->flush();
	}

	/**
	 * Deletes the user
	 */
	public function delete() {
	  if($this->getUsername() == 'frontend_guest') return false;
	  $query = Zend_Registry::getInstance()->entitymanager->createQuery('delete from Default_Model_User u where u.id = ?1');
	  $query->setParameter(1,$this->getId());
	  return $query->execute();
	}

	/**
	 * Get's all the users
	 */
	public static function getAllUsers() {
		$query = Zend_Registry::getInstance ()->entitymanager->createQuery ( 'select u from Default_Model_User u where u.id != 1 group by u.username' );
		$users = $query->getResult ();
		return $users;
	}

	public function sendActivationMail(){
	  $txt = Default_Model_Content::getInstanceByCode('ACCOUNT_ACTIVATED_EMAIL_TXT');
	  if($txt){
	    $mail = new Zend_Mail();
	    $mail->setBodyText($txt->getContent());
	    $adminEmail = Zend_Registry::getInstance()->settings['site']['adminemail'];
	    $mail->setFrom($adminEmail, Zend_Registry::getInstance()->settings['site']['title']);
	    $mail->addTo($this->getEmail(), 'Admin '.Zend_Registry::getInstance()->settings['site']['title']);
	    $subject = Default_Model_Content::getInstanceByCode('ACCOUNT_ACTIVATED_EMAIL_SUBJECT');
	    if($subject){
	      $mail->setSubject($subject->getContent());
	    }else{
	      $mail = new Zend_Mail();
	      $mail->setBodyText('Just send a activation inform email to user '.$this->getId().' but i could not find the item ACCOUNT_ACTIVATED_MAIL_SUBJECT .');
	      $adminEmail = Zend_Registry::getInstance()->settings['site']['sysadminemail'];
	      $mail->setFrom($adminEmail, Zend_Registry::getInstance()->settings['site']['title']);
	      $mail->addTo($adminEmail, 'Admin '.Zend_Registry::getInstance()->settings['site']['title']);
	      $mail->setSubject('Can\'t give subject to activation email.');
	      $mail->send();
	    }
	    $mail->send();
	  }else{
	    $mail = new Zend_Mail();
	    $mail->setBodyText('Wanted to send a activation inform email to user '.$this->getId().' but i failed because i can\'t find a content with code ACCOUNT_ACTIVATED_MAIL_TXT.');
	    $adminEmail = Zend_Registry::getInstance()->settings['site']['sysadminemail'];
	    $mail->setFrom($adminEmail, Zend_Registry::getInstance()->settings['site']['title']);
	    $mail->addTo($adminEmail, 'Admin '.Zend_Registry::getInstance()->settings['site']['title']);
	    $mail->setSubject('Can\'t send activation email.');
	    $mail->send();
	  }
	}

	/**
	 * Get's a new sso key and reset's the ttl
	 */
	public function getNewSsokey(){
	  $lastweek = mktime(date("H"),date("i"),date("s"),date("n"),date("j"),date("Y")) - 604800;
	  if($lastweek < $this->ssottl){ // within ttl of key
	    $ttl = time();
	    $this->setSsottl($ttl);
	    $this->save();
	  }else{
	    $ttl = time();
	    $key = sha1($this->username.$this->password);
	    $this->setSsokey($key);
	    $this->setSsottl($ttl);
	    $this->save();
	  }


      return $this->ssokey;
	}

	/**
	 * Checks if a given key is correct
	 *
	 * @param string $key
	 */
	public function checkSsoKey($key){
      if($this->ssokey == $key){ // key is correct
        $lastweek = mktime(date("H"),date("i"),date("s"),date("n"),date("j"),date("Y")) - 604800;
        if($lastweek < $this->ssottl){ // within ttl of key
          return true;
        }
      }
      return false;
	}
}