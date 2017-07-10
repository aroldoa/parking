<?php
/**
*
*/
class Resource_Reservation extends My_Model_Resource_Db_Table_Abstract
{
	protected $_name = 'reservation';
	protected $_rowClass = 'Resource_Reservation_Item';
	protected $_referenceMap = array(
		'User' => array(
			'columns' => array('user'),
			'refTableClass' => 'Resource_User',
			'refColumns' => array('id')
		),
		'Spot' => array(
			'columns' => array('lot', 'type'),
			'refTableClass' => 'Resource_Spot',
			'refColumns' => array('lot', 'type')
		),
		'Lot' => array(
			'columns' => array('lot'),
			'refTableClass' => 'Resource_Lot',
			'refColumns' => array('id')
		),
		'Transaction' => array(
			'columns' => array('transaction'),
			'refColumns' => array('transaction_id'),
			'refTableClass' => 'Resource_Transaction'
		),
		'Cruise' => array(
			'columns' => array('cruise'),
			'refColumns' => array('id'),
			'refTableClass' => 'Resource_Cruise'
		),
	);

	public function saveRow($info, $row = null)
    {

        if (null === $row) {
            $row = $this->createRow();
			$row->created = time();
			$row->transaction = 'Cash';
        }

        $columns = $this->info('cols');
        foreach ($columns as $column) {
            if (array_key_exists($column, $info)) {
                $row->$column = $info[$column];
            }
        }

		$row->from = strtotime($info['from']);
		$row->to = strtotime($info['to']);

        return $row->save();
    }

	public function getReservations($options)
	{
		$select = $this->select();

		if (isset($options['from'])) {

			$select->where('`from` >= ?', $options['from']);
			$select->where('`from` <= ?', $options['from']+86399);
		}

		if (isset($options['to'])) {
			$select->where('`to` >= ?', $options['to']);
			$select->where('`to` <= ?', $options['to']+86399);
		}

		if (isset($options['type']) && $options['type'] != null) {
			$select->where('`type` = ?', $options['type']);
		}

		if (isset($options['order'])) {
			$select->order($options['order']);
		}

		if (isset($options['lot']) && $options['lot'] != null) {
			$select->where('`lot` = ?', $options['lot']);
		}

		if (isset($options['user']) && $options['user'] !== null) {
			$select->where('`user` = ?', $options['user']);
		}

		if (isset($options['transaction']) && $options['transaction'] !== null) {
			$select->where('`transaction` = ?', $options['transaction']);
		}

		if (isset($options['date']) && $options['date'] !== null) {
			$select->where('`from` <= ?', $options['date']);
			$select->where('`to` >= ?', $options['date']);
		}

		if (isset($options['status'])) {
			$select->where('`status` = ?', $options['status']);
		}

		$show = isset($options['show']) ? $options['show'] : 20;
		$page = isset($options['page']) ? $options['page'] : null;

		if (null !== $page && $show != 'all') {
			$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
			$count = clone $select;
			$count->reset(Zend_Db_Select::COLUMNS);
			$count->reset(Zend_Db_Select::FROM);
			$count->from('reservation', new Zend_Db_Expr('COUNT(*) AS `zend_paginator_row_count`'));
			$adapter->setRowCount($count);

			$paginator = new Zend_Paginator($adapter);
			$paginator->setItemCountPerPage($show)->setCurrentPageNumber((int) $page);

			return $paginator;
		}

		return $this->fetchAll($select);
	}

	public function getRowById($id)
	{
		return $this->find($id)->current();
	}

	public function getReservationData($page, $recordPerPage) {
        $select = $this->select();
        $select->order(array('from DESC', 'type ASC'));
        $select->limit($recordPerPage,($page-1)*$recordPerPage);
        return $this->fetchAll($select);
	}
}
