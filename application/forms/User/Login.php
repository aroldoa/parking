<?php
/**
* 
*/
class Form_User_Login extends Zend_Form
{
	
	public function init()
	{
		
		// add path to custom validators & filters
        $this->addElementPrefixPath(
            'Form_Validate',
            APPLICATION_PATH . '/forms/validate/',
            'validate'
        );

		$this->addElement('text', 'email', array(
			'required' 		=> true,
			'label' 		=> 'Email Address',
			'filters' 		=> array('StringTrim'),
			'validators' 	=> array('EmailAddress'),
			'errorMessages' => array('Please enter a valid email address'),
		));
		
		$this->addElement('password', 'password', array(
			'required' 		=> true,
			'label' 		=> 'Password',
			'filters' 		=> array('StringTrim'),
			'errorMessages' => array('Please enter your password'),
		));
		
		$this->addElement('submit', 'submit', array(
			'required' 		=> false,
			'ignore' 		=> true,
			'label' 		=> 'Login',
			'decorators' => array('ViewHelper',array('HtmlTag', array('tag' => 'dd', 'id' => 'form-submit'))),
		));
		$this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend', 'class' => 'errors')),
            'Form'
        ));
	}
}
