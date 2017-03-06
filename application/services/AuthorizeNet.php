<?php
/**
*
*/
require_once('My/PaymentGateway/AuthorizeNet/AuthorizeNet.php');
class Service_AuthorizeNet
{
	protected $_apiLoginId; // = "3v3qH6G5"
	protected $_transactionKey; // = "97rEf5pcg6G52P7a"
	protected $_sandbox = true;
	protected $_testRequest = false;
	protected $_transactionMethod = 'AIM';
	protected $_transaction;
	protected $_merchantSet = false;
	protected $_cartSet = false;
	protected $_billigSet = false;
	protected $_response;
	protected $_merchantName;

	public function __construct($options = null)
	{
		if ($options === null) {
			$options = new Zend_Config_Ini(APPLICATION_PATH . '/configs/authorize.net.ini', APPLICATION_ENV);
		}
		if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        } elseif ($options instanceof Model_SiteSettings) {
        	$this->setOptions($options);
        }

		$this->_setTransactionMethod($options);
		$this->_setMerchantData();
	}

	public function setOptions(array $options)
	{
		if (isset($options['apiLoginId'])) {
			$this->_apiLoginId = $options['apiLoginId'];
			define('AUTHORIZENET_API_LOGIN_ID', $this->_apiLoginId);
			unset($options['apiLoginId']);
		}
		// echo $this->_apiLoginId;

		if (isset($options['transactionKey'])) {
			$this->_transactionKey = $options['transactionKey'];
			define('AUTHORIZENET_TRANSACTION_KEY', $this->_transactionKey);
			unset($options['transactionKey']);
		}

		if (isset($options['sandbox'])) {

			if ($options['sandbox'] == 'false') {
				define('AUTHORIZENET_SANDBOX', false);
			}
			unset($options['sandbox']);
		}

		if (isset($options['testRequest'])) {
			$this->_testRequest = $options['testRequest'];
			unset($options['testRequest']);
		}

		if (isset($options['transactionMethod'])) {
			$this->_transactionMethod = $options['transactionMethod'];
			unset($options['transactionMethod']);
		} else {
			$this->_transactionMethod = "AIM";
		}

		if (isset($options['logfile'])) {
			define('AUTHORIZENER_LOG_FILE', $options['logfile']);
		} else {
			define('AUTHORIZENET_LOG_FILE', APPLICATION_PATH . '/../data/logs/authorizenet.log');
		}

		$this->_merchantName = $options['siteName'];
	}

	public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }

	protected function _setTransactionMethod()
	{
		if (isset($options['transactionMethod'])) {
			$this->_transactionMethod = $options['transactionMethod'];
		}

		switch (strtolower($this->_transactionMethod)) {
			case 'aim':
				$this->_transaction = new AuthorizeNetAIM;
				break;

			default:
				throw new AuthorizeNetException('Transaction method can only be "AIM"');
				break;
		}
	}

	protected function _setMerchantData()
	{
		$merchant = new stdClass();
		$merchant->login = $this->_apiLoginId;
		$merchant->tran_key = $this->_transactionKey;
		$merchant->allow_partial_auth = "false";
		if (APPLICATION_ENV != 'production') {
			$merchant->duplicate_window = '10';
		}


		$this->_transaction->setFields($merchant);
		$this->_merchantSet = true;
	}

	public function setCartValues(Model_Cart $cart)
	{
		$transaction = new stdClass();
		$transaction->amount = $cart->getTotal();
		$transaction->tax = $cart->getTaxCost();
		$transaction->description = '';

		foreach ($cart as $item) {
			$name = $item->spot->keyToType . ' Parking Spot';
			$description = $item->cruise->ship->name . ' ' . $item->cruise->sailDate;

			$this->_transaction->addLineItem( // id, name, description, quantity, unit_price, taxable
				$item->id,
				$name,
				$description,
				$item->quantity,
				$item->spot_price,
				'Y'
			);
			$transaction->description .= $description . ' | ';
		}

		$transaction->description = $this->_merchantName;
		$this->_transaction->setFields($transaction);
		$this->_cartSet = true;
	}

	public function setBillingValues($values)
	{
		$customer = new stdClass();
		$addressKeys = array(
			'first_name',
			'last_name',
			'address',
			'city',
			'state',
			'zip',
			'country'
		);

		foreach ($values as $key => $value) {
			$customer->$key = $values[$key];
			// proxy in shipping info
			if (in_array($key, $addressKeys)) {
				$ship_key = 'ship_to_' . $key;
				$customer->$ship_key = $values[$key];
			}
		}

		$this->_transaction->setFields($customer);
		$this->_billingSet = true;
	}

	public function processPayment()
	{
		if (!$this->_merchantSet || !$this->_cartSet || !$this->_billingSet) {
			throw new Exception('All necessary transaction data has not been set');
			// return false;
		}

		$this->_response = $this->_transaction->authorizeAndCapture();

		if ($this->_response->approved) {
			return true;
		}

		return false;
	}

	public function getResponse()
	{
		if (isset($this->_response)) {
			return $this->_response;
		}
	}
}
