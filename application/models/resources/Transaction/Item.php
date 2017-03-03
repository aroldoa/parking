<?php
/**
* id, transaction_id, productId, productName, productQty, productPrice
*/
class Resource_Transaction_Item extends My_Model_Resource_Db_Table_Row_Abstract
{
	public function getUser()
	{
		return $this->findParentRow('Resource_User');
	}
	
	public function getReservations()
	{
		return $this->findDependentRowset('Resource_Reservation');
	}
}
