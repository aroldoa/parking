<?php
/**
* 
*/
class Model_Ship extends My_Model_Abstract
{
	const STATUS_ACTIVE = 'active';
	const STATUS_INACTIVE = 'inactive';
	
	public function save($data = array())
	{
		if (!is_array($data)) {
			return null;
		}
		
		$row = null;
		if (array_key_exists('id', $data)) {
			if ($data['id'] != null) {
				$row = $this->getRowById($data['id']);
			}
		}
		unset($data['id']);
		
		return $this->getResource('Ship')->saveRow($data, $row);
	}
	
	public function delete(Resource_Ship_Item $row)
	{
		if ($row === null) {
			return null;
		}
		
		return $this->getResource('Ship')->deleteRow($row);
	}
	
	public function getShips($options = array())
	{
		if (!is_array($options)) {
			return null;
		}
		
		return $this->getResource('Ship')->getShips($options);
	}
	
	public function getRowById($id = null)
	{
		if ($id === null) {
			return null;
		}
		
		return $this->getResource('Ship')->getRowById($id);
	}
}
