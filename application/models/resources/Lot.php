<?php
/**
* 
*/
class Resource_Lot extends My_Model_Resource_Db_Table_Abstract
{
	protected $_name = 'lot';
	protected $_rowClass = 'Resource_Lot_Item';
	protected $_dependentTables = array(
		'Resource_Spot'
	);
	protected $_referenceMap = array(
		'Reservation' => array(
			'columns' => array('id'),
			'refTableClass' => 'Resource_Reservation',
			'refColumns' => array('lot')
		),
	);
	
	public function getRowById($id)
	{
		return $this->find($id)->current();
	}
	
	public function getLots()
	{
		return $this->fetchAll($this->select());
	}
	
	public function deleteRow($row)
	{
		if (!$row instanceof $this->_rowClass) {
			return false;
		}
		
		// Delete the lots spots
		$sModel = new Model_Spot();
		foreach ($row->getSpots() as $spot) {
			
			if (!$sModel->delete($spot)) {
				return false;
			};
			
		}
		
		// kill the row!
		return $row->delete();
	}
}
