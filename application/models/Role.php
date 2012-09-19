<?php

/**
 * @Entity
 * @Table(name="role")
 */
use jcms\PDOHelper;

class Default_Model_Role implements Zend_Acl_Role_Interface {

  const GOD_ROLE = 'God';

  /**
   * @Id @Column(type="integer")
   * @GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @Column(type="string")
   */
  private $name;

  public function getId() {
    return $this->id;
  }

  public function setName($string) {
    $this->name = $string;
    return true;
  }

  public function getName() {
    return $this->name;
  }

  // implements Zend_Acl_Role_Interface
  public function getRoleId() {
    return $this->name;
  }

  /**
   * Get's all the users of the current role
   */
  public function getUsers() {
    $conn = PDOHelper::getConnection();

    $sql = 'select user.id as user_id from user join user_role on (user.id = user_role.user_id) join role on (user_role.role_id = role.id) where role.id = '.$this->id;

    $users = array();
    foreach ($conn->query($sql) as $row) {
      $users[] = Default_Model_User::getInstanceByPk($row['user_id']);
    }
    return $users;
  }

  /**
   * Get's all the roles
   */
  public static function getAllRoles() {

    $query = Zend_Registry::getInstance()->entitymanager->createQuery('select r from Default_Model_Role r where r.id != 1 group by r.name');

    return $query->getResult();
  }

  /**
   * Get's the roles except for the system roles
   */
  public static function getRoles() {

    $query = Zend_Registry::getInstance()->entitymanager->createQuery("select r from Default_Model_Role r where r.id != 1 and r.name not in ('God','Admin','Backend', 'Frontend') group by r.name");

    return $query->getResult();
  }



  /**
   * Get's a Role object based on the given Primairy Key
   *
   * @param $id Integer
   */
  public static function getInstanceByPk($id) {
    return jcms\ModelHelper::getInstanceByPkForModeltype($id, 'Default_Model_Role');
  }

  /**
   * Saves object to dtb
   */
  public function save() {
    Zend_Registry::getInstance()->entitymanager->persist($this);
    Zend_Registry::getInstance()->entitymanager->flush();
  }

  /**
   * Returns true if the role can be changed
   */
  public function canEdit(){
    if(strtolower($this->getName()) == 'god'){
      return false;
    }
    if(strtolower($this->getName()) == 'admin'){
      if(!Zend_Registry::getInstance()->session->user->hasRole('god')){
        return false;
      }
    }
    if(strtolower($this->getName()) == 'backend'){
      if(!Zend_Registry::getInstance()->session->user->hasRole('god')){
        return false;
      }
    }
    if(strtolower($this->getName()) == 'frontend'){
      if(!Zend_Registry::getInstance()->session->user->hasRole('god')){
        return false;
      }
    }
    return true;
  }

  /**
   * Returns true if the role can be deleted
   */
  public function canDelete(){
    if(strtolower($this->getName()) == 'god'){
      return false;
    }
    if(strtolower($this->getName()) == 'admin'){
      return false;
    }
    if(strtolower($this->getName()) == 'backend'){
      return false;
    }
    if(strtolower($this->getName()) == 'frontend'){
      return false;
    }
    return true;
  }

  /**
   * Deletes the role
   */
  public function delete() {
    if($this->canEdit()){
      $query = Zend_Registry::getInstance()->entitymanager->createQuery('delete from Default_Model_Role r where r.id = ?1');
      $query->setParameter(1,$this->getId());
      return $query->execute();
    }
    return false;
  }
}