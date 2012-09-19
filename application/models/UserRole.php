<?php
/**
 * @Entity
 * @Table(name="user_role")
 */
class Default_Model_UserRole {

	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/** @Column(type="integer") */
	private $role_id;

	/** @Column(type="integer") */
	private $user_id;

	public function setUserId($id) {
		$this->user_id = $id;
		return true;
	}

	public function getUserId() {
		return $this->user_id;
	}

	public function setRoleId($id) {
		$this->role_id = $id;
		return true;
	}

	public function getRoleId() {
		return $this->role_id;
	}

	/**
	 * Saves object to dtb
	 */
	public function save(){
	  Zend_Registry::getInstance()->entitymanager->persist($this);
	  Zend_Registry::getInstance()->entitymanager->flush();
	}
}

?>