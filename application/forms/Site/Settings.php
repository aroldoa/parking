<?php
/**
* 
*/
class Form_Site_Settings extends My_Form_Abstract
{
	
	public function init()
	{
		parent::init();
		
		$this->setAttrib('id', 'settings-form');
		
		$this->addElement('text', 'taxRate', array(
			'label' => 'Tax Rate (#.##%)',
			'filters' => array('StringTrim'),
			'validators' => array(
				array('Regex', true, array('pattern' => '/^\d?\.\d+$/'))
			),
			'errorMessages' => array('Please enter percentage in format "8.50"')
		));
		
		$this->addElement('text', 'apiLoginId', array(
			'label' => 'Authorize.net API Login Id',
		));
		
		$this->addElement('text', 'transactionKey', array(
			'label' => 'Authorize.net Transaction Key',
		));
		
		$this->addElement('select', 'sandbox', array(
			'label' => 'Authorize.net Sandbox?',
			'multiOptions' => array(
				'true' => 'True', 
				'false' => 'False'
			)
		));
		
		$this->addElement('text', 'siteName', array(
			'label' => 'Site Name'
		));
		
		$this->addElement('textarea', 'policy', array(
			'label' => 'Policy Text',
			'attribs' => array(
				'rows' => 8,
				'cols' => 80
			)
		));
		
		$this->addElement('textarea', 'emailText', array(
			'label' => 'Confirmation Email Text',
			'attribs' => array(
				'rows' => 8,
				'cols' => 80
			)
		));
		
		$this->addElement('textarea', 'analytics', array(
			'label' => 'Analytics',
			'attribs' => array(
				'rows' => 8,
				'cols' => 80
			)
		));
		
		$this->addElement('textarea', 'spotTypes', array(
			'label' => 'Spot Types (One on each line)',
			'attribs' => array(
				'rows' => 8,
				'cols' => 80
			)
		));
		
		$this->setCustomDecorators();
		
		$this->addElement('submit', 'submit', array(
			'label' => 'Save',
			'decorators' => array('ViewHelper')
		));
		
		$this->addElement('submit', 'cancel', array(
			'label' => 'Cancel',
			'decorators' => array('ViewHelper')
		));
		
		
		
		$this->addDisplayGroup(
			array('apiLoginId', 'transactionKey', 'sandbox', 'taxRate', 'spotTypes', 'policy', 'emailText', 'analytics'),
			'cart',
			array(
				'legend' => 'Cart Settings',
				'decorators' => $this->setCustomDisplayGroupDecorators()
			)
		);
		
		$this->addDisplayGroup(
			array('submit', 'cancel'),
			'actions',
			array(
				'decorators' => $this->setCustomDisplayGroupDecorators('button')
			)
		);
	}
}
