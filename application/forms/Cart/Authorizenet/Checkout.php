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
				array('Regex', false, array('/^[a-zA-Z0-9\.\,\-\_\ ]+$/i'))
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
			array('card_num', 'exp_date', 'card_code'),
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
	}
}
