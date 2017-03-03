<?php
/**
* 
*/
class Resource_Site_Settings extends My_Model_Resource_Db_Table_Abstract
{
	protected $_name = 'settings';
	
	public function loadSettings()
	{
		$select = $this->select();
		// var_dump($select);
		return $this->fetchAll($select);
	}
	
	public function getSetting($key)
	{
		$select = $this->select();
		$select->where('`key` = ?', $key);
		
		return $this->fetchRow($select);
	}
	
	public function saveSetting($key, $value)
	{
		
		$row = $this->getSetting($key);
		
		if ($row == null) {
			$row = $this->createRow();
		}
		
		$row->key = $key;
		$row->value = $value;
		
		return $row->save();
	}
}
