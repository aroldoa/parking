<?php
/**
* 
*/
class Form_Lot extends My_Form_Abstract
{
	
	public function init()
	{
		parent::init();
		
		$this->addElement('hidden', 'id', array(
			'decorators' => array('viewHelper')
		));
		
		$this->addElement('text', 'name', array(
			'label' => 'Name:',
			'required' => true,
			'decorators' => array('composite')
		));
		
		$this->addElement('text', 'address', array(
			'label' => 'Address:',
			'required' => true,
			'decorators' => array('composite')
		));
		
		$this->addElement('text', 'city', array(
			'label' => 'City:',
			'required' => true,
			'decorators' => array('composite')
		));
		
		$this->addElement('text', 'state', array(
			'label' => 'State:',
			'required' => true,
			'decorators' => array('composite')
		));
		
		$this->addElement('text', 'zip', array(
			'label' => 'Zipcode:',
			'required' => true,
			'decorators' => array('composite')
		));
		
		$this->addElement('textarea', 'description', array(
			'label' => 'Description:',
			'required' => true,
			'attribs' => array(
				'cols' => 60,
				'rows' => 6
			),
			'decorators' => array('composite')
		));
		
		$this->addElement('select', 'status', array(
			'label' => 'Status:',
			'multiOptions' => array(
				'Open' => 'Open', 
				'Closed' => 'Closed'
			),
			'decorators' => array('composite')
		));
		
		$this->addElement('submit', 'submit', array(
			'label' => 'Save Lot',
			'decorators' => array('ViewHelper'),
		));
		
		$this->addElement('submit', 'delete', array(
			'label' => 'Delete Lot',
			'decorators' => array('ViewHelper'),
		));
		
		$this->addElement('submit', 'cancel', array(
			'label' => 'Cancel',
			'decorators' => array('ViewHelper'),
		));
		
		$this->addDisplayGroup(
			array('submit', 'delete', 'cancel'), 
			'actions', 
			array(
				'decorators' => $this->setCustomDisplayGroupDecorators('button')
			)
		);
	}
}
