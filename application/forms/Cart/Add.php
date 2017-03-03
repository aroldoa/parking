<?php
/**
* 
*/
class Form_Cart_Add extends My_Form_Abstract
{
	
	public function init()
	{
		parent::init();
		
		$this->addElement('submit', 'submit', array(
			'label' => 'Add to Cart'
		));
		
		$this->addElement('hidden', 'id');
		$this->addElement('hidden', 'from');
		$this->addElement('hidden', 'to');
		$this->addElement('hidden', 'quantity');
		
		$this->setCustomDecorators();
	}
}
