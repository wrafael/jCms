<?php
/**
 * Function holder for Model related functions
 */
namespace jcms;

class ModelHelper
{
  /**
   * Get's a object based on the given Primairy Key and Modeltype
   *
   * @param integer $id
   * @param string $modeltype like Default_Model_Content
   * @throws Exception
   */
  public static function getInstanceByPkForModeltype($id, $modeltype) {

    switch($modeltype){
      case 'Default_Model_Role':
        // is we query a role, don't let anybody else then god query the system roles
        if(\Zend_Registry::getInstance()->session->user->hasRole('god')){
          $query = \Zend_Registry::getInstance()->entitymanager->createQuery('select r from Default_Model_Role r WHERE r.id = ?1');
        }else{
          $query = \Zend_Registry::getInstance()->entitymanager->createQuery('select r from Default_Model_Role r WHERE r.id = ?1 and r.name not in (`God`,`Admin`,`Frontend`,`Backend`)');
        }
        break;
      default:
        $query = \Zend_Registry::getInstance()->entitymanager->createQuery('select m from '.$modeltype.' m WHERE m.id = ?1');
        break;
    }

    $query->setParameter(1,$id);
    try{
      $object = $query->getSingleResult();
      
      // if object is of content, let's check the rights
      if($object instanceof \Default_Model_Content){
        $object->setACL();

        if($object->isAllowedByUser()){
          return $object;
        }else{
          header('HTTP/1.1 403 Forbidden');
          return false;
        }
      }else if(!$object){
      	header('HTTP/1.1 404 Not Found');
        return false;
      }else{
      	return $object;
      }
    }catch (\Doctrine\ORM\NoResultException $e){
      return false;
    }catch (\Exception $e){
      throw $e;
    }
  }
}