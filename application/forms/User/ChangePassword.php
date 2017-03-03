<?php
/**
* 
*/
class Form_User_ChangePassword extends Zend_Form
{
	
	public function init()
	{
		// add path to custom validators & filters
        $this->addElementPrefixPath(
            'Form_Validate',
            APPLICATION_PATH . '/forms/validate/',
            'validate'
        );

		$this->addElement('password', 'password', array(
			'label' 		=> 'Password',
			'required' 		=> false,
			'filters' 		=> array('StringTrim'),
		));
		
		$this->addElement('password', 'confirm', array(
			'label' 		=> 'Confirm Password',
			'required' 		=> false,
			'filters' 		=> array('StringTrim'),
			'validators'	=> array('PasswordConfirm'),
		));
		
		$this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'append', 'class' => 'errors')),
            'Form'
        ));
		
		$this->addDisplayGroup(array('password', 'confirm', 'description'), 'changePassword', array('legend' => 'Change Password'));
	}
}
