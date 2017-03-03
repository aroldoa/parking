<?php
/**
* 
*/
class Resource_User_Item extends My_Model_Resource_Db_Table_Row_Abstract
{
	
	public function getFullname()
	{
		return $this->first_name . ' ' . $this->last_name;
	}
	
	public function getLastLogin()
	{
		if ($this->last_login == null) {
			return 'Never';
		}
		$datetime = date_create($this->getRow()->last_login);
		return date_format($datetime, 'M. jS, Y, g:i a'); //'F jS, Y \a\t H:i'
	}
	
	public function getdateJoined()
	{
		$datetime = date_create($this->getRow()->created);
		return date_format($datetime, 'M. jS, Y'); //'F jS, Y \a\t H:i'
	}
	
	public function getTransactions()
	{
		return $this->findDependentRowset('Resource_Transaction', 'User');
	}
	
	public function getReservations()
	{
		return $this->findDependentRowset('Resource_Reservation', 'User');
	}
}
