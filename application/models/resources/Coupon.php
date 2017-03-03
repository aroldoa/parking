<?php
/**
* 
*/
class Resource_Coupon extends My_Model_Resource_Db_Table_Abstract
{
	protected $_name = 'coupon';
	protected $_rowClass = 'Resource_Coupon_Item';
	
	public function getRowById($id)
	{
		return $this->find($id)->current();
	}
	
	public function saveRow($info, $row = null)
    {
		
        if (null === $row) {
            $row = $this->createRow();
        }
        
        $columns = $this->info('cols');
        foreach ($columns as $column) {
            if (array_key_exists($column, $info)) {
                $row->$column = $info[$column];
            }
        }
        
        return $row->save();
    }
	
	public function getCoupons($options)
	{
		$select = $this->select();
		
		
		$show = isset($options['show']) ? $options['show'] : 20;
		$page = isset($options['page']) ? $options['page'] : 1;
		
		if (null !== $page) {
			$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
			$count = clone $select;
			$count->reset(Zend_Db_Select::COLUMNS);
			$count->reset(Zend_Db_Select::FROM);
			$count->from($this->_name, new Zend_Db_Expr('COUNT(*) AS `zend_paginator_row_count`'));
			$adapter->setRowCount($count);
			
			$paginator = new Zend_Paginator($adapter);
			$paginator->setItemCountPerPage($show)->setCurrentPageNumber((int) $page);
			
			return $paginator;
		}
		
		return $this->fetchAll($select);
	}
	
	public function getCouponByCouponCode($code)
	{
		$select = $this->select();
		$select->where('code = ?', $code);
		
		return $this->fetchRow($select);
	}
}
