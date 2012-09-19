<?php
namespace jcms;

class Route {
  /**
   * Get's the complete route path in an array with objects, keys are the level, user needs acces to the objects to get them.
   */
  public static function getPath(\Default_Model_Content $content){
    $route = array();

    $route[] = $content;

    if($content->getId() == 1) return $route;

    $parent = \Default_Model_Content::getInstanceByPk($content->getParent());
    if($parent){
      $route[] = $parent;

      while($parent->getId() != 1){
        $parent = \Default_Model_Content::getInstanceByPk($parent->getParent());
        if($parent){
          $route[] = $parent;
        }else{
          break;
        }
      }
    }

    return array_reverse($route);
  }

  /**
   * Get's a Id of an object based on the given Url, !No rights are checked in this function!
   *
   * @param string $url
   * @throws Exception
   * @return array with id & objecttypecode of content
   */
  public static function getRouteInfoByUrl($url) {
    $query = \Zend_Registry::getInstance()->entitymanager->createQuery('select c from Default_Model_Content c WHERE c.url = ?1');
    $query->setParameter(1,$url);
    try{
      $object = $query->getSingleResult();

      return array('id'=>$object->getId(), 'objecttypecode'=>$object->getObjecttype()->getCode());

      return $object;
    }catch (\Doctrine\ORM\NoResultException $e){
      return false;
    }catch (\Exception $e){
      throw $e;
    }
    return false;
  }
}

?>