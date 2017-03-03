<?php
/**
* 
*/
class Resource_Spot extends My_Model_Resource_Db_Table_Abstract
{
	protected $_name = 'spot';
	protected $_referenceMap = array(
		'Lot' => array(
			'columns' => array('lot'),
			'refTableClass' => 'Resource_Lot',
			'refColumns' => array('id')
		),
	);
	protected $_rowClass = 'Resource_Spot_Item';
	protected $_dependentTables = array(
		'Resource_Reservation',
		'Resource_Spot_Price'
	);
	
	public function deleteRow($row)
	{
		if (!$row instanceof $this->_rowClass) {
			return false;
		}
		
		// Delete the spots price tiers
		foreach ($row->getPriceTiers() as $tier) {
			
			if (!$tier->delete()) {
				return false;
			}
			
		}
		
		// kill the row!
		return $row->delete();
	}
	
	public function getSpots($options)
	{
		$select = $this->select();
		
		if (isset($options['lot'])) {
			$select->where('`lot` = ?', $options['lot']);
		}
		
		if (isset($options['type'])) {
			$select->where('`type` = ?', $options['type']);
		}
		// echo $select;
		return $this->fetchAll($select);
	}
	
	public function getRowById($id)
	{
		return $this->find($id)->current();
	}
}
