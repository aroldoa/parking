<?php
class Form_Validate_CruiseDate extends Zend_Validate_Abstract
{
    const INVALID_CRUISE = 'cruiseDoesntExist';
	const RESERVATION_CLOSED = 'reservationAfterCutoff';

    protected $_messageTemplates = array(
        self::INVALID_CRUISE => '%ship%" doesn\'t sail on "%value%"',
		self::RESERVATION_CLOSED => 'Online reservations closed for this cruise'
    );

	public $ship = 'unkown';
	protected $_messageVariables = array(
        'ship' => 'ship'
    );

    protected $_model;
    protected $_method;

    public function __construct()
    {
        $this->_model  = new Model_Cruise();
        $this->_method = 'getCruise';

        if (!method_exists($this->_model, $this->_method)) {
            throw new My_Exception('Method ' . $method . 'does not exist in model');
        }
    }

    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
		$cruise = $this->_model->getCruise($context);
		
		// make sure there is a cruise for that date
		if ($cruise) {
			// make sure they aren't trying to book on/after the date of cruise
			if (strtotime($cruise->sailDate) <= strtotime('today')) {
				$this->_error(self::RESERVATION_CLOSED);
				return false;
			}
			
			return true;
		}
		
		$this->setShipName($context);

        $this->_error(self::INVALID_CRUISE);
        return false;
    }

	public function setShipName($context)
	{
		$shipModel = new Model_Ship();
		
		$ship = $shipModel->getRowById($context['ship']);
		
		$this->ship = $ship->name;
		return $ship->name;
	}
}