<?php
/**
 * My_Model_Resource_Db_Table_Row_Abstract
 *
 * Composite the Zend_Db_Table_Row
 *
 * @category   Storefront
 * @package    Storefront_Model_Resource
 * @copyright  Copyright (c) 2008 Keith Pope (http://www.thepopeisdead.com)
 * @license    http://www.thepopeisdead.com/license.txt     New BSD License
 */
abstract class My_Model_Resource_Db_Table_Row_Abstract
{
    /**
     * @var Zend_Db_Table_Row
     */
    protected $_row = null;
	protected $_data;
	protected $_resultColumns = array();
	protected $_meta;

    public function __construct(array $config = array())
    {
		$this->_data = $config['data'];
        $this->setRow($config);
		$this->init();
    }

	public function init(){}

    public function __get($columnName)
    {
        $lazyLoader = 'get' . ucfirst($columnName);
        if (method_exists($this,$lazyLoader)) {
            return $this->$lazyLoader();
        }
		
		// this was added by me
		if (count($this->_resultColumns) > 0) {
			if (array_key_exists($columnName, $this->_resultColumns) 
				&& (isset($this->_resultColumns[$columnName]))) {
				return $this->_resultColumns[$columnName];
			}
		}
		

		if (isset($this->getRow()->$columnName)) {
			return $this->getRow()->$columnName;
		}
		
		// so was this
		return null;
    }
	
	protected function _setMeta()
	{
		// get table name
		$table = $this->getTable()->info('name');
		
		// create proper cased reference rule name
		$parts = explode('_', $table);
		if (count($parts) > 1) {
			foreach ($parts as $key => $part) {
				$parts[$key] = ucfirst($part);
			}
			$referenceRule = implode('_', $parts);
		} else {
			$referenceRule = ucfirst($table);
		}
		
		// build select for getting dependent rowsets since we need 2 cols to match
		$select = $this->select();
		$select->where('`table` = ?', $table);
		
		$this->_meta = $this->findDependentRowset('Resource_Meta', $referenceRule, $select);
		
		return true;
	}
	
	public function getMeta()
	{
		if (!isset($this->_meta) || count($this->_meta) == 0) {
			$this->_setMeta();
		}
		return $this->_meta;
	}
	
	protected function _setResultColumns() 
	{
		// if page doesn't yet have id, doesn't have properties
		if (!$this->id > 0)
			return;
		
		// set column values from base table
		foreach ($this->getRow() as $column => $value) {
			if (array_key_exists($column, $this->_resultColumns)) {
				$this->_resultColumns[$column] = $value;
			}
		}
		
		// set column values from meta table
		$rows = $this->getMeta();
		foreach ($rows as $row) {
			$key = $row->key;
			$value = $row->value;

			$this->_resultColumns[$key] = $value;
		}

		return true;
	}

	public function toArray() 
	{
		if (count($this->_resultColumns) > 0) {
			return (array) $this->_resultColumns;
		} else {
			return (array)	$this->_data;
		}
		
	}

    public function __isset($columnName)
    {
        return $this->getRow()->__isset($columnName);
    }

    public function __set($columnName, $value)
    {
        return $this->getRow()->__set($columnName, $value);
    }

    public function getRow()
    {
        return $this->_row;
    }

    public function setRow(array $config = array())
    {
        $rowClass = 'Zend_Db_Table_Row';
        if (isset($config['rowClass'])) {
            $rowClass = $config['rowClass'];
        }

        if (is_string($rowClass)) {
            $this->_row = new $rowClass($config);
            return;
        }

        if (is_object($rowClass)) {
            $this->_row = $rowClass;
            return;
        }

        throw new My_Model_Exception('Could not set rowClass in ' . __CLASS__);
    }

    public function __call($method, array $arguments)
    {
        return call_user_func_array(array($this->getRow(), $method), $arguments);
    }

    /**
     * Reconnect the table if we are serialized
     */
    public function __wakeup()
    {
        if (!$this->getRow()->isConnected()) {
            $tableClass = $this->getRow()->getTableClass();
            $table = new $tableClass();
            $this->getRow()->setTable($table);
			$this->init();
        }
    }
}
