<?php
/**
* 
*/
class Model_Coupon extends My_Model_Abstract
{
	public function saveCoupon($data = array())
	{
		if (empty($data)) {
			return null;
		}
		
		$row = null;
		if (array_key_exists('id', $data)) {
			if ($data['id'] != null) {
				$row = $this->getRowById($data['id']);
			}
		}
		unset($data['id']);
		
		$data['expiration'] = strtotime($data['expiration']);
		
		return $this->getResource('coupon')->saveRow($data, $row);
	}
	
	public function deleteCoupon(Resource_Coupon_Item $coupon)
	{
		return $this->getResource('coupon')->deleteRow($coupon);
	}
	
	public function getRowById($id = null)
	{
		if (null === $id) {
			return null;
		}
		
		return $this->getResource('coupon')->getRowById($id);
	}
	
	public function getCoupons($options = array())
	{
		$defaults = array(
			'order' => array('from DESC', 'type ASC'),
		);
		
		foreach ($defaults as $k => $v) {
			$options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
		}
		
		return $this->getResource('coupon')->getCoupons($options);
	}
	
	public function getCouponByCouponCode($code = null)
	{
		if (null === $code) {
			return null;
		}
		
		return $this->getResource('coupon')->getCouponByCouponCode($code);
	}
}
