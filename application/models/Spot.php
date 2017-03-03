<?php
/**
* 
*/
class Model_Spot extends My_Model_Abstract
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
		
		return $this->getResource('Spot')->saveRow($data, $row);
	}
	
	public function delete($row = null)
	{
		if ($row === null) {
			return null;
		}
		
		return $this->getResource('Spot')->deleteRow($row);
	}
	
	public function getSpots($options = array())
	{
		if (!is_array($options)) {
			return null;
		}
		
		$defaults = array();
		
		return $this->getResource('Spot')->getSpots($options);
	}
	
	public function getSpot($options = array())
	{
		return $this->getSpots()->current();
	}
	
	public function getRowById($id = null)
	{
		if ($id == null) {
			return null;
		}
		
		$id = (int) $id;
		
		return $this->getResource('Spot')->getRowById($id);
	}
	
	public function getPriceTier($tierId = null)
	{
		if (null === $tierId) {
			return null;
		}
		
		return $this->getResource('spotPrice')->find($tierId)->current();
	}
	
	public function savePriceTier($data = null)
	{
		if (null === $data) {
			return null;
		}
		
		$tier = null;
		if (isset($data['id'])) {
			$tier = $this->getPriceTier($data['id']);
			unset($data['id']);
		}
		
		return $this->getResource('spotPrice')->saveRow($data, $tier);
	}
	
	public function deleteTier(Resource_Spot_Price_Item $tier)
	{
		$tier->delete();
	}
}
