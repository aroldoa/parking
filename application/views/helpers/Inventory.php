<?php
/**
* 
*/
class Zend_View_Helper_Inventory extends Zend_View_Helper_Abstract
{
	public $lot;
	public $month;
	public $year;
	public $prevYear;
	public $nextYear;
	public $prevMonth;
	public $nextMonth;
	protected $_timestamp;
	protected $_maxDay;
	protected $_startDay;
	
	
	public function inventory()
	{
		$this->month = $this->view->month;
		$this->year = $this->view->year;
		$this->lot = $this->view->lot;
		
		$this->prevYear = $this->year;
		$this->nextYear = $this->year;
		$this->prevMonth = $this->month - 1;
		$this->nextMonth = $this->month + 1;

		if ($this->prevMonth == 0 ) {
		    $this->prevMonth = 12;
		    $this->prevYear = $this->year - 1;
		}
		if ($this->nextMonth == 13 ) {
		    $this->nextMonth = 1;
		    $this->nextYear = $this->year + 1;
		}
		
		$this->_timestamp = mktime(0,0,0,$this->month,1,$this->year);
		$this->_maxDay = date("t",$this->_timestamp);
		$thismonth = getdate ($this->_timestamp);
		$this->_startDay = $thismonth['wday'];
		
		return $this;
	}
	
	public function showMonth()
	{
		$this->view->assign(array(
			'maxDay' => $this->_maxDay,
			'startDay' => $this->_startDay,
			'nextMonth' => $this->nextMonth,
			'nextYear' => $this->nextYear,
			'prevMonth' => $this->prevMonth,
			'prevYear' => $this->prevYear
		));
		return $this->view->render('inventory/_calendar.phtml');
	}
}
