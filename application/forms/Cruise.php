<?php
/**
* 
*/
class Form_Cruise extends My_Form_Abstract
{
	public function init()
	{
		parent::init();
		
		$this->addElement('hidden', 'id', array(
			'decorators' => array('ViewHelper')
		));
		
		$this->addElement('hidden', 'ship', array(
			'decorators' => array('ViewHelper')
		));
		
		$this->addElement('text', 'date', array(
			'label' => 'Date:',
			'required' => true,
			'decorators' => array('composite')
		));
		
		$this->addElement('select', 'days', array(
			'label' => 'Days:',
			'required' => true,
			'multiOPtions' => $this->getDays(),
			'decorators' => array('composite')
		));
		
		$this->addElement('submit', 'submit', array(
			'decorators' => array('ViewHelper')
		));
		
		$this->addElement('submit', 'cancel', array(
			'decorators' => array('ViewHelper')
		));
		
		$this->addElement('submit', 'delete', array(
			'decorators' => array('ViewHelper')
		));
		
		$this->addDisplayGroup(
			array('submit', 'delete', 'cancel'), 
			'actions', 
			array(
				'decorators' => $this->setCustomDisplayGroupDecorators('button')
			)
		);
		
		
	}
	
	public function getDays()
	{
		$return = array();
		
		for ($i=1; $i <= 31; $i++) { 
			$return[$i] = $i;
		}
		
		return $return;
	}
}
