<?php
/**
* 
*/
class Form_User_Edit extends My_Form_Abstract
{
	
	public function init()
	{
		parent::init();

		$this->addElement('text', 'first_name', array(
			'required' 		=> true,
			'label' 		=> 'First Name',
			'filters' 		=> array('StringTrim'),
			'errorMessages' => array('Please enter a first name'),
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('text', 'last_name', array(
			'required' 		=> true,
			'label' 		=> 'Last Name',
			'filters' 		=> array('StringTrim'),
			'errorMessages' => array('Please enter a last name'),
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('text', 'email', array(
			'required' 		=> true,
			'label' 		=> 'Email Address',
			'filters' 		=> array('StringTrim'),
			'validators' 	=> array('UniqueEmailAddress'),
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('text', 'phone', array(
			// 'required' 		=> true,
			'label' 		=> 'Phone Number',
			'filters' 		=> array('StringTrim'),
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('text', 'address', array(
			// 'required' 		=> true,
			'label' 		=> 'Address',
			'filters' 		=> array('StringTrim'),
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('text', 'city', array(
			// 'required' 		=> true,
			'label' 		=> 'City',
			'filters' 		=> array('StringTrim'),
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('select', 'state', array(
			// 'required' 		=> true,
			'label' 		=> 'State',
			'multiOptions'	=> $this->getStates(),
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('text', 'zip', array(
			// 'required' 		=> true,
			'label' 		=> 'Zip',
			'filters' 		=> array('StringTrim'),
			'decorators' 	=> array('Composite')
		));
		
		$roles = new My_Static_UserRole();
		$this->addElement('select', 'role', array(
			'required' 		=> true,
			'label' 		=> 'User Role',
			'filters' 		=> array('StringTrim'),
			'validators' 	=> array('Alpha'),
			'errorMessages' => array('User role must be alphabetical'),
			'decorators' 	=> array('Composite'),
			'multiOptions'	=> $roles->toArray()
		));
		
		$this->addElement('password', 'password', array(
			'label' 		=> 'Password',
			'ignore'		=> false,
			'filters' 		=> array('StringTrim'),
			'validators'	=> array('PasswordConfirm'),
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('password', 'confirm', array(
			'label' 		=> 'Confirm Password',
			'ignore'		=> true,
			'filters' 		=> array('StringTrim'),
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('hidden', 'id', array(
			'decorators' => array('ViewHelper'),
		));
		
		$this->addElement('submit', 'submit', array(
			'required' 		=> false,
			'ignore' 		=> true,
			'label' 		=> 'Save',
			'decorators' => array('ViewHelper'),
		));
		
		$this->addElement('submit', 'delete', array(
			'required' 		=> false,
			'ignore' 		=> true,
			'label' 		=> 'Delete',
			'decorators' => array('ViewHelper'),
		));
		
		$this->addElement('submit', 'cancel', array(
			'required' 		=> false,
			'ignore' 		=> true,
			'label' 		=> 'Cancel',
			'decorators' => array('ViewHelper'),
		));
		
		
		$this->addDisplayGroup(
			array('first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state', 'zip', 'role', 'id'), 
			'userDetails', 
			array(
				'decorators' => $this->setCustomDisplayGroupDecorators()
			)
		);
		
		$this->addDisplayGroup(
			array('password', 'confirm'), 
			'userPassword', 
			array(
				'decorators' => $this->setCustomDisplayGroupDecorators()
			)
		);
		
		$this->addDisplayGroup(
			array('submit', 'delete', 'cancel'), 
			'actions', 
			array(
				'decorators' => $this->setCustomDisplayGroupDecorators('button')
			)
		);
	}
	
	public function getStates()
	{
		$states = new My_Static_State();
		return $states->toArray();
	}
}
