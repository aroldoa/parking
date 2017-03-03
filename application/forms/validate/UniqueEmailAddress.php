<?php
class Form_Validate_UniqueEmailAddress extends Zend_Validate_Abstract
{
    const EMAIL_EXISTS = 'usernameExists';
	CONST EMAIL = 'invalidEmail';

    protected $_messageTemplates = array(
        self::EMAIL_EXISTS => 'Email address "%value%" already in use',
		self::EMAIL => 	'Please enter a valid email address'
    );

    protected $_model;
    protected $_method;

    public function __construct()
    {
        $this->_model  = new Model_User();
        $this->_method = 'getUserByEmail';

        if (!method_exists($this->_model, $this->_method)) {
            throw new My_Exception('Method ' . $method . 'does not exist in model');
        }
    }

    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
		
		$email = new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_LOCAL);
		
		if (!$email->isValid($value)) {
			$this->_error(self::EMAIL);
			return false;
		}
		
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		
		if (isset($identity)) {
			if ($value == $identity->email) {
				return true;
			}
		}
		
		if (isset($context['id']) && $context['id'] != '') {
			$user = $this->_model->getUserById($context['id']);
			if ($user->email == $value) {
				return true;
			}
		}
		
		
        $found = call_user_func(array($this->_model, $this->_method), $value);
        
        if (null == $found) {
            return true;
        }

        $this->_error(self::EMAIL_EXISTS);
        return false;
    }
}