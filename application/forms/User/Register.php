<?php
/**
* 
*/
class Form_User_Register extends Zend_Form
{
	
	public function init()
	{
		$this->setAction('/account/register');
		$this->setMethod('post');
		
		// add path to custom validators & filters
        $this->addElementPrefixPath(
            'Form_Validate',
            APPLICATION_PATH . '/forms/validate/',
            'validate'
        );

		$this->addElement('text', 'first_name', array(
			'required' 		=> true,
			'label' 		=> 'First Name',
			'filters' 		=> array('StringTrim'),
			'errorMessages' => array('Please enter your first name'),
		));
		
		$this->addElement('text', 'last_name', array(
			'required' 		=> true,
			'label' 		=> 'Last Name',
			'filters' 		=> array('StringTrim'),
			'errorMessages' => array('Please enter your last name'),
		));
		
		$this->addElement('text', 'email', array(
			'required' 		=> true,
			'label' 		=> 'Email Address',
			'filters' 		=> array('StringTrim'),
			'validators' 	=> array('UniqueEmailAddress'),
			// 'errorMessages' => array('Please enter a valid email address'),
		));
		
		$this->addElement('text', 'username', array(
			'required' 		=> true,
			'label' 		=> 'Username',
			'filters' 		=> array('StringTrim'),
			// 'errorMessages' => array('Please enter a username, numbers and letters only'),
			'validators' 	=> array('UniqueUsername'),
			
		));
		
		$this->addElement('captcha', 'captcha', array(
			'label' 	=> 'Please enter these letters below:',
			'required' 	=> true,
			'ignore'	=> true,
			'attribs'	=> array('size' => '21'),
			'captcha'	=> array(
				'captcha' 	=> 'image',
				'font'	  	=> APPLICATION_PATH . '/../data/fonts/VeraBd.ttf',
				'wordlen' 	=> 6,
				'timeout' 	=> 300,
				'expiration'=> 360,
				'width'	  	=> 150,
				'GcFreq'	=> 50
			),
		));
		
		$this->addElement('submit', 'submit', array(
			'required' 		=> false,
			'ignore' 		=> true,
			'label' 		=> 'Register',
			'decorators' => array('ViewHelper',array('HtmlTag', array('tag' => 'dd', 'id' => 'form-submit'))),
		));
	}
}
