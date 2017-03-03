<?php
/**
* 
*/
class Form_Spot extends My_Form_Abstract
{
	
	public function init()
	{
		parent::init();
		
		$this->addElement('hidden', 'id', array(
			'decorators' => array('viewHelper')
		));
		
		$lotModel = new Model_Lot();
		$lots = $lotModel->getLots();
		$lotsArray = array();
		
		foreach ($lots as $lot) {
			$lotsArray[$lot->id] = $lot->name;
		}
		
		$this->addElement('select', 'lot', array(
			'label' => 'Lot:',
			'required' => true,
			'multiOptions' => $lotsArray,
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('select', 'type', array(
			'label' => 'Type:',
			'required' => true,
			'multiOptions' => $this->getSpotTypes(),
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('text', 'quantity', array(
			'label' => 'Quantity:',
			'required' => true,
			'attribs' => array(
				'maxlength' => '4'
			),
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('textarea', 'description', array(
			'label' => 'Description', 
			'required' => true,
			'attribs' => array(
				'cols' => 60,
				'rows' => 6
			),
			'decorators' 	=> array('Composite')
		));
		
		
		$this->addElement('submit', 'submit', array(
			'label' => 'Save Spot',
			'decorators' => array('ViewHelper'),
		));
		
		$this->addElement('submit', 'delete', array(
			'label' => 'Delete Spot',
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
	
	public function getSpotTypes()
	{
		$settings = new Model_SiteSettings();
		
		return $settings->getSpotTypes();
	}
}
