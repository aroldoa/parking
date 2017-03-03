<?php
/**
* 
*/
class My_Static_SpotType
{
	protected $_types; 
	
	public function __construct()
	{
		$this->_types =  array(
			'' => 'Select One',
			'Covered' => 'Covered',
			'Uncovered' => 'Uncovered', 
			'RV' => 'RV',
			'Bus' => 'Bus'
		);
		return $this;
	}
	
	public function toArray()
	{
		return $this->_types;
	}
}
