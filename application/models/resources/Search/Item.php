<?php
/**
* 
*/
class Resource_Search_Item 
// implements SeekableIterator, Countable, ArrayAccess
{
	
	public $id;
	public $ship;
	public $cruise;
	public $lot;
	public $spot;
	public $from;
	public $to;
	public $days;
	public $spot_price;
	public $quantity;
	public $total;
	
	
	public function __construct(Resource_Cruise_Item $cruise, $spot, $qty)
	{
		$this->id = $cruise->id . '_' . $spot->lot->id . '_' .$spot->id;
		$this->ship = $cruise->ship;
		$this->cruise = $cruise;
		$this->lot = $spot->lot;
		$this->spot = $spot;
		$this->from = date('m/d/Y', $cruise->date);
		$this->to = date('m/d/Y', $cruise->return);
		$this->days = (int) $cruise->days;
		$this->spot_price = (int) $spot->getPriceTier($this->days)->price;
		$this->quantity = (int) $qty;
		$this->total = (int) $this->getTotal();
		
		return $this;
	}
	
	public function getTotal()
	{
		return $this->spot_price * $this->quantity;
	}
	
	public function toArray()
	{
		return (array) $this;
	}
}