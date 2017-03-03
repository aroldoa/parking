<?php
/**
* Service_ObjectPagination
* Enables item by item pagination when given pre-ordered result
* set and current Item. 
* 
* @param $objects Object of Multiple objects, which contains current item
* @param $item Current object
* @param $idField optional key to identify current object in "objects"
*/
class Service_ObjectPagination 
{
	protected $_objects = array();
	protected $_objectCount;
	protected $_item;
	protected $_position;
	
	public function __construct($objects = null, $item = null, $idField = 'id')
	{
		$this->_objectCount = $this->setObjects($objects);
		$this->setItem($item, $idField);
		
		return $this;
	}
	
	public function setObjects($objects)
	{
		if ($objects === null) {
			throw new Exception('Objects cannot be null in: ' . __LINE__);
		}
		
		foreach ($objects as $object) {
			$this->_objects[] = $object;
		}
		
		return count($this->_objects);
	}
	
	public function setItem($item, $idField)
	{
		if ($item === null) {
			throw new Exception('Current Item cannot be null in: ' . __LINE__);
		}
		
		foreach ($this->_objects as $index => $object) {
			if ($object->$idField == $item->$idField) {
				$this->_position = $index;
			}
		}
	}
	
	public function current()
	{
		return $this->_objects[$this->_position];
	}
	
	public function getNext()
	{
		$nextPosition = $this->_position + 1;
		if ($nextPosition > ($this->_objectCount - 1)) {
			return null;
		}
		
		return $this->_objects[$nextPosition];
	}
	
	public function getPrev()
	{
		$previousPosition = $this->_position - 1;
		if ($previousPosition < 0) {
			return null;
		}
		
		return $this->_objects[$previousPosition];
	}
	
	public function first()
	{
		return $this->_objects[0];
	}
	
	public function last()
	{
		return $this->_objects[$this->_objectCount - 1];
	}
}