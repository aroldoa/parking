<?php
/**
*
*/
class Form_Cart_Authorizenet_Checkout extends My_Form_Abstract
{

	public function init()
	{
		parent::init();
		$this->setAttrib('id', 'checkout-form');
		$this->setAttrib('class', 'checkout-form');
		$this->setDescription('Enter Your Billing Information');

		$this->setDecorators(array(
			array(
				'ViewScript',
				array('viewScript' => 'index/_checkout-form.phtml')
			),
			'Form'
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
				array('Regex', false, array('/^[a-zA-Z0-9\.\,\-\_\'\ ]+$/i'))
			),
			'errorMessages' => array('Please enter your address')
		));

		$this->addElement('text', 'city', array(
			'label' => 'City:',
			'required' => true,
			'filters' => array('StringTrim'),
			'validators' => array(
				array('Regex', false, array('/^[a-zA-Z0-9\.,-_ ]+$/i'))
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

		// $this->addElement('text', 'country', array(
		// 			'label' => 'Country:',
		// 			'required' => true,
		// 			'errorMessages' => array('Please enter your country')
		// 		));


		$this->addElement('text', 'card_num', array(
			'label' => 'Card Number',
			'required' => true,
			'validators' => array(
				'Digits'
			),
			'errorMessages' => array('Please enter your credit card number')
		));

		$months = array();
		for ($i = 1; $i < 13; $i++)
		{
			if ($i < 10)
				$months['0'.$i] = '0'.$i;
			else
				$months[$i] = $i;
		}

		$expMonth = new Zend_Form_Element_Select('exp_month');
		$expMonth->setLabel('Exp:')
			->addMultiOptions($months)
			->setAttrib('style', 'width: 20%; display: inline !important')
			->setDescription('/');

		$this->addElement($expMonth);

		// Generate the Expiry Year options
		$years = array();
		$thisYear = date('Y');

		for ($i = 0; $i < 10; ++$i)
		{
			$val = $thisYear + $i;
			$years[$val] = $val;
		}

		// The Expiry Year field
		$expYear = new Zend_Form_Element_Select('exp_year');
		$expYear->addMultiOptions($years)
			->setAttrib('style', 'width: 30%; display: inline !important')
			->setDescription(' (Month / Year)');

		$this->addElement($expYear);

		$this->addElement('text', 'card_code', array(
			'label' => 'Security Code',
			'required' => true,
			'filters' => array(
				'stringTrim'
			),
			'validators' => array(
				'Digits',
				array('StringLength', false, array(3,4))
			),
			'errorMessages' => array('Please enter the security code from the back of the card')
		));


		/*
			Add Elements to Display Groups
		*/
		$this->addDisplayGroup(
			array('first_name', 'last_name', 'email', 'phone'),
			'person-info',
			array(
				// 'legend' => 'Account Settings',
				'decorators' => array(
					'FormElements',
					'Fieldset',
					array('Description', array('placement' => 'prepend', 'tag' => 'div')),
					array('HtmlTag', array('tag' => 'div', 'class' => 'checkout-group')),
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
					'Fieldset',
					array('Description', array('placement' => 'prepend', 'tag' => 'div')),
					array('HtmlTag', array('tag' => 'div', 'class' => 'checkout-group')),
				),
				'description' => 'Billing Address',
			)
		);

		$this->addDisplayGroup(
			array('card_num', 'exp_month','exp_year', 'card_code'),
			'payment-info',
			array(
				// 'legend' => 'Account Settings',
				'decorators' => array(
					'FormElements',
					'Fieldset',
					array('Description', array('placement' => 'prepend', 'tag' => 'div')),
					array('HtmlTag', array('tag' => 'div', 'class' => 'checkout-group')),
				),
				'description' => 'Payment Details',
			)
		);

		$this->addElement('checkbox', 'confirm', array(
			'label' => 'Agree to Terms',
			'required' => true,
		));

		$this->addElement('submit', 'submit', array(
			'label' => 'Pay Now',
			'decorators' => array(
				'ViewHelper',
				array('HtmlTag', array('tag' => 'div', 'class' => 'form-submit'))
			)
		));

		$this->setCustomDecorators();

		$expMonth->setDecorators(array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'id' => 'card-expire',
				'openOnly' => true)),
			array('Description', array('tag' => 'span', 'class' => 'seperator')),
			array('Label', array('tag' => 'div'))
		));

		$expYear->setDecorators(array(
			'ViewHelper',
			array('Description', array('tag' => 'small', 'class' => 'greyout')),
			array(array('row' => 'HtmlTag'), array('tag' => 'div', 'closeOnly' => true))
		));
	}
}
