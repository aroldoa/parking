<?php
/**
* 
*/
class Form_Search extends My_Form_Abstract
{
	protected $_results = array();
	
	public function init()
	{
		parent::init();
		$this->setAttrib('id', 'reservation-form');
		
		$search = new Zend_Form_SubForm();
		
		$this->setDecorators(array(
			array(
				'ViewScript',
				array('viewScript' => 'index/_search-form.phtml')
			),
			'Form'
		));
		
		$this->addElement('select', 'ship', array(
			'required' => true,
			'label' => 'Cruise Ship Name',
			'multiOptions' => $this->getShipOptions(),
			'errorMessages' => array('Please Choose your Ship')
		));
		
		$this->addElement('select', 'cruise', array(
			'required' => true,
			'label' => 'Cruise Departure Date',
			'multiOptions' => array(null => 'Please Select Your Ship First'),
			// 'validators' => array('CruiseDate')
		));
		
		$this->addElement('select', 'type', array(
			'required' => true,
			'label' => 'Parking Spot Type',
			'multiOptions' => array(null => 'Please Choose a Cruise Date First'),
			'errorMessages' => array('Please Choose Parking Spot Type')
		));
		
		$this->addElement('select','quantity', array(
			'required' => true,
			'label' => 'Number of Spots',
			'multiOptions' => array('Please Choose Parking Spot Type First'),
			'errorMessages' => array('Please Select the Number of Spots')
		));
		
		// $this->addElement('submit', 'submit', array(
		// 			'label' => 'Search Lot'
		// 		));
		
		$this->setCustomDecorators();
	}
	
	public function getShipOptions()
	{
		$shipModel = new Model_Ship();
		
		$options = array(
			'status' => Model_Ship::STATUS_ACTIVE
		);
		
		$shipRows = $shipModel->getShips($options);
		
		$ships = array(
			null => 'Please Select Your Ship'
		);
		foreach ($shipRows as $ship) {
			$ships[$ship->id] = $ship->name;
		}
		
		return $ships;
	}
	
	public function setCruiseDateOptions(Resource_Ship_Item $ship)
	{
		$options = array(
			'from' => time(),
			'ship' => $ship->id,
			'order' => 'date ASC'
		);
		
		$mCruise = new Model_Cruise();
		$cruises = $mCruise->getCruises($options);
	
		$return = array(
			null => 'Select a Date'
		);
		
		foreach ($cruises as $cruise) {
			$return[$cruise->id] = $cruise->sailDate;
		}
		
		return $this->getElement('cruise')->setMultiOptions($return);
	}
	
	public function setTypeOptions()
	{
		$mLot = new Model_Lot();
		$lots = $mLot->getLots();
		
		$spotTypes = array(
			null => 'Select One'
		);
		
		// loop through lots and check spot types
		foreach ($lots as $lot) {
			foreach ($lot->spots as $spot) {
				if (!in_array($spot->type, $spotTypes) && count($spot->priceTiers) > 0) {
					$spotTypes[$spot->type] = $spot->getKeyToType();
				}
			}
		}
		
		return $this->getElement('type')->setMultiOptions($spotTypes);
	}
	
	public function setQuantityOptions($remaining)
	{
		// echo $remaining;
		if ($remaining == 0) {
			$options = array('none' => 'No Spots Remaining');
		} else {
			$number = $remaining > 20 ? 20 : $remaining;

			$options = array(
				null => 'Select Quantity'
			);
			for ($i=1; $i <=  $number; $i++) { 
				$options[$i] = $i;
			}
		}
		
		
		return $this->getElement('quantity')->setMultiOptions($options);
	}
	
	public function addCheckoutButton()
	{
		$this->addElement('submit', 'checkout', array(
			'label' => 'Purchase Parking',
			'decorators' => array('Submit')
		));
	}
	
	public function addCartButton()
	{
		$this->addElement('submit', 'cart', array(
			'label' => 'Add To Cart',
			'decorators' => array('Submit')
		));
	}
	
	public function setResults(array $results)
	{
		$this->_results = $results;
		$results = new Zend_Form_SubForm();
		foreach ($this->_results as $result) {
			$results->addElement('hidden', 'result', array(
				'value' => $result->id,
				'decorators' => array('viewHelper')
			));
			
			$results->addElement('submit', 'cart', array(
				'label' => 'Add To Cart',
				'decorators' => array('viewHelper')
			));
			
		}
		
		$this->addSubform($results, 'results');
	}
	
	public function getResults()
	{
		if (isset($this->_results)) {
			return $this->_results;
		}
		
		return null;
	}
}
