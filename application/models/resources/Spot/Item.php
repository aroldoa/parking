<?php
/**
*
*/
class Resource_Spot_Item extends My_Model_Resource_Db_Table_Row_Abstract
{

	public function getParent()
	{
		return $this->findParentRow('Resource_Lot');
	}

	public function getLot()
	{
		return $this->getParent();
	}

	public function getSpotReservations($options = null)
	{
		$select = $this->getTable()->select();

		if ($options != null) {

			if (isset($options['from']) && isset($options['to'])) {

				$from = $options['from'];

				$days = array();
				$days[] = $from;

				while ($from < $options['to']) {
					$days[] = $from += (60 * 60 * 24);
				}

				$from = $options['from'];
				$to = $options['to'];

				$select->where('`from` <= ?', $from + 86399);

				$select->where('`to` >= ?', $to );
			}
		}

        // Get the wide range rowset
		$rowSet = $this->findDependentRowset('Resource_Reservation', 'Spot', $select);

        // Narrow down the results to just those in range
        $reservations = array();
        foreach ($rowSet as $reservation) {
            $reservations[] = $reservation;
        }

        if (count($reservations) > 0) {
            return $reservations;
        }

        return null;
	}

	public function inventoryRemaining($options)
	{
		$reservations = $this->getSpotReservations($options);

		if (null === $reservations) {
			return $this->quantity;
		}

        // set up some placeholders
		$from = $options['from'];
		$to = $options['to'];
		$oneDay = 60 * 60 * 24;
		$point = $from;
		$dailyQuantities = array(
			0 => 0
		);

        // looping through the days finding the max reserved duing timespan
		while ($point <= $to) {
			$sum = 0;

            foreach ($reservations as $reservation) {
				$sum += $reservation->quantity;
			}
			$dailyQuantities[] = $sum;
			$point += $oneDay;
		}

        // echo max($dailyQuantities);
		return $this->quantity - max($dailyQuantities);
	}

	public function getPriceTiers()
	{
		$select = $this->select();

		$select->order(array('days ASC'));
		return $this->findDependentRowset('Resource_Spot_Price', 'Spot', $select);
	}

	public function getPriceTier($days = null)
	{
		if (null === $days) {
			return null;
		}

		$select = $this->select();
		$select->where('days = ?', $days);

		return $this->findDependentRowset('Resource_Spot_Price', 'Spot', $select)->current();
	}

	public function getKeyToType()
	{
		$pattern = array(
			'/-/',
			'/_/'
		);
		$replace = array(
			'/',
			' '
		);

		$text = preg_replace($pattern, $replace, $this->type);

		return $text;
	}
}
