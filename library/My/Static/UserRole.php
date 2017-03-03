<?php
/**
* 
*/
class My_Static_UserRole
{
	protected $_roles; 
	
	public function __construct()
	{
		$this->_roles =  array(
			null => 'Select One',
			'customer' => 'Customer',
			'employee' => 'Employee',
			'administrator' => 'Administrator'
		);
		return $this;
	}
	
	public function toArray()
	{
		return $this->_roles;
	}
}
