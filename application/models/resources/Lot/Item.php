<?php
/**
* 
*/
class Resource_Lot_Item extends My_Model_Resource_Db_Table_Row_Abstract
{
	protected $_spots;
	
	public function getSpots()
	{
		if (!$this->_spots) {
			$this->setSpots();
		}
		return $this->_spots;
	}
	
	public function setSpots()
	{
		$this->_spots = $this->findDependentRowset('Resource_Spot', 'Lot');
	}
	
	public function getSpotsum()
	{
		$sum = 0;
		foreach ($this->spots as $spot) {
			$sum += $spot->quantity;
		}
		
		return $sum;
	}
	
	public function getPricesum()
	{
		$sum = 0;
		foreach ($this->_spots as $spot) {
			$sum += $spot->price * $spot->quantity;
		}
		
		return $sum;
	}
	
	public function getGoogleMapUrl()
	{
		$link = 'http://maps.google.com/maps?q=';
		
		$string = $this->address . ' ' . $this->zip;
		
		$link .= str_replace(' ', '+', $string);
		
		return $link;
	}
}
