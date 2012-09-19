<?php
/**
 * @Entity
 * @Table(name="permission")
 */
class Default_Model_Permission {

	const PERMISSION_READ = 'R'; // read the information
	const PERMISSION_EDIT = 'W'; // edit the item
	const PERMISSION_EXECUTE = 'X'; // remove & add item etc...
	const PERMISSION_ADDCHILD = 'A'; // add children
	const PERMISSION_MOVE = 'M'; // move children

	private static $permissions;

	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @Column(type="string")
	 */
	private $type;

	/**
	 * @Column(type="integer")
	 */
	private $role_id;

	/**
	 * @Column(type="integer")
	 */
	private $objecttype_id;

	public function setObjecttypeId($id) {
		$this->objecttype_id = $id;
		return true;
	}

	public function getObjecttypeId() {
		return $this->objecttype_id;
	}

	public function setRoleId($id) {
		$this->role_id = $id;
		return true;
	}

	public function getRoleId() {
		return $this->role_id;
	}

	public function setType($string) {
		if (in_array ( $string, array (self::PERMISSION_READ, self::PERMISSION_EDIT, self::PERMISSION_EXECUTE, self::PERMISSION_ADDCHILD, self::PERMISSION_MOVE ) )) {
			$this->type = $string;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get's all the permissions set for a given role
	 *
	 * @param Default_Model_Role $role
	 */
	public static function getPermissionsForRole($role){
	  if(!isset(Default_Model_Permission::$permissions[$role->getId()])){
  	    $query = Zend_Registry::getInstance()->entitymanager->createQuery('select p from Default_Model_Permission p where p.role_id = ?1');
  	    $query->setParameter(1, $role->getId());
  	    $ps = $query->getResult();
  	    $perArray = array();
  	    foreach($ps as $p){
  	      $perArray[$p->getObjecttypeId()][strtoupper($p->getType())] = true;
  	    }
  	    Default_Model_Permission::$permissions[$role->getId()] = $perArray;
	  }
	  return Default_Model_Permission::$permissions[$role->getId()];
	}

	public function getType() {
		return $this->type;
	}
}

?>