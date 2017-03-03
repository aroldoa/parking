<?php
/**
* 
*/
class Resource_Reservation_Item extends My_Model_Resource_Db_Table_Row_Abstract
{
	protected $_user;
	protected $_lot;
	protected $_spot;
	protected $_cruise;
	
	public function getUser()
	{
		if (null === $this->_user) {
			$this->_user = $this->findParentRow('Resource_User');
		}
		return $this->_user;
	}
	
	public function getFromDate($format = null)
	{
		if ($format === null) {
			$format = $this->getDateFormat();
		}
		
		return date($format, $this->from);
	}
	
	public function getToDate($format = null)
	{
		if ($format === null) {
			$format = $this->getDateFormat();
		}

		return date($format, $this->to);
	}
	
	public function getCreated($format = null)
	{
		if ($format === null) {
			$format = $this->getDateFormat();
		}

		return date($format, $this->getRow()->created);
	}
	
	public function getDateFormat()
	{
		return 'm/d/Y';
	}
	
	public function getDays()
	{
		if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
			$datetime1 = new DateTime($this->getFromDate());
			$datetime2 = new DateTime($this->getToDate());
			$interval = $datetime1->diff($datetime2);
			
			return $interval->format('%r%a days');
		} else {
			// changed to following format if php < 5.3
			$days = ($this->to - $this->from) / (60 * 60 * 24);
			
			return ceil($days) . ' days';
		}
	}
	
	public function getLot()
	{
		if (null === $this->_lot) {
			$this->_lot = $this->findParentRow('Resource_Lot');
		}
		
		return $this->_lot;
	}
	
	public function getSpot()
	{
		if (null === $this->_spot) {
			$this->_spot = $this->findParentRow('Resource_Spot');
		}
		
		return $this->_spot;
	}
	
	public function getTransaction()
	{
		if (null == $transaction = $this->findParentRow('Resource_Transaction')) {
			$transaction = new stdClass();
			$transaction->transaction_id = 'Cash';
			$transaction->amount = '';
		}
		
		return $transaction;
		// return $this->getRow()->transaction;
	}
	
	public function getCruise()
	{
		if (null === $this->_cruise) {
			$this->_cruise = $this->findParentRow('Resource_Cruise');
		}
		return $this->_cruise;
	}
	
	public function getStatus()
	{
		if ($this->getRow()->status == '') {
			return 'Not Set';
		}
		
		return $this->getRow()->status;
	}
}
