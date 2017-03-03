<?php
/**
* 
*/
class Resource_Cruise_Item extends My_Model_Resource_Db_Table_Row_Abstract
{
	public function getShip()
	{
		return $this->findParentRow('Resource_Ship');
	}
	
	public function getSailDate($format = null)
	{
		if ($format === null) {
			$format = $this->getDateFormat();
		}
		
		return date($format, $this->date);
	}
	
	public function getReturn()
	{
		$dt = new DateTime($this->sailDate);
		$dt->modify('+'.$this->days.' days');
		
		return strtotime($dt->format($this->dateFormat));
	}
	
	public function getDateFormat()
	{
		return 'm/d/Y';
	}
}
