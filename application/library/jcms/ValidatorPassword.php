<?php
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
	  // TODO er moet wel uberhoubt 1 wachtwoord aanwezig zijn!


		$request = \Zend_Controller_Front::getInstance()->getRequest();

		if(trim($request->getParam ($this->first, 'foo')) == trim($request->getParam ($this->second, 'bar'))){
			return true;
		}
		$this->_error(self::CHECK_TYPO);
		return false;
	}
}