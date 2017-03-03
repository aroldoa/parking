<?php
/**
* 
*/
class Resource_Cart_Item
{
	// the spot type id
	public $id;
	public $qty;
	public $from;
	public $to;
	
	public function __construct(Resource_Reservation_Item $reservation, $qty = null)
	{
		# code...
	}
	
	public function getTotal()
	{
		return 1;
	}
}
