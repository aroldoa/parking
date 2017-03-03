<?php
/**
* 
*/
class Model_Reservation extends My_Model_Abstract
{
	protected $_models = array();
	
	public function save($data = array(), Resource_User_Item $user)
	{
		if (!is_array($data)) {
			return null;
		}
		
		// load row if already exists
		$row = null;
		if (array_key_exists('id', $data)) {
			if ($data['id'] != null) {
				$row = $this->getRowById($data['id']);
			}
		}
		
		unset($data['id']);
		
		// set the user id value ??
		$data['user'] = $user->id;
		
		return $this->getResource('Reservation')->saveRow($data, $row);
	}
	
	/**
	 * This function prepares data processed from cart and then uses save()
	 */
	public function saveReservation(Resource_Search_Item $item, Resource_User_Item $user, Resource_Transaction_Item $transaction)
	{
		$data = array();
		$data['lot'] = $item->lot->id;
		$data['type'] = $item->spot->type;
		$data['quantity'] = $item->quantity;
		$data['from'] = $item->from;
		$data['to'] = $item->to;
		$data['status'] = 'payed';
		$data['transaction'] = $transaction->transaction_id;
		$data['cruise'] = $item->cruise->id;
		
		return $this->save($data, $user);
	}
	
	public function delete($row = null)
	{
		if ($row === null) {
			return null;
		}
		
		return $this->getResource('Reservation')->deleteRow($row);
	}
	
	public function getReservations($options = array())
	{
		if (!is_array($options)) {
			return null;
		}
		
		$defaults = array(
			'order' => array('from DESC', 'type ASC'),
		);
		
		foreach ($defaults as $k => $v) {
			$options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
		}
		
		return $this->getResource('Reservation')->getReservations($options);
	}
	
	public function getRowById($id = null)
	{
		if ($id === null) {
			return null;
		}
		
		$id = (int) $id;
		
		return $this->getResource('Reservation')->getRowById($id);
	}
	
	/*
		Working - $held
	*/
	public function checkAvailabilityByCruise(Resource_Cruise_Item $cruise, $type = null, $qty = null, $held = null)
	{
		if ($qty === null || $type === null) {
			return null;
		}
		
		// if (null !== $held) {
		// 		$qty = $qty - $held;
		// 	}
		
		// set our options from the sanitizes post data to seed our queries
		$options = array(
			'from' => (int)$cruise->date,
			'to' => (int)$cruise->return,
			'type' => $type,
			'quantity' => $qty
		);
		
		$spotModel = $this->getModel('Spot');
		
		// get the spots in inventory that match the spot type requested
		$spots = $spotModel->getSpots($options);
		$results = array();
		
		// loop through each matched spot type, 
		// get the reservations that match the spot row for the requested dates
		// loop through the matched reservations to get a sum quantity of spots
		// to take out of inventory
		foreach ($spots as $spot) {
			if ($spot->lot->status == 'Closed') {
				continue;
			}
			
			// if the spots remaining in inventory are greater than the 
			// number of spots requested add the spot to the result array
			if ($spot->inventoryRemaining($options) >= $qty) {
				// make sure their is a price tier set for that spot/day combo!!
				if (null !== $spot->getPriceTier($cruise->days)) {
					$searchResult = new Resource_Search_Item($cruise, $spot, $qty);
					$results[$searchResult->id] = $searchResult;
				}
				
			}
		}
		
		// return the array if it has matches
		if (count($results) > 0) {
			return $results;
		}
		
		return null;
	}
	
	public function checkAvailabilityByDate($from, $to, $type, $qty, $held = null)
	{
		if ($qty === null || $type === null) {
			return null;
		}
		
		if (null !== $held) {
			$qty = $qty - $held;
		}
		
		// set our options from the sanitizes post data to seed our queries
		$options = array(
			'from' => (int) $from,
			'to' => (int) $to,
			'type' => $type,
			'quantity' => $qty
		);
		
		// get the spots in inventory that match the spot type requested
		$spots = $this->getModel('Spot')->getSpots($options);

		foreach ($spots as $spot) {
			if ($spot->lot->status == 'Closed') {
				continue;
			}
			
			// if the spots remaining in inventory are greater than the 
			// number of spots requested add the spot to the result array
			if ($spot->inventoryRemaining($options) >= $qty) {
				return true;
			}
		}
		
		return false;
	}
	
	protected function getModel($model = null)
	{
		if ($model === null) {
			return null;
		}
		
		if (isset($this->_models[$model])) {
			return $this->_models[$model];
		}
		
		$name = 'Model_' . $model;
		
		return $this->_models[$model] = new $name();
	}
	
	public function getReservationData($page, $recordPerPage)
	{
		return $this->getResource('Reservation')->getReservationData($page, $recordPerPage);
	}
}
