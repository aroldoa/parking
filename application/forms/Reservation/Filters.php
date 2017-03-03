<?php
/**
* 
*/
class Form_Reservation_Filters extends My_Form_Abstract
{
	protected $_lots;
	
	public function init()
	{
		parent::init();
		
		$this->setMethod('Post');
		
		$this->addElement('select', 'type', array(
			'label' => 'Spot Type',
			'multiOptions' => $this->getSpotTypes(),
			'decorators' => array('Composite')
		));
		
		$this->addElement('select', 'lot', array(
			'label' => 'Lot',
			'multiOptions' => $this->getLots(),
			'decorators' => array('Composite')
		));
		
		$this->addElement('select', 'status', array(
			'label' => 'Reservation Status',
			'multiOptions' => $this->getStatus(),
			'decorators' => array('Composite')
		));
		
		$this->addElement('text', 'date', array(
			'label' => 'Reservation Contains Date',
			'decorators' => array('Composite')
		));
		
		$this->addElement('text', 'from', array(
			'label' => 'Reservation Start Date',
			'decorators' => array('Composite')
		));
		
		$this->addElement('text', 'to', array(
			'label' => 'Reservation End Date',
			'decorators' => array('Composite')
		));
		
		$this->addElement('text', 'transaction', array(
			'label' => 'Transaction Id',
			'decorators' => array('Composite')
		));
		
		$this->addElement('select', 'show', array(
			'label' => 'Show Results', 
			'multiOptions' => array(
				null => 'Select One',
				10 => 10,
				25 => 25,
				50 => 50,
				100 => 100,
				'all' => 'All'
			),
			'decorators' => array('Composite')
		));
		
		$this->addElement('submit', 'submit', array(
			'label' => 'Apply Filters',
			'decorators' => array('viewHelper'),
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
	
	public function getLots()
	{
		if (!isset($this->_lots)) {
			$lotModel = new Model_Lot();
			$lots = $lotModel->getLots();
			
			$return = array();
			$return[null] = 'Any';
			
			foreach ($lots as $lot) {
				$return[$lot->id] = $lot->name;
			}
			
			$this->_lots = $return;
		}
		
		return $this->_lots;
	}
	
	public function getStatus()
	{
		$status = new My_Static_ReservationStatus();
		return $status->toArray();
	}
	
	public function getSpotTypes()
	{
		$settings = new Model_SiteSettings();
		
		return $settings->getSpotTypes();
	}
}
