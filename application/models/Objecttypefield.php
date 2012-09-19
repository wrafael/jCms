<?php
/**
 * @Entity
* @Table(name="objecttypefield")
*/
class Default_Model_Objecttypefield {
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/** @Column(type="integer") */
	private $objecttype_id;

	/** @Column(type="string") */
	private $db_name;

	/** @Column(type="string") */
	private $label;

	// 0 is the field is not in use, other integers are the sort values
	/** @Column(type="integer") */
	private $sort;

	/** @Column(type="string") */
 	private $alternative_type;

	public function setObjecttypeId($int) {
		$this->objecttype_id = $int;
		return true;
	}

	public function getObjecttypeId() {
		return $this->objecttype_id;
	}

	public function setAlternativeType($string) {
		$this->alternative_type = $string;
		return true;
	}

	public function getAlternativeType() {
		return $this->alternative_type;
	}

	public function setDbName($string) {
		$this->db_name = $string;
		return true;
	}

	public function getDbName() {
		return $this->db_name;
	}

	public function setLabel($string) {
		$this->label = $string;
		return true;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setSort($integer) {
		$this->sort = $integer;
		return true;
	}

	public function getSort() {
		return $this->sort;
	}

	public function getId() {
	  return $this->id;
	}

	/**
	 * Get's a Objecttypefiel object based on the given Database name
	 *
	 * @param String $name
	 */
	public static function getInstanceByDBName($name, $objecttypeId){

	  $query = Zend_Registry::getInstance()->entitymanager->createQuery('select o from Default_Model_Objecttypefield o WHERE o.db_name = ?1 and o.objecttype_id = ?2');
	  $query->setParameter(1, $name);
	  $query->setParameter(2, $objecttypeId);
	  $objecttypefield = $query->getSingleResult();

	  return $objecttypefield;
	}

	/**
	 * Get's a Objecttypefiel object based on the given Primairy Key
	 *
	 * @param Integer $id
	 */
	public static function getInstanceByPk($id){
      return jcms\ModelHelper::getInstanceByPkForModeltype($id, 'Default_Model_Objecttypefield');
	}

	/**
	 * Get's some the information on this field from the information schema
	 */
	public function getMetaData(){
		return Zend_Registry::getInstance()->entitymanager->getMetadataFactory()->getMetadataFor('Default_Model_Content')->fieldMappings[$this->db_name];
	}

	/**
	 * Get's the type to use for handling, so checks the alternate type
	 */
	public function getFieldType(){
	  if(trim($this->getAlternativeType()) == ''){
	    $m = $this->getMetaData();
	    return $m['type'];
	  }else{
	    return $this->getAlternativeType();
	  }
	}

	/**
	 * Get's the getter for the FieldDefenitions class so that we can render the correct form field for this data
	 */
	public function getFormGetterString(){
		if(is_null($this->getAlternativeType())){

		    $fields = array_merge(jcms\FieldDefenitions::$fields, jcms\FieldDefenitions::$systemfields);

		    $fieldType = $fields[$this->getDbName()];

			return 'getForm'.str_replace(' ','', ucwords(strtolower(str_replace('_',' ', $fieldType))));
		}
		return 'getForm'.str_replace(' ','', ucwords(strtolower(str_replace('_',' ', $this->getAlternativeType()))));
	}


	public function getContentGetterString(){
		return 'get'.str_replace(' ','', ucwords(strtolower(str_replace('_',' ', $this->getDbName()))));
	}

	public function getContentSetterString(){
		return 'set'.str_replace(' ','', ucwords(strtolower(str_replace('_',' ', $this->getDbName()))));
	}

	/**
	 * Saves object to dtb
	 */
	public function save(){
	  if(Zend_Registry::getInstance()->session->user->hasRole('god')){
	    Zend_Registry::getInstance()->entitymanager->persist($this);
	    Zend_Registry::getInstance()->entitymanager->flush();
	  }
	}

	/**
	 * Deletes the Objecttypefield
	 */
	public function delete() {
	  // TODO als er nog content van dit type is mag dit objecttype niet worden verwijdert
	  if(Zend_Registry::getInstance()->session->user->hasRole('god')){
	    $query = Zend_Registry::getInstance()->entitymanager->createQuery('delete from Default_Model_Objecttypefield o where o.id = ?1');
	    $query->setParameter(1,$this->getId());
	    return $query->execute();
	  }
	}
}
?>