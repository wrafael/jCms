<?php

class ErrorController extends Zend_Controller_Action {

  public function errorAction() {
    $this->layout = Zend_Layout::getMvcInstance();
    $this->layout->setLayout('error');
    $this->_helper->viewRenderer('objecttypetemplates/error', null, true);

    $errors = $this->_getParam('error_handler');
    $params = serialize($errors['request']->getParams());
    $message = $errors['exception']->getMessage();
    $html = <<<EOT
    {$message}
    {$params}
EOT;
    $mail = new Zend_Mail();
    $mail->setBodyText($html);
    $mail->setFrom('jcms@jcms.nl', 'jcms');
    $mail->addTo(Zend_Registry::getInstance()->settings['site']['sysadminemail'], 'Admin jcms');
    $mail->setSubject(jcms\ErrorHelper::getRandomQuote());
    $mail->send();

    $user = Zend_Registry::getInstance()->session->user;

//     if(isset($user) && ($user->hasRole('Admin') || $user->hasRole('God'))){
//       $this->view->error = $errors;
//     }else{
      if(Zend_Registry::getInstance()->settings['site']['showerror']){
        $this->view->error = $errors;
      }else{
        header('location: '.Zend_Registry::getInstance()->settings['site']['error']);
      }
//     }
  }
}