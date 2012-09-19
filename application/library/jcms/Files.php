<?php
namespace jcms;

class Files {

  public static function saveContentFile(\Default_Model_Objecttypefield $field, \Zend_Form $form, \Default_Model_Content $content) {
    $content->setSort(time());
    $content->save();

    // get setters and getters for dtb field
    $setter = $field->getContentSetterString();
    $getter = $field->getContentGetterString();

    // get the form element
    $formElementName = $field->getDbName();
    $element = $form->$formElementName;

    // get the file info
    $fileInfo = $element->getFileInfo();

    // if($fileInfo[$formElementName]['received']){

    $fileInfo = $fileInfo[$field->getDbName()];

    $farr = explode('.',$fileInfo['name']);
    $extention = $farr[count($farr) - 1];

    // make information array to store
    $infoArray = array();
    $infoArray['name'] = $fileInfo['name'];
    $infoArray['type'] = $extention;
    $infoArray['uid'] = $content->getId() . '_' . date('Ymd') . '_' . uniqid();
    $infoArray['folder'] = str_replace(getcwd(),'',$fileInfo['destination'] . DS);

    // recieve the new file
    if(! $element->receive()){
      throw new \Exception('Can\'t upload file to ' . $infoArray['folder']);
    }

    if($element->isUploaded()){
      $new_name = getcwd() . $infoArray['folder'] . $infoArray['uid'];
      $old_name = getcwd() . $infoArray['folder'] . $fileInfo['name'];

      // move the new file to the not so public location and give it a new name
      if(rename($old_name,$new_name)){

        // if this is not a new file, remove the old file
        if(! is_null($content->$getter())){
          $content->dropFiles();
        }

        $infoArray['batch'] = false;
        $infoArray['mime'] = self::getMimeType($fileInfo['name']);
        // save the new file data
        $content->$setter(json_encode($infoArray));
        $content->save();

      }else{
        // if we can't rename the new file, remove it
        @unlink($fileInfo['destination'] . DS . $fileInfo['name']);
        // throw new \Exception('Can\'t move new file');
      }
    }
    // }
  }

  /**
   * Imports a file to the given parent and set's the correct info with the given info.
   *
   * @param string $folder
   * @param string $filename
   * @param integer $contenttypeId
   * @param string $setter
   * @param string $parent
   */
  public static function saveImportImage($folder, $filename, $contenttypeId, $setter, $parent) {
    $content = new \Default_Model_Content();
    $content->setObjecttypeId($contenttypeId);
    $content->setParent($parent->getId());
    $fileinfo = explode('.',$filename);
    $content->setTitle($fileinfo[0]);

    $content->setSort(time());
    $content->save();

    $content->setUrl($content->getRoutePathString());

    $farr = explode('.',$filename);
    $extention = $farr[count($farr) - 1];

    // make information array to store
    $infoArray = array();
    $infoArray['name'] = $filename;
    $infoArray['type'] = $extention;
    $infoArray['uid'] = $content->getId() . '_' . date('Ymd') . '_' . uniqid();
    $infoArray['folder'] = "\\..\\".\Zend_Registry::getInstance()->settings['jcms']['uploadfolder']."\\";

    $new_name = getcwd() . $infoArray['folder'] . $infoArray['uid'];
    $old_name = getcwd() . "\\..\\".\Zend_Registry::getInstance()->settings['jcms']['importfolder']."\\" . $filename;

    if(file_exists($old_name)){
      // move the new file to the not so public location and give it a new name
      if(rename($old_name,$new_name)){
        $getSizeInfo = getimagesize($new_name);
        $infoArray['mime'] = $getSizeInfo['mime'];
        $infoArray['batch'] = true;

        // save the new file data
        $content->$setter(json_encode($infoArray));
        $content->save();
      }else{
        $content->delete(false);
      }
    }else{
      $content->delete(false);
    }
  }

  /**
   * Saves a file to the correct folder.
   *
   * @param \Default_Model_Objecttypefield $field
   * @param \Zend_Form $form
   * @param Default_Model_Content $content
   * @throws \Exception
   */
  public static function saveContentImage(\Default_Model_Objecttypefield $field, \Zend_Form $form, \Default_Model_Content $content) {
    $content->setSort(time());
    $content->save();

    // get setters and getters for dtb field
    $setter = $field->getContentSetterString();
    $getter = $field->getContentGetterString();

    // get the form element
    $formElementName = $field->getDbName();
    $element = $form->$formElementName;

    // get the file info
    $fileInfo = $element->getFileInfo();

    // if($fileInfo[$formElementName]['received']){

    $fileInfo = $fileInfo[$field->getDbName()];

    $farr = explode('.',$fileInfo['name']);
    $extention = $farr[count($farr) - 1];

    // make information array to store
    $infoArray = array();
    $infoArray['name'] = $fileInfo['name'];
    $infoArray['type'] = $extention;
    $infoArray['uid'] = $content->getId() . '_' . date('Ymd') . '_' . uniqid();
    $infoArray['folder'] = str_replace(getcwd(),'',$fileInfo['destination'] . DS);

    // recieve the new file
    if(! $element->receive()){
      throw new \Exception('Can\'t upload file to ' . $infoArray['folder']);
    }
    if($element->isUploaded()){
      $new_name = getcwd() . $infoArray['folder'] . $infoArray['uid'];
      $old_name = getcwd() . $infoArray['folder'] . $fileInfo['name'];

      // move the new file to the not so public location and give it a new name
      if(rename($old_name,$new_name)){
        // if this is not a new file, remove the old file
        if(! is_null($content->$getter())){
          $content->dropFiles();
        }

        // self::resizeImage($new_name,$new_name .
        // '_thumb',200,200,$infoArray['type'],0);

        $getSizeInfo = getimagesize($new_name);
        $infoArray['mime'] = $getSizeInfo['mime'];
        $infoArray['batch'] = false;

        // save the new file data
        $content->$setter(json_encode($infoArray));
        $content->save();

      }else{
        // if we can't rename the new file, remove it
        @unlink($fileInfo['destination'] . DS . $fileInfo['name']);
        // throw new \Exception('Can\'t move new image');
      }
    }
    // }
  }

  public static function resizeImage($src, $dst, $width, $height, $type, $crop = 0) {
    $type = strtolower($type);

    if(! list($w,$h) = getimagesize($src))
      return false;

    if($type == 'jpeg')
      $type = 'jpg';
    switch ($type) {
      case 'bmp' :
        $img = imagecreatefromwbmp($src);
        break;
      case 'gif' :
        $img = imagecreatefromgif($src);
        break;
      case 'jpg' :
        $img = imagecreatefromjpeg($src);
        break;
      case 'png' :
        $img = imagecreatefrompng($src);
        break;
      default :
        return false;
    }

    // resize
    if($crop){

      if($w < $width){
        $w = $width;
      }
      if($h < $height){
        $h = $height;
      }

      $ratio = max($width / $w,$height / $h);
      $h = $height / $ratio;
      $x = ($w - $width / $ratio) / 2;
      $w = $width / $ratio;

    }else{
      if($w < $width){
        $w = $width;
      }
      if($h < $height){
        $h = $height;
      }
      $ratio = min($width / $w,$height / $h);
      $width = $w * $ratio;
      $height = $h * $ratio;
      $x = 0;
    }

    $new = imagecreatetruecolor($width,$height);

    // preserve transparency
    if($type == "gif" or $type == "png"){
      imagecolortransparent($new,imagecolorallocatealpha($new,0,0,0,127));
      imagealphablending($new,false);
      imagesavealpha($new,true);
    }

    imagecopyresampled($new,$img,0,0,$x,0,$width,$height,$w,$h);

    switch ($type) {
      case 'bmp' :
        imagewbmp($new,$dst);
        break;
      case 'gif' :
        imagegif($new,$dst);
        break;
      case 'jpg' :
        imagejpeg($new,$dst);
        break;
      case 'png' :
        imagepng($new,$dst);
        break;
    }
    return true;
  }

  /**
   * Serves a thumbnail file
   *
   * @param integer $contentId
   * @param string $fieldName
   */
  public static function serveThumb($objectId, $fieldName) {
    self::serveImage($objectId,$fieldName,'_thumb');
  }

  /**
   * Serves an image file
   *
   * @param integer $contentId
   * @param string $fieldName
   */
  public static function serveImage($objectId, $fieldName, $postfix = '') {

    $content = \Default_Model_Content::getInstanceByPk($objectId);

    $field = \Default_Model_Objecttypefield::getInstanceByDBName($fieldName,$content->getObjecttypeId());

    $getter = $field->getContentGetterString();

    $imgInfo = json_decode($content->$getter());

    $location = getcwd() . $imgInfo->folder . $imgInfo->uid . $postfix;

    if(file_exists($location)){
      $contents = file_get_contents($location);
      header('Content-type: image/jpeg');
      echo $contents;
      exit();
    }
  }

  /**
   * Serves a file, returns it when it's a image and serves it as download when
   * it is something else
   *
   * @param integer $objectId
   * @param string $fieldName
   * @param integer $width
   * @param integer $height
   * @param boolean $cropImage
   * @param boolean $download
   *          if possible and is image, serve as download
   */
  public static function serveFile($objectId, $fieldName, $width = null, $height = null, $cropImage = false, $download = false) {

    $content = \Default_Model_Content::getInstanceByPk($objectId);

    $field = \Default_Model_Objecttypefield::getInstanceByDBName($fieldName,$content->getObjecttypeId());

    $getter = $field->getContentGetterString();

    $imgInfo = json_decode($content->$getter());

    $location = getcwd() . $imgInfo->folder . $imgInfo->uid;

    if(file_exists($location)){
      $file = file_get_contents($location);
      if(strstr($imgInfo->mime,'image')){
        if(! is_null($width) && ! is_null($height)){
          $resizedCacheName = $location . '_resized_' . $width . 'x' . $height . '_crop' . $cropImage;
          if(file_exists($resizedCacheName)){
            $file = file_get_contents($resizedCacheName);
          }else{
            self::resizeImage($location,$resizedCacheName,$width,$height,$imgInfo->type,$cropImage);
            $file = file_get_contents($resizedCacheName);
          }
        }
      }

      header('Content-Type: ' . self::getMimeType($imgInfo->name));

      if($download){
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . str_replace(' ','_',$imgInfo->name));
      }

      echo $file;
      exit();
    }
  }

  public static function getMimeType($file) {

    $file = strtolower($file);

    // our list of mime types
    $mime_types = array(
        "pdf"=>"application/pdf",
        "exe"=>"application/octet-stream",
        "zip"=>"application/zip",
        "docx"=>"application/msword",
        "doc"=>"application/msword",
        "xls"=>"application/vnd.ms-excel",
        "ppt"=>"application/vnd.ms-powerpoint",
        "gif"=>"image/gif",
        "png"=>"image/png",
        "jpeg"=>"image/jpg",
        "jpg"=>"image/jpg",
        "mp3"=>"audio/mpeg",
        "wav"=>"audio/x-wav",
        "mpeg"=>"video/mpeg",
        "mpg"=>"video/mpeg",
        "mpe"=>"video/mpeg",
        "mov"=>"video/quicktime",
        "avi"=>"video/x-msvideo",
        "3gp"=>"video/3gpp",
        "css"=>"text/css",
        "jsc"=>"application/javascript",
        "js"=>"application/javascript",
        "php"=>"text/html",
        "htm"=>"text/html",
        "html"=>"text/html",
        "txt"=>"text/plain"
    );

    $extension = explode('.',$file);

    $key = count($extension) - 1;

    if(isset($mime_types[$extension[$key]])){
      return $mime_types[$extension[$key]];
    }else{
      return "text/plain";
    }
  }
}

?>