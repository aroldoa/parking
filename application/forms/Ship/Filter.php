<?php
/**
* 
*/
class Form_Ship_Filter extends My_Form_Abstract
{
	
	public function init()
	{
		parent::init();
		
		$this->addElement('hidden', 'id', array());
		
		$this->addElement('select', 'year', array(
			'label' => 'Select Year',
			'multiOptions' => $this->getYearOptions()
		));
		
		$this->setCustomDecorators();
		
		$this->addElement('submit', 'submit', array(
			'label' => 'Apply Filters',
			'decorators' => array('viewHelper')
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
	
	public function getYearOptions()
	{
		$date = new DateTime();
		$current = (int) $date->format('Y');
		
		$years = array(
			null => 'Select Year',
			$current - 1 => $current -1,
			$current => $current
		);
		
		for ($i=1; $i < 3; $i++) { 
			$years[$current + $i] = $current + $i;
		}
		
		return $years;
	}
}
