<?php
/**
* 
*/
class Form_Validate_ToDate extends Zend_Validate_Abstract
{
	const INVALID_DATE = 'invalidDate';


    protected $_messageTemplates = array(
        self::INVALID_DATE => 'To date must be later than from date'
    );
	
	public function __construct()
	{
		# code...
	}
	
	public function isValid($value, $context = null)
	{
		$from = strtotime($context['from']);
		$to = strtotime($value);
		
		if ($from >= $to) {
			$this->_error(self::INVALID_DATE);
			return false;
		}
		
		return true;
	}
}
