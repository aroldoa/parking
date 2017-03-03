<?php
/**
* 
*/
class Resource_Coupon_Item extends My_Model_Resource_Db_Table_Row_Abstract
{
	public function getExpirationDate($format = null)
	{
		if ($format === null) {
			$format = $this->getDateFormat();
		}
		
		return date($format, $this->expiration);
	}
	
	public function getDateFormat()
	{
		return 'm/d/Y';
	}
	
	public function getDescription()
	{
		if ($this->type == 'fixed') {
			return '$' . $this->value . ' Off!';
		} else if ($this->type == 'percent') {
			return $this->value . '% Off!';
		}
	}
}
