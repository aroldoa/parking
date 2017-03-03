<?php
/**
* 
*/
class Form_Reservation extends My_Form_Abstract
{
	
	public function init()
	{
		parent::init();
		
		$this->addElement('hidden', 'id', array(
			'decorators' => array('viewHelper')
		));
		
		$this->addElement('text', 'from', array(
			'label' => 'Parking Date:',
			'required' => true,
			'decorators' => array('Composite'),
			'validators' => array('Date')
		));
		
		$this->addElement('text', 'to', array(
			'label' => 'Return Date:',
			'required' => true,
			'decorators' 	=> array('Composite'),
			'validators' => array('Date', 'ToDate')
		));
		
		$this->addElement('select', 'lot', array(
			'label' => 'Lot:',
			'required' => true,
			'multiOptions' => $this->getLots(),
			'decorators' 	=> array('Composite')
		));
		
		$this->addElement('select', 'type', array(
			'label' => 'Spot Type:',
			'required' => true,
			'multiOptions' => $this->getSpotTypes(),
			'decorators' 	=> array('Composite'),
		));
		
		$this->addElement('text', 'quantity', array(
			'label' => '# of Spots:',
			'required' => true,
			'attribs' => array(
				'maxlength' => '4'
			),
			'decorators' 	=> array('Composite'),
		));
		
		$status = new My_Static_ReservationStatus();
		
		$this->addElement('select', 'status', array(
			'label' => 'Reservation Status',
			'decorators' => array('Composite'),
			'multiOptions' => $status->toArray()
		));
		
		$this->addElement('submit', 'submit', array(
			'label' => 'Reserve Spot',
			'decorators' => array('ViewHelper'),
		));
		
		$this->addElement('submit', 'cancel', array(
			'label' => 'Cancel',
			'decorators' => array('ViewHelper'),
		));
		
		$this->addElement('submit', 'delete', array(
			'label' => 'Delete Reservation',
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
			$lotModel = new Model_Lot();
			$lots = $lotModel->getLots();
			$lotsArray = array();

			foreach ($lots as $lot) {
				$lotsArray[$lot->id] = $lot->name;
			}
			
			return $lotsArray;
	}
	
	public function getSpotTypes()
	{
		$settings = new Model_SiteSettings();
		
		return $settings->getSpotTypes();
	}
}
