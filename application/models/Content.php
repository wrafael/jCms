<?php

/**
 * This class represents a Content
 *
 * It extends the Zend_Acl and implements the Zend_Acl_Recource_Interface
 * You can ask this class: $instance->isAllowed('roleid', null, 'permissiontype')
 *
 * Fields need to have a correct and complete set of metadata to work properly in this cms
 * http://docs.doctrine-project.org/projects/doctrine-orm/en/2.0.x/reference/annotations-reference.html
 *
 * @Entity
 * @Table(name="content")
 */
class Default_Model_Content extends Zend_Acl implements Zend_Acl_Resource_Interface {

  private $cache;

  /**
   * @Id @Column(type="integer") @generatedValue(strategy="IDENTITY")
   */
  private $id;

  /**
   * @active @Column(type="boolean", length=1, unique=false, nullable=false)
   */
  private $active;

  /**
   * @sort @Column(type="integer")
   */
  private $sort;

  /**
   * @objecttype_id @Column(type="integer", length=10, unique=false,
   * nullable=false) @ManyToOne(targetEntity="Objecttype")
   * @JoinColumn(name="objecttype_id", referencedColumnName="id")
   */
  private $objecttype_id;

  /**
   * @parent @Column(type="integer", length=10, unique=true, nullable=false)
   * @OneToOne(targetEntity="Content") @JoinColumn(name="parent",
   * referencedColumnName="id")
   */
  private $parent;

  /**
   * @folder @Column(type="boolean", length=1, unique=false, nullable=false)
   */
  private $folder;

  /**
   * @title @Column(type="string", length=100, unique=false, nullable=true)
   */
  private $title;

  /**
   * @url @Column(type="string", length=100, unique=true, nullable=true)
   */
  private $url;

  /**
   * @code @Column(type="string", length=50, unique=true, nullable=true)
   */
  private $code;

  /**
   * @Column(type="text", unique=true, nullable=false)
   */
  private $content;

  // internat array of children of this content
  private $children = null;

  public function setCode($string) {
    $this->code = $string;
    return true;
  }

  public function getCode() {
    return $this->code;
  }

  public function setActive($boolean) {
    $this->active = $boolean;
    return true;
  }

  public function getActive() {
    return $this->active;
  }

  public function setTitle($string) {
    $this->title = $string;
    return true;
  }

  public function getTitle() {
    return $this->title;
  }

  public function setUrl($string) {
    $this->url = $string;
    return true;
  }

  public function getUrl() {
    return $this->url;
  }

  public function setId($int) {
    $this->id = $int;
    return true;
  }

  public function getId() {
    return $this->id;
  }

  public function setContent($string) {
    $this->content = $string;
    return true;
  }

  public function getContent() {
    return $this->content;
  }

  public function setParent($int) {
    $this->parent = $int;
    return true;
  }

  public function getParent() {
    return $this->parent;
  }

  public function setSort($int) {
    $this->sort = $int;
    return true;
  }

  public function getSort() {
    return $this->sort;
  }

  public function setFolder($int) {
    $this->folder = $int;
    return true;
  }

  public function getFolder() {
    return $this->folder;
  }

  public function setObjecttypeId($id) {
    $this->objecttype_id = $id;
    $this->setACL(); // only if we know what objectype we are, we can set the
                     // ACL
    return true;
  }

  public function setObjecttype($objecttype) {
    $this->objecttype_id = $objecttype->getId();
    $this->setACL(); // only if we know what objectype we are, we can set the
                     // ACL
    return true;
  }

  public function getObjecttypeId() {
    return $this->objecttype_id;
  }

  /**
   * Adds a child to the internal array of children
   *
   * @param Default_Model_Content $child
   */
  public function addChild(Default_Model_Content $child) {
    $this->children[$child->getId()] = $child;
  }

  /**
   * Returns the Objecttype object
   *
   * @param Default_Model_Objecttype $permission
   */
  public function getObjecttype() {
    return Default_Model_Objecttype::getInstanceByPk($this->getObjecttypeId());
  }

  // implements Zend_Acl_Resource_Interface
  public function getResourceId() {
    return 'CT' . $this->getObjecttypeId();
  }

  /**
   * Determs if the Content is allowed to the specific permission for a given
   * user
   *
   * @param Default_Model_User $user
   * @param unknown_type $permission
   */
  public function isAllowedByUser(Default_Model_User $user = null, $permission = null) {
    //if($this->active){

      if(is_null($user))
        $user = \Zend_Registry::getInstance()->session->user;



      if(is_null($permission))
        $permission = 'R';

      $roles = $user->getRoles();

      foreach($roles as $roleuid=>$role){
        if(parent::isAllowed($role,$this,$permission)){
          return true;
        }
      }
    //}
    return false;
  }

  /**
   * Get's all the children the current user has rights on
   *
   */
  public function getChildren($limit = 10000) {
    if(is_null($this->children)){
      $query = Zend_Registry::getInstance()->entitymanager->createQuery('select c from Default_Model_Content c WHERE c.parent = ?1 order by c.sort');
      $query->setParameter(1,$this->getId());
      $children = $contents = $query->getResult();

      foreach($children as $child){
        $child->setACL();
      }

      $this->children = array();
      $count = 1;
      foreach($children as $child){
        if($child->isAllowedByUser()){
          if($count > $limit) continue;
          $this->children[] = $child;
          $count++;
        }
      }
    }

    return $this->children;
  }

  /**
   * Gives the first child of this item
   */
  public function getFirstChild(){
    $child = $this->getChildren(1);
    return array_shift($child);
  }

  /**
   * Get's all the children, not only the ones on the first level
   */
  public function getAllChildren() {
    $children = $this->getChildren();

    foreach($children as $child){
      $children = array_merge($child->getAllChildren(),$children);
    }

    return $children;
  }

  /**
   * Get's a unique id string for the users permissions
   *
   * @return string
   */
  public function getPermissionsString() {
    $permissions = '';
    if($this->isAllowedByUser(null,Default_Model_Permission::PERMISSION_EDIT))
      $permissions .= Default_Model_Permission::PERMISSION_EDIT;
    if($this->isAllowedByUser(null,Default_Model_Permission::PERMISSION_EXECUTE))
      $permissions .= Default_Model_Permission::PERMISSION_EXECUTE;
    if($this->isAllowedByUser(null,Default_Model_Permission::PERMISSION_READ))
      $permissions .= Default_Model_Permission::PERMISSION_READ;
    if($this->isAllowedByUser(null,Default_Model_Permission::PERMISSION_ADDCHILD))
      $permissions .= Default_Model_Permission::PERMISSION_ADDCHILD;
    if($this->isAllowedByUser(null,Default_Model_Permission::PERMISSION_MOVE))
      $permissions .= Default_Model_Permission::PERMISSION_MOVE;
    return $permissions;
  }

  /**
   * Fills the Access Control List
   */
  public function setACL() {
   // if($this->active){
      $cache = Zend_Registry::getInstance()->cache;
      $em = Zend_Registry::getInstance()->entitymanager;
      // Setup the Access Control List for this Content
      $roles = array();
      $roleObjects = array(); // we need a array with the role id's s key in the
                              // last foreach within this function
      $permissions = array();

      if(! parent::has($this))
        parent::addResource($this);

        // Get the Roles and add them to the ACL
      if(($roles = $cache->load('roles')) === false){
        $query = $em->createQuery('select r from Default_Model_Role r');
        $roles = $query->getResult();
        $cache->save($roles,'roles');
      }

      foreach($roles as $role){
        $roleObjects[$role->getId()] = $role;
        if(! parent::hasRole($role))
          parent::addRole($role);
      }
      if(! parent::hasRole($role))
        parent::addRole(new Zend_Acl_Role(Default_Model_Role::GOD_ROLE));

        // Get the permissions and add them to the ACL
      $query = $em->createQuery('select p from Default_Model_Permission p where p.objecttype_id = ?1');
      $query->setParameter(1,$this->getObjecttypeId());
      $permissions = $query->getResult();

      if($this->active || is_null($this->id)){
        foreach($permissions as $permission){
          switch ($permission->getType()) {
            case Default_Model_Permission::PERMISSION_READ :
              // echo 'allow R<br />';
              parent::allow($roleObjects[$permission->getRoleId()],$this->getResourceId(),Default_Model_Permission::PERMISSION_READ);
              break;
            case Default_Model_Permission::PERMISSION_EDIT :
              // echo 'allow E<br />';
              parent::allow($roleObjects[$permission->getRoleId()],$this->getResourceId(),Default_Model_Permission::PERMISSION_EDIT);
              parent::allow($roleObjects[$permission->getRoleId()],$this->getResourceId(),Default_Model_Permission::PERMISSION_READ);
              break;
            case Default_Model_Permission::PERMISSION_EXECUTE :
              // echo "allows {Default_Model_Permission::PERMISSION_EDIT}<br />";
              parent::allow($roleObjects[$permission->getRoleId()],$this->getResourceId(),Default_Model_Permission::PERMISSION_EDIT);
              parent::allow($roleObjects[$permission->getRoleId()],$this->getResourceId(),Default_Model_Permission::PERMISSION_READ);
              parent::allow($roleObjects[$permission->getRoleId()],$this->getResourceId(),Default_Model_Permission::PERMISSION_EXECUTE);
              break;
            case Default_Model_Permission::PERMISSION_ADDCHILD :
              parent::allow($roleObjects[$permission->getRoleId()],$this->getResourceId(),Default_Model_Permission::PERMISSION_ADDCHILD);
              break;
            case Default_Model_Permission::PERMISSION_MOVE :
              parent::allow($roleObjects[$permission->getRoleId()],$this->getResourceId(),Default_Model_Permission::PERMISSION_MOVE);
              break;
          }
        }

        parent::allow(new Zend_Acl_Role(Default_Model_Role::GOD_ROLE),$this->getResourceId(),Default_Model_Permission::PERMISSION_EXECUTE);
        parent::allow(new Zend_Acl_Role(Default_Model_Role::GOD_ROLE),$this->getResourceId(),Default_Model_Permission::PERMISSION_READ);
        parent::allow(new Zend_Acl_Role(Default_Model_Role::GOD_ROLE),$this->getResourceId(),Default_Model_Permission::PERMISSION_EDIT);
        parent::allow(new Zend_Acl_Role(Default_Model_Role::GOD_ROLE),$this->getResourceId(),Default_Model_Permission::PERMISSION_ADDCHILD);
        parent::allow(new Zend_Acl_Role(Default_Model_Role::GOD_ROLE),$this->getResourceId(),Default_Model_Permission::PERMISSION_MOVE);
    }
    //}
  }

  /**
   * Get's a Content object based on the given Primairy Key
   *
   * @param Integer $id
   * @return Default_Model_Content
   */
  public static function getInstanceByPk($id) {
    return jcms\ModelHelper::getInstanceByPkForModeltype($id, 'Default_Model_Content');
  }

  /**
   * Get's a Content object based on the given Code
   *
   * @param
   *          String Code
   * @return Default_Model_Content
   */
  public static function getInstanceByCode($code, $ignoreACL = false) {
    $query = Zend_Registry::getInstance()->entitymanager->createQuery('select c from Default_Model_Content c WHERE c.code = ?1');
    $query->setParameter(1,$code);
    try{
      $content = $query->getSingleResult();
    }catch(Exception $e){
      header('HTTP/1.1 404 Not Found');
      return false;
    }

    if($ignoreACL) return $content;

    $content->setACL();

    if($content->isAllowedByUser()){
      return $content;
    }else{
      header('HTTP/1.1 403 Forbidden');
      return false;
    }
  }

  /**
   * Fills the sort value based on the latest id + 1
   *
   * @param integer $id
   * @param boolean $save
   *          if this function will save the object to the database
   */
  public function setSortValue($save = false) {
    $qb = Zend_Registry::getInstance()->entitymanager->createQueryBuilder();

    $qb->select('c')->from('Default_Model_Content','c')->orderBy('c.id','DESC')->setMaxResults(1);

    $query = $qb->getQuery();

    $content = $query->getSingleResult();

    $sort = $content->getId();
    $this->setSort(++ $sort);
    if($save)
      $this->save();
  }

  /**
   * Removes this object and children from the database, if the user is allowed.
   * By default, checks if the user is allowed to remove the children
   *
   * @param boolean $checkChildren
   * @return boolean
   */
  public function delete($checkChildren = true) {
    if($this->isAllowedByUser(null,Default_Model_Permission::PERMISSION_EXECUTE)){

      // check if i'm allowed to delete all children
      if($checkChildren){
        foreach($this->getAllChildren() as $child){
          if(! $child->isAllowedByUser(null,Default_Model_Permission::PERMISSION_EXECUTE)){
            return false;
          }
        }
      }

      foreach($this->getAllChildren() as $child){ // let's force choke them all
        $child->delete(false);
      }

      $this->dropFiles();

      $this->setUrl(null);
      $this->setParent(null);
      $this->setCode(null);
      $this->setActive(false);
      Zend_Registry::getInstance()->entitymanager->persist($this);
      Zend_Registry::getInstance()->entitymanager->flush();
      return true;
    }
    return false;
  }

  /**
   * Unlinks all the related files of the object
   */
  public function dropFiles(){
    $folder = str_replace(DS.Zend_Registry::getInstance()->settings['jcms']['wwwfolder'],'',getcwd()).DS.Zend_Registry::getInstance()->settings['jcms']['uploadfolder'];
    if ($handle = opendir($folder)) {
      while (false !== ($entry = readdir($handle))) {
        $info = explode('_',$entry);
        if($info[0] == $this->getId()){
          unlink($folder.DS.$entry);
        }
      }
      closedir($handle);
    }
  }

  /**
   * Saves object to dtb, also makes it active
   *
   * @param boolean $ignoreACL
   * @throws \Zend_Exception
   */
  public function save($ignoreACL = false) {
    if($this->isAllowedByUser(null,Default_Model_Permission::PERMISSION_EDIT) || $ignoreACL){
      $this->setActive(true);
      \Zend_Registry::getInstance()->entitymanager->persist($this);
      \Zend_Registry::getInstance()->entitymanager->flush();
    }else{
      throw new \Zend_Exception('not allowed');
    }
  }

  /**
   * Get's the complete route path string, by calling jcms\Route::getpath
   */
  public function getRoutePathString(){
    $returnString = '';

    $startLevel = 999;

    foreach(jcms\Route::getPath($this) as $level=>$object){

      if(strtoupper($object->getCode()) == 'CONTENT') $startLevel = $level;

      if($level > $startLevel){
        // replace everything that is not letter with an _
        $newUrl = preg_replace ('/[^A-Za-z0-9]/', '_', $object->getTitle());

        $returnString .= '/' . strtolower($newUrl);

        if($object->getId() == $this->getId()){
          return $returnString;
        }
      }
    }

//TODO check if url is in dtb,,.
    //$qb->select('c')->from('Default_Model_Content','c')->where("c.url == '{$returnString}'")->setMaxResults(1);
    //$query = $qb->getQuery();
    //$content = $query->getSingleResult();


    return $returnString;
  }

  // All the following are getters and setters for the custom content fields
  // Add as many as you like, and don't forget to add a form defenition function
  // for the backend and add the db_name to the array in jcms\FieldDefenition

  /**
   * @integer1 @Column(type="integer")
   */
  private $integer1;

  public function setInteger1($int) {
    $this->integer1 = $int;
    return true;
  }

  public function getInteger1() {
    return $this->integer1;
  }

  /**
   * @integer2 @Column(type="integer")
   */
  private $integer2;

  public function setInteger2($int) {
    $this->integer2 = $int;
    return true;
  }

  public function getInteger2() {
    return $this->integer2;
  }

  /**
   * @integer3 @Column(type="integer")
   */
  private $integer3;

  public function setInteger3($int) {
    $this->integer3 = $int;
    return true;
  }

  public function getInteger3() {
    return $this->integer3;
  }

  /**
   * @string1 @Column(type="string")
   */
  private $string1;

  public function setString1($string) {
    $this->string1 = $string;
    return true;
  }

  public function getString1() {
    return $this->string1;
  }

  /**
   * @string2 @Column(type="string")
   */
  private $string2;

  public function setString2($string) {
    $this->string2 = $string;
    return true;
  }

  public function getString2() {
    return $this->string2;
  }

  /**
   * @string3 @Column(type="string")
   */
  private $string3;

  public function setString3($string) {
    $this->string3 = $string;
    return true;
  }

  public function getString3() {
    return $this->string3;
  }

  /**
   * @datetime1 @Column(type="string")
   */
  private $datetime1;

  public function setDatetime1($datetime) {
    $timestamp = strtotime($datetime);
    $this->datetime1 = date('Y-m-d H:i:00',$timestamp);
    return true;
  }

  public function getDatetime1($format = 'd-m-Y H:i') {

    if(is_null($this->datetime1)){
      return false;
    }

    $timestamp = strtotime($this->datetime1);
    return date($format, $timestamp);
  }

  /**
   * @datetime2 @Column(type="string")
   */
  private $datetime2;

  public function setDatetime2($datetime) {
    $timestamp = strtotime($datetime);
    $this->datetime2 = date('Y-m-d H:i:00',$timestamp);
    return true;
  }

  public function getDatetime2($format = 'd-m-Y H:i') {

    if(is_null($this->datetime2)){
      return false;
    }

    $timestamp = strtotime($this->datetime2);
    return date($format, $timestamp);
  }

  /**
   * @datetime3 @Column(type="string")
   */
  private $datetime3;

  public function setDatetime3($datetime) {
    $timestamp = strtotime($datetime);
    $this->datetime3 = date('Y-m-d H:i:00',$timestamp);
    return true;
  }

  public function getDatetime3() {

    if(is_null($this->datetime3)){
      return false;
    }

    $timestamp = strtotime($this->datetime3);
    return date('d-m-Y H:i', $timestamp);
  }

  /**
   * @Column(type="text")
   */
  private $text1;

  public function setText1($string) {
    $this->text1 = $string;
    return true;
  }

  public function getText1() {
    return $this->text1;
  }
}