<?php
/**
* 
*/
class Model_Cruise extends My_Model_Abstract
{
	
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
		
		$data['date'] = strtotime($data['date']);

		return $this->getResource('Cruise')->saveRow($data, $row);
	}
	
	public function delete(Resource_Cruise_Item $row)
	{
		if ($row === null) {
			return null;
		}
		
		return $this->getResource('Cruise')->deleteRow($row);
	}
	
	public function getRowById($id = null)
	{
		if ($id === null) {
			return null;
		}
		
		return $this->getResource('Cruise')->getRowById($id);
	}
	
	public function getCruises($options = array())
	{
		if (!is_array($options)) {
			return null;
		}
		
		$defaults = array(
			'order' => array('date DESC')
		);
		
		foreach ($defaults as $k => $v) {
			$options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
		}
		
		return $this->getResource('Cruise')->getCruises($options);
	}
	
	public function getCruise($data = array())
	{
		if (!is_array($data)) {
			return null;
		}
		
		return $this->getResource('Cruise')->getCruise($data);
	}
	
}
