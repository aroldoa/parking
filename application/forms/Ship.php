<?php
/**
* 
*/
class Form_Ship extends My_Form_Abstract
{
	protected $_lots;
	
	public function init()
	{
		parent::init();
		
		$this->addElement('hidden', 'id', array(
			'decorators' => array('ViewHelper')
		));
		
		$this->addElement('text', 'name', array(
			'label' => 'Name:',
			'required' => true,
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('select', 'lot', array(
			'label' => 'Lot:',
			'required' => true,
			'multiOptions' => $this->getLots(),
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('select', 'status', array(
			'label' => 'Status:',
			'required' => true,
			'multiOptions' => array(
				'active' => 'Active',
				'inactive' => 'Inactive'
			),
			'decorators' 	=> array('Composite')
		));
		
		
		$this->addElement('submit', 'submit', array(
			'label' => 'Save Ship',
			'decorators' => array('ViewHelper'),
		));
		
		$this->addElement('submit', 'cancel', array(
			'label' => 'Cancel',
			'decorators' => array('ViewHelper'),
		));
		
		$this->addElement('submit', 'delete', array(
			'label' => 'Delete',
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
	
	public function getLots()
	{
		if (!isset($this->_lots)) {
			$lotModel = new Model_Lot();
			$lots = $lotModel->getLots();
			
			$return = array();
			$return[] = 'Any';
			
			foreach ($lots as $lot) {
				$return[$lot->id] = $lot->name;
			}
			
			$this->_lots = $return;
		}
		
		return $this->_lots;
	}
}
