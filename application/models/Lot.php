<?php
/**
* 
*/
class Model_Lot extends My_Model_Abstract
{
	public function save($data = array())
	{
		// var_dump($data);
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
		
		return $this->getResource('Lot')->saveRow($data, $row);
	}
	
	public function delete($row = null)
	{
		if ($row === null) {
			return null;
		}
		
		return $this->getResource('Lot')->deleteRow($row);
	}
	
	public function getRowById($id = null)
	{
		if ($id === null) {
			return null;
		}
		
		$id = (int) $id;
		
		return $this->getResource('Lot')->getRowById($id);
	}
	
	public function getLots()
	{
		return $this->getResource('Lot')->getLots();
	}
}
