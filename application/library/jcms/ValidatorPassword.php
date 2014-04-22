<?php
/**
 * Custom validator for password based on the Zend validator
 */
namespace jcms;

class ValidatorPassword extends \Zend_Validate_Abstract
{
	const CHECK_TYPO = 'VALIDATOR_PASSWORD_TYPO';

	protected $_messageTemplates = array(
	    self::CHECK_TYPO => "Typing error on this field"
	);

	private $first = '';
	private $second = '';

	/**
	 *
	 * @param String First form field with password
	 * @param String Second form field with password
	 */
	public function __construct($first, $second)
	{
		$this->first = $first;
		$this->second = $second;
	}

	public function isValid($value)
	{
		$request = \Zend_Controller_Front::getInstance()->getRequest();

		$pw1 = trim($request->getParam ($this->first, 'foo'));
		$pw2 = trim($request->getParam ($this->second, 'foo'));
		
		if($pw1 == '' && $pw2 == ''){
			return true;
		}
		
		if($pw1 == $pw2){
			return true;
		}

		$this->_error(self::CHECK_TYPO);
		return false;
	}
}