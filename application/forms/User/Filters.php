<?php
/**
* 
*/
class Form_User_Filters extends My_Form_Abstract
{
	
	public function init()
	{
		parent::init();
		
		$this->addElement('text', 'first_name', array(
			'label' => 'First Name:',
			'decorators' => array('Composite')
		));
		
		$this->addElement('text', 'last_name', array(
			'label' => 'Last Name:',
			'decorators' => array('Composite')
		));
		
		$this->addElement('select', 'state', array(
			'label' => 'User State',
			'decorators' => array('Composite'),
			'multiOptions' => $this->getStates()
		));
		
		$this->addElement('text', 'zip', array(
			'label' => 'User Zipcode:',
			'decorators' => array('Composite')
		));
		
		
		$this->addElement('select', 'role', array(
			'label' => 'User Type',
			'decorators' => array('Composite'),
			'multiOptions' => $this->getUserTypes()
		));
		
		$this->addElement('submit', 'submit', array(
			'label' => 'Apply Filters',
			'decorators' => array('viewHelper')
		));
		
		$this->addElement('submit', 'clear', array(
			'label' => 'Clear Filters',
			'decorators' => array('viewHelper')
		));
		
		$this->addDisplayGroup(
			array('submit', 'clear'), 
			'actions', 
			array(
				'decorators' => $this->setCustomDisplayGroupDecorators('button')
			)
		);
	}
	
	public function getUserTypes()
	{
		$roles = new My_Static_UserRole();
		return $roles->toArray();
	}
	
	public function getStates()
	{
		$states = new My_Static_State();
		return $states->toArray();
	}
}
