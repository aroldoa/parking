<?php
/**
* 
*/
class Form_Transaction extends Zend_Form
{
	
	public function init()
	{
		$this->addElement('', '', array(
			'label' => '',
			'required' => true
		));
	}
}
