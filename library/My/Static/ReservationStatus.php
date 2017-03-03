<?php
/**
* 
*/
class My_Static_ReservationStatus
{
	protected $_array; 
	
	public function __construct()
	{
		$this->_array =  array(
			null => 'Select One',
			'cart' => 'In Cart',
			'held' => 'Held',
			'payed' => 'Payed',
			'complete' => 'Complete'
		);
		
		return $this;
	}
	
	public function toArray()
	{
		return $this->_array;
	}
}
