<?php
/**
* 
*/
class Resource_Cruise extends My_Model_Resource_Db_Table_Abstract
{
	protected $_name = 'cruise';
	protected $_rowClass = 'Resource_Cruise_Item';
	protected $_referenceMap = array(
		'Ship' => array(
			'columns' => array('ship'),
			'refTableClass' => 'Resource_Ship',
			'refColumns' => array('id')
		),
	);
	protected $_dependentTables = array(
		'Resource_Reservation'
	);
	
	public function getRowById($id)
	{
		return $this->find($id)->current();
	}
	
	public function getCruises($options)
	{
		$select = $this->select();
		
		if (isset($options['order'])) {
			$select->order($options['order']);
		}
		
		if (isset($options['ship'])) {
			$select->where('`ship` = ?', $options['ship']);
		}
		
		if (isset($options['from'])) {
			$select->where('`date` >= ?', $options['from']);
		}
		
		if (isset($options['year'])) {
			$select->where('date >= ?', mktime(0, 0, 0, 1, 1, $options['year']));
			$select->where('date <= ?', mktime(0, 0, 0, 1, 1, $options['year'] + 1));
		}
		
		$page = isset($options['page']) ? $options['page'] : null;
		$show = isset($options['show']) ? $options['show'] : 20;
		
		if (null !== $page) {
			$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
			$count = clone $select;
			$count->reset(Zend_Db_Select::COLUMNS);
			$count->reset(Zend_Db_Select::FROM);
			$count->from('cruise', new Zend_Db_Expr('COUNT(*) AS `zend_paginator_row_count`'));
			$adapter->setRowCount($count);
			
			$paginator = new Zend_Paginator($adapter);
			$paginator->setItemCountPerPage($show)->setCurrentPageNumber((int) $page);
			
			return $paginator;
		}
		
		return $this->fetchAll($select);
	}
	
	public function getCruise($data)
	{
		$select = $this->select();
		
		if (isset($data['ship'])) {
			$select->where('ship = ?', $data['ship']);
		}
		
		if (isset($data['date'])) {
			$select->where('date = ?', strtotime($data['date']));
		}
		
		return $this->fetchRow($select);
	}
}
