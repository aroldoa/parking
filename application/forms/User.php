<?php
/**
* 
*/
class Form_User extends Zend_Form
{
	
	public function init()
	{
		$this->setDecorators(array(
	        'FormElements',
	        'Form'
	    ));
	
		// for all elements:
		$this->addElementPrefixPath(
			'My_Decorator',
			'My/Decorator/',
			'decorator'
		);
		
		$this->addElement('hidden', 'id', array(
			'decorators' 	=> array('viewHelper'),
		));
		
		$this->addElement('text', 'email', array(
			'label' => 'Email:',
			'required' => true,
			'decorators' 	=> array('Composite'),
		));
		
		$this->addElement('text', 'first', array(
			'label' => 'First:',
			'required' => true,
			'decorators' 	=> array('Composite'),
		));
		
		$this->addElement('text', 'last', array(
			'label' => 'Last:',
			'required' => true,
			'decorators' 	=> array('Composite'),
		));
		
		$this->addElement('text', 'phone', array(
			'label' => 'Phone:',
			'required' => true,
			'decorators' 	=> array('Composite'),
		));
		
		$this->addElement('text', 'address', array(
			'label' => 'Address:',
			'required' => true,
			'decorators' 	=> array('Composite'),
		));
		
		$this->addElement('text', 'city', array(
			'label' => 'City:',
			'required' => true,
			'decorators' 	=> array('Composite'),
		));
		
		$this->addElement('text', 'state', array(
			'label' => 'State',
			'required' => true,
			'decorators' 	=> array('Composite'),
		));
		
		$this->addElement('text', 'zip', array(
			'label' => 'Zip:',
			'required' => true,
			'decorators' 	=> array('Composite'),
		));
		
		$this->addElement('text', 'ip', array(
			'label' => 'IP:',
			'required' => true,
			'decorators' 	=> array('Composite'),
		));
		
		$this->addElement('text', 'created', array(
			'label' => 'Created',
			'required' => true,
			'decorators' 	=> array('Composite'),
		));
		
		$this->addElement('text', 'last_login', array(
			'label' => 'Last Login:',
			'required' => true,
			'decorators' 	=> array('Composite'),
		));
		
		$this->addElement('text', 'password', array(
			'label' => 'Password',
			'required' => true,
			'decorators' 	=> array('Composite'),
		));
		
		$this->addElement('submit', 'submit', array(
			'label' => 'Save User',
			'decorators' 	=> array('Submit'),
		));
	}
}
