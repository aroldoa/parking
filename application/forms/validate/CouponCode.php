<?php
class Form_Validate_CouponCode extends Zend_Validate_Abstract
{
    const CODE_TAKEN = 'codeTaken';

    protected $_messageTemplates = array(
        self::CODE_TAKEN => '"%value%" is already in use!',
    );

    protected $_model;
    protected $_method;

    public function __construct()
    {
        $this->_model  = new Model_Coupon();
        $this->_method = 'getCouponByCouponCode';

        if (!method_exists($this->_model, $this->_method)) {
            throw new My_Exception('Method ' . $method . 'does not exist in model');
        }
    }

    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
		$coupon = $this->_model->getCouponByCouponCode($value);
		
		// if we have a result
		if ($coupon) {
			if (isset($context['id'])) {
				
				if ($coupon->id = $context['id']) {
					return true;
				}
				
			}
			$this->_error(self::CODE_TAKEN);
			return false;
		}
		
		return true;
    }
}