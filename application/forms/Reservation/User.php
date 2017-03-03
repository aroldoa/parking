<?php
/**
* 
*/
class Form_Reservation_User extends My_Form
{
	
	public function init()
	{
		parent::init();
		
		$this->addElement('text', 'email', array(
			'required' => true, 
			'label' => 'Email Address:',
			'decorators' => array(),
		));
		
		$this->addElement('text', 'first_name', array(
			'required' => true, 
			'label' => 'First Name:',
			'decorators' => array(),
		));
		
		$this->addElement('text', 'last_name', array(
			'required' => true, 
			'label' => 'Last Name:',
			'decorators' => array(),
		));
	}
}
