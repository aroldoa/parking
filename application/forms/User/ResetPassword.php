<?php
/**
* 
*/
class Form_User_ResetPassword extends Zend_Form
{
	
	public function init()
	{
		$this->setAction('/account/resetpassword');
		$this->setMethod('post');
		
		// add path to custom validators & filters
        $this->addElementPrefixPath(
            'Form_Validate',
            APPLICATION_PATH . '/forms/validate/',
            'validate'
        );
		
		$this->addElement('text', 'email', array(
			'label' => 'Email Address',
			'required' => true,
			'validators' => array('Email'),
			'filters' => array('StringTrim'),
		));
		
		$this->addElement('submit', 'submit', array(
			'required' 		=> false,
			'ignore' 		=> true,
			'label' 		=> 'Reset Password',
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
