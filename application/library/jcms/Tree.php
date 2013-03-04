<?php
namespace jcms;

class Tree
{
	private static $treeObject;

	public $objects = null;


	/**
	 * Retrieves the default registry instance.
	 *
	 * @return Zend_Registry
	 */
	public static function getInstance()
	{
		if (self::$treeObject === null) {
			self::init();
		}

		return self::$treeObject;
	}

	public static function init(){
		self::$treeObject = new Tree();
		self::$treeObject->getContent();
	}

	/**
	 * Fills the tree with all the content
	 */
	private function getContent(){
		$em = \Zend_Registry::getInstance ()->entitymanager;
		$user = \Zend_Registry::getInstance()->session->user;

		if(!$user){
			return false;
		}

		$q_str = "SELECT ur FROM Default_Model_UserRole ur JOIN ur.user_id u JOIN ur.role_id r WHERE u.id = 1";

		$rsm = new \Doctrine\ORM\Query\ResultSetMapping;
		$rsm->addEntityResult('Default_Model_Content', 'c');
		$rsm->addFieldResult('c', 'id', 'id');
		$rsm->addFieldResult('c', 'parent', 'parent');
		$rsm->addFieldResult('c', 'url', 'url');
		$rsm->addFieldResult('c', 'sort', 'sort');
		$rsm->addFieldResult('c', 'folder', 'folder');
		$rsm->addFieldResult('c', 'objecttype_id', 'objecttype_id');
		$rsm->addFieldResult('c', 'code', 'code');
		$rsm->addFieldResult('c', 'title', 'title');
		$rsm->addFieldResult('c', 'active', 'active');
		$addToQuery = '';
		foreach(FieldDefenitions::$fields as $field=>$type){
			$addToQuery .= 'content.'.$field.', ';
			$rsm->addFieldResult('c', $field, $field);
		}

		// get all the content the user has rights for
		if($user->hasRole('God')){
			$sql = 'SELECT '.$addToQuery.' content.active, content.id, content.url, content.parent, content.folder as folder, content.objecttype_id, content.code, content.title, content.sort from content where content.active = 1 order by content.sort';
		}else{
			$sql = <<<EOT
			SELECT {$addToQuery} content.active, content.url, content.content, content.id, content.parent, content.folder as folder, content.objecttype_id, content.code, content.title, content.sort FROM user
			JOIN user_role on (user_role.user_id = user.id)
			JOIN role on (user_role.role_id = role.id)
			JOIN permission on (permission.role_id = role.id)
			JOIN objecttype on (permission.objecttype_id = objecttype.id)
			JOIN content on (objecttype.id = content.objecttype_id)
			WHERE permission.`type` in ('R','W','X') AND user.id = ? AND content.active = 1 order by content.sort
EOT;
		}

		$query = $em->createNativeQuery($sql, $rsm);
		$query->setParameter(1, $user->getId());
		$contents = $query->getResult();

		//get an array with object id as key
		$structuredTrees = array();
		foreach($contents as $content){
			$content->setACL();
			if($content->isAllowedByUser()) $structuredTrees[$content->getId()] = $content;
		}

		// fill all the children arrays of the objects
		foreach($structuredTrees as $key=>$structuredContent){
			foreach($contents as $content){
				if($content->getParent() == $structuredContent->getId()) $structuredContent->addChild($content);
			}
		}

		$this->objects = $structuredTrees;
	}

	/**
	 * Returns a Json encoded tree structure
	 */
	public function getAsJson(){
		return json_encode($this->getTree());
	}

	/**
	 * Returns a tree structure in a array
	 */
	public function getTree(){
		if(is_null($this->objects)){
			$this->getContent();
		}

		$array = array();
		$this->addToArray($array, $this->objects[1]);

		return $array;
	}

	/**
	 * Get's a part of the tree based on a parent code
	 *
	 * @param String $code
	 */
	public function getPartByParentCode($code){
	  if(is_null($this->objects)){
	    $this->getContent();
	  }

	  foreach($this->objects as $optionalParent){
	    if($optionalParent->getCode() == $code){
	      $parent = $optionalParent;
	    }
	  }

		$array = array();
		$this->addToArray($array, $parent);

		return $array;
	}


	/**
	 * Put's the children of a content in a array
	 *
	 * @param Array $inputArray
	 * @param \Default_Model_Content $content
	 */
	private function addToArray(&$inputArray, \Default_Model_Content $content){

		$array = array('code'=>$content->getCode(), 'objecttype'=>$content->getObjecttype()->getName(), 'url'=>$content->getUrl(),'sort'=>$content->getSort(), 'icon'=>$content->getObjecttype()->getIcon(),'label'=>$content->getTitle(), 'id'=>$content->getId(), 'parent'=>$content->getParent(), 'permissions'=>$content->getPermissionsString());

		if (count ( $content->getChildren () ) > 0) {
			$newArray = array();

			foreach ( $this->getChildrenOf($content->getId()) as $child ) {
				if($child->isAllowedByUser()) $this->addToArray($newArray, $child);
			}
			$array['children'] = $newArray;
		}
		$inputArray[] = $array;
	}

	/**
	 * Get's the children of a specific item
	 *
	 * @param integer $id
	 */
	public function getChildrenOf($id, $bySortKey = false){
		if(is_null($this->objects)){
			$this->getContent();
		}

		$childs = array();
		foreach($this->objects as $object){
			if($object->getParent() == $id){
				if($bySortKey){
					$childs[$object->getSort()] = $object;
				}else{
					$childs[$object->getId()] = $object;
				}
			}
		}

		if($bySortKey) ksort($childs);

		return $childs;
	}




}