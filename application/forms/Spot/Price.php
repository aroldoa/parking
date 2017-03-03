<?php
/**
* 
*/
class Form_Spot_Price extends My_Form_Abstract
{
	public function init()
	{
		parent::init();
		
		$this->addElement('hidden', 'id', array());
		$this->addElement('hidden', 'spot', array());
		
		$this->addElement('text', 'days', array(
			'label' => 'Tier Length (days)',
			'required' => true,
			'decorators' => array('Composite'),
			'validators' => array('Digits', 'UniquePriceTier')
		));
		
		$this->addElement('text', 'price', array(
			'label' => 'Tier Price (dollars)',
			'required' => true,
			'decorators' => array('Composite'),
			'validators' => array('Digits')
		));
		
		$this->setCustomDecorators();
		
		$this->addElement('submit', 'submit', array(
			'label' => 'Save',
			'decorators' => array('viewHelper')
		));
		
		$this->addElement('submit', 'cancel', array(
			'label' => 'Cancel',
			'decorators' => array('viewHelper')
		));
		
		$this->addDisplayGroup(
			array('submit', 'cancel'), 
			'actions', 
			array(
				'decorators' => $this->setCustomDisplayGroupDecorators('button')
			)
		);
		
		
	}
}
