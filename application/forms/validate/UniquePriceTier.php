<?php
class Form_Validate_UniquePriceTier extends Zend_Validate_Abstract
{
    const EXISTS = 'tierExists';

    protected $_messageTemplates = array(
        self::EXISTS => 'This spot already has a price tier for that number of days'
    );

	protected $_spot;
	protected $_model;

	public function __construct()
	{
		$this->_model = new Model_Spot();
	}

    public function isValid($value, $context = null)
    {
        $value = (int) $value;
        $this->_setValue($value);

		$this->_spot = $this->_model->getRowById($context['spot']);
		
		if ($tier = $this->_spot->getPriceTier($value)) {
			
			if (isset($context['id']) && $tier->id == $context['id']) {
				return true;
			}
			
			$this->_error(self::EXISTS);
			return false;
		}
		
		return true;
    }
}
