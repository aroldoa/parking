<?php
/**
* 
*/
class Model_Transaction extends My_Model_Abstract
{
	
	public function saveTransaction(Resource_User_Item $user, AuthorizeNetAIM_Response $response, Model_Cart $cart)
	{
		return $this->getResource('Transaction')->saveTransaction($user, $response, null, $cart);
	}
	
	public function getTransaction($transaction_id = null)
	{
		if ($transaction_id === null) {
			return null;
		}
		return $this->getResource('Transaction')->getTransaction($transaction_id);
	}
	
	public function getTransactions($options = array())
	{
		$defaults = array(
			'order' => array('ts_created DESC')
		);
		
		foreach ($defaults as $k => $v) {
			$options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
		}
		
		return $this->getResource('transaction')->getTransactions($options);
	}
	
	public function getTransactionData($page, $recordPerPage)
	{
				
		return $this->getResource('transaction')->getTransactionData($page, $recordPerPage);
	}
}
