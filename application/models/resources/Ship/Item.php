<?php
/**
* 
*/
class Resource_Ship_Item extends My_Model_Resource_Db_Table_Row_Abstract
{
	public function getCruises()
	{
		$select = $this->select();
		
		$select->order('date DESC');
		
		return $this->findDependentRowset('Resource_Cruise', 'Ship', $select);
	}
	
	public function getLotName()
	{
		$row = $this->findParentRow('Resource_Lot');
		
		if ($row) {
			return $row->name;
		}
		
		return 'Any';
	}
	
	public function hasCruise($date)
	{
		$select = $this->select();
		
		$select->where('`date` = ?', strtotime($date));
		
		return $this->findDependentRowset('Resource_Cruise', 'Ship', $select);
	}
}
