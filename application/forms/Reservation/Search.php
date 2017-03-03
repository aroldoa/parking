<?php
/**
* 
*/
class Form_Reservation_Search extends My_Form_Abstract
{
	
	public function init()
	{
		parent::init();
		
		$this->setDecorators(array(
			'Description',
	        'FormElements',
	        array('Form', array('class' => 'echo-form'))
	    ));
		
		$this->addElement('select', 'ship', array(
			'label' => 'Ship Name:',
			'decorators' => array('Composite'),
			'multiOptions' => $this->setShips(),
			// 'attribs' => array(
			// 				'onchange' => 'fire'
			// 			)
		));
		
		$this->addElement('select', 'cruise', array(
			'label' => 'Cruise Date:',
			'decorators' => array('Composite'),
			'multiOptions' => array(null => 'Select Ship First!')
		));
		
		$this->addElement('select', 'type', array(
			'label' => 'Spot Type:',
			'decorators' => array('Composite'),
			'multiOptions' => array(null => 'Select Cruise First!')
		));
		
		$this->addElement('select', 'quantity', array(
			'label' => 'Number of spots',
			'decorators' => array('Composite'),
			'multiOptions' => array(null => 'Select Spot Type First!')
		));
		
		$this->addElement('text', 'first_name', array(
			'label' => 'First Name:',
			'required' => true,
			'errorMessages' => array('Please enter your first name')
		));
		
		$this->addElement('text', 'last_name', array(
			'label' => 'Last Name:',
			'required' => true,
			'errorMessages' => array('Please enter your last name')
		));
		
		$this->addElement('text', 'email', array(
			'label' => 'Email:',
			'required' => true,
			'filters' => array('StringTrim'),
			'validators' => array(
				// 'NotEmpty',
				'EmailAddress'
			),
			'errorMessages' => array('Please enter a valid email address')
		));
		
		$this->addElement('text', 'phone', array(
			'label' => 'Phone:',
			'required' => true,
			'errorMessages' => array('Please enter a contact phone number')
		));
		
		$this->addElement('text', 'address', array(
			'label' => 'Address:',
			'required' => true,
			'filters' => array('StringTrim'),
			'validators' => array(
				array('Regex', false, array('/^[a-zA-Z0-9 ]+$/i'))
			),
			'errorMessages' => array('Please enter your address')
		));
		
		$this->addElement('text', 'city', array(
			'label' => 'City:',
			'required' => true,
			'filters' => array('StringTrim'),
			'validators' => array(
				array('Regex', false, array('/^[a-zA-Z0-9 ]+$/i'))
			),
			'errorMessages' => array('Please enter your city name')
		));
		
		$states = new My_Static_State();
		
		$this->addElement('select', 'state', array(
			'label' => 'State:',
			'required' => true,
			'validators' => array(
				array('StringLength', false, array(2,2)),
				'Alpha'
			),
			'errorMessages' => array('Please enter your state abbreviation'),
			'multiOptions' => $states->toArray()
		));
		
		$this->addElement('text', 'zip', array(
			'label' => 'Zip Code:',
			'required' => true,
			'filters' => array(
				'StringTrim'
			), 
			'validators' => array(
				array('StringLength', false, array(5,5)) ,
				'Digits',
			),
			'errorMessages' => array('Please enter a 5 digit zipcode')
		));
		
		$this->addElement('text', 'country', array(
			'label' => 'Country:',
			'required' => true,
			'errorMessages' => array('Please enter your country')
		));
		
		
		$this->addElement('text', 'card_num', array(
			'label' => 'Card Number',
			'required' => true,
			'validators' => array(
				'Digits'
			),
			'errorMessages' => array('Please enter your credit card number')
		));
		
		$this->addElement('text', 'exp_date', array(
			'label' => 'Exp. (MM/YY)',
			'required' => true,
			'validators' => array(
				array('Regex', false, array('/^\d\d\/\d\d$/i'))
			),
			'errorMessages' => array('Please enter expiration in format "mm/yy"'),
		));
		
		$this->addElement('text', 'card_code', array(
			'label' => 'Security Code',
			'required' => true,
			'filters' => array(
				'stringTrim'
			),
			'validators' => array(
				'Digits',
				array('StringLength', false, array(3,3))
			),
			'errorMessages' => array('Please enter the security code from the back of the card')
		));
		
		$this->displayGroups();
		
		// $this->addElement('submit', 'submit', array(
		// 			'label' => 'Pay Now',
		// 			'decorators' => array(
		// 				'ViewHelper',
		// 				array('HtmlTag', array('tag' => 'div', 'class' => 'form-submit sixteen columns clear'))
		// 			)
		// 		));
		
		$this->setCustomDecorators();
	}
	
	public function setShips()
	{
		$mShip = new Model_Ship();
		
		$ships = $mShip->getShips();
		
		$options = array(
			null => 'Select your ship'
		);
		foreach ($ships as $ship) {
			$options[$ship->id] = $ship->name;
		}
		
		return $options;
	}
	
	public function setShipCruiseDates(Resource_Ship_Item $ship)
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
		// return $return;
	}
	
	public function setSpotTypes()
	{
		$types = new My_Static_SpotType();
		return $types->toArray();
	}
	
	public function setQuantityOptions($remaining)
	{
		if ($remaining == 0) {
			return array(null => 'No Spots Remaining');
		}
		$number = $remaining > 20 ? 20 : $remaining;
		
		$options = array(
			null => 'Select Quantity'
		);
		for ($i=1; $i <=  $number; $i++) { 
			$options[$i] = $i;
		}
		
		return $options;
	}
	
	// public function addCruiseDateSelect(Resource_Ship_Item $ship)
	// {
	// 	$this->addElement('select', 'cruise', array(
	// 		'label' => 'Ship Name:',
	// 		'decorators' => array('Composite'),
	// 		'multiOptions' => array(null => 'select one')
	// 	));
	// }
	
	public function addSubmitButton()
	{
		$this->addElement('submit', 'submit', array(
			'label' => 'Pay Now',
			'decorators' => array(
				'ViewHelper',
				array('HtmlTag', array('tag' => 'div', 'class' => 'form-submit sixteen columns'))
			)
		));
		
	}
	
	public function addSpotTypeSelect()
	{	
		$this->getElement('type')->setMultiOptions($this->setSpotTypes());
	}
	
	public function addSpotQuantitySelect($remaining)
	{
		// $this->addElement('select', 'quantity', array(
		// 			'label' => 'Number of spots',
		// 			'decorators' => array('Composite'),
		// 			'multiOptions' => $this->setQuantityOptions($remaining)
		// 		));
		
		$this->getElement('quantity')->setMultiOptions($this->setQuantityOptions($remaining));
	}
	
	public function displayGroups()
	{
		$this->addDisplayGroup(
			array('ship', 'cruise', 'type', 'quantity'), 
			'search-info', 
			array(
				// 'legend' => 'Account Settings', 
				'decorators' => array(
					'FormElements', 
					array('Description', array('placement' => 'prepend', 'tag' => 'div')),
					array('Fieldset', array('class' => 'four columns'))
				),
				'description' => 'Reservation Details',
			)
		);
		
		$this->addDisplayGroup(
			array('first_name', 'last_name', 'email', 'phone'), 
			'person-info', 
			array(
				// 'legend' => 'Account Settings', 
				'decorators' => array(
					'FormElements', 
					array('Description', array('placement' => 'prepend', 'tag' => 'div')),
					array('Fieldset', array('class' => 'four columns'))
				),
				'description' => 'Personal Details',
			)
		);
		
		$this->addDisplayGroup(
			array('address', 'city', 'state', 'zip', 'country'), 
			'address-info', 
			array(
				// 'legend' => 'Account Settings', 
				'decorators' => array(
					'FormElements', 
					array('Description', array('placement' => 'prepend', 'tag' => 'div')),
					array('Fieldset', array('class' => 'four columns'))
				),
				'description' => 'Billing Address',
			)
		);
		
		$this->addDisplayGroup(
			array('card_num', 'exp_date', 'card_code'), 
			'payment-info', 
			array(
				// 'legend' => 'Account Settings', 
				'decorators' => array(
					'FormElements', 
					array('Description', array('placement' => 'prepend', 'tag' => 'div')),
					array('Fieldset', array('class' => 'four columns'))
				),
				'description' => 'Payment Details',
			)
		);
	}
}