<?php
/**
* 
*/
class Form_Cart_Table extends My_Form_Abstract
{
	
	public function init()
	{
		parent::init();
		
		$this->setDecorators(array(
			array(
				'ViewScript',
				array('viewScript' => 'index/_cart.phtml')
			),
			'Form'
		));
		
		$this->setMethod('post');
		$this->setAction('');
		
		$this->addElement('hidden', 'shipping');
		
		$this->addElement('submit', 'update', array(
			'decorators' => array('ViewHelper'),
			'label' => 'Update Cart'
		));
		
		$this->addElement('submit', 'checkout', array(
			'decorators' => array('ViewHelper'),
			'label' => 'Checkout'
		));
		
		$this->addElement('submit', 'continue', array(
			'decorators' => array('ViewHelper'),
			'label' => 'Continue Shopping'
		));
		
		$this->addElement('submit', 'empty', array(
			'decorators' => array('ViewHelper'),
			'label' => 'Empty Cart'
		));
	}
}
