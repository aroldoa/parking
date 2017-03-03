<?php
class Form_Validate_PasswordConfirm extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'noMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Passwords do not match'
    );

	public function __construct()
	{
		
	}

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);
		// throw new Exception('right here');
        if (is_array($context)) {
            if (isset($context['confirm'])
                && ($value == $context['confirm']))
            {
				
                return true;
            }
        } elseif (is_string($context) && ($value == $context)) {
	
            return true;
        }

        $this->_error(self::NOT_MATCH);
        return false;
    }
}
