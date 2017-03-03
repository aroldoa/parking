<?php
/**
* 
*/
class Resource_Ship extends My_Model_Resource_Db_Table_Abstract
{
	protected $_name = 'ship';
	protected $_rowClass = 'Resource_Ship_Item';
	protected $_dependentTables = array(
		'Resource_Cruise'
	);
	protected $_referenceMap = array(
		'Lot' => array(
			'columns' => array('lot'),
			'refTableClass' => 'Resource_Lot',
			'refColumns' => array('id')
		),
	);
	
	public function getRowById($id)
	{
		return $this->find($id)->current();
	}
	
	public function getShips($options)
	{
		$select = $this->select();
		
		if (isset($options['status'])) {
			$select->where('status = ?', $options['status']);
		}
		
		return $this->fetchAll($select);
	}
	
	public function deleteRow($row)
	{
		if (!$row instanceof $this->_rowClass) {
			return false;
		}
		
		// get and delete dependent rows?
		foreach ($row->cruises as $cruise) {
			if (!$cruise->delete()) {
				return false;
			}
		}
		
		return $row->delete();
	}
}
