<?php
/**
* 
*/
class Model_Cart extends My_Model_Abstract 
implements SeekableIterator, Countable, ArrayAccess
{
	protected $_sessionNamespace;
	protected $_items = array();
	protected $_subTotal = 0;
	protected $_total = 0;
	protected $_taxRate;
	protected $_tax = 0;
	protected $_coupon;
	protected $_discount = 0;
	
	public function init()
	{
		$this->loadSession();
	}
	
	public function loadSession()
	{
		if (isset($this->getSessionNs()->items)) {
			$this->_items = $this->getSessionNs()->items;
		}
		
		if (isset($this->getSessionNs()->tax)) {
			$this->_tax = $this->getSessionNs()->tax;
		}
		
		if (isset($this->getSessionNs()->coupon)) {
			$this->_coupon = $this->getSessionNs()->coupon;
			// var_dump($this->_coupon);
		}
		
		if (isset($this->getSessionNs()->discount)) {
			$this->_discount = $this->getSessionNs()->discount;
		}
	}
	
	public function getSessionNs()
	{
		if (null === $this->_sessionNamespace) {
			$this->setSessionNs(new Zend_Session_Namespace(__CLASS__));
		}
		
		return $this->_sessionNamespace;
	}
	
	public function setSessionNs(Zend_Session_Namespace $ns)
	{
		$this->_sessionNamespace = $ns;
	}
	
	public function persist()
	{
		$this->getSessionNs()->items = $this->_items;
		$this->getSessionNs()->tax = $this->_tax;
		$this->getSessionNs()->coupon = $this->_coupon;
		$this->getSessionNs()->discount = $this->_discount;
	}
	
	public function CalculateTotals()
	{
		$sub = 0;
		foreach ($this as $item) {
			$sub = $sub + $item->getTotal();
		}
		
		$this->_subTotal = $sub;
		
		if ($this->_coupon) {
			$this->applyCoupon();
		}
		// if ($this->_discount) {
		// 			$this->_subTotal = $sub - $this->_discount;
		// 		}
		
		// get tax rate
		$this->setTaxRate();
		// calculate cart tax
		$this->_tax = round($this->_subTotal * $this->_taxRate, 2);
		
		$this->_total = $this->_subTotal + $this->_tax;
		// $log->notice($this->_total);
	}
	
	public function addItem($item, $quantity)
	{
		if (0 > $quantity) {
			return false;
		}
		
		if (0 == $quantity) {
			$this->removeItem($item);
			return false;
		}
		
		$item->quantity = $quantity;
		
		$this->_items[$item->id] = $item;
		
		// reset the discount value
		$this->_discount = 0;
		
		$this->persist();
		return $item;
	}
	
	public function removeItem($item)
	{
		unset($this->_items[$item->id]);
		
		$this->persist();
	}
	
	public function emptyCart()
	{
		foreach ($this->_items as $item) {
			$this->removeItem($item);
		}
		
		unset($this->_tax);
		unset($this->_discount);
		$this->persist();
	}
	
	public function getSubTotal() 
	{
		$this->CalculateTotals();
		return $this->_subTotal;
	}
	
	public function getTotal() 
	{
		$this->CalculateTotals();
		return $this->_total;
	}
	
	public function setTaxCost($tax)
	{
		$this->_tax = $tax;
		$this->CalculateTotals();
		$this->persist();
	}
	
	public function getTaxCost()
	{
		$this->CalculateTotals();
		return $this->_tax;
	}
	
	public function setTaxRate()
	{
		$settings = new Model_SiteSettings();
		// $settings = $settings->getSettings();
		$this->_taxRate = $settings->taxRate;
		
		// correct for different ways of entering %'s
		if ($this->_taxRate > 1) {
			$this->_taxRate = $this->_taxRate / 100;
		}
	}
	
	public function getTaxRate()
	{
		$this->CalculateTotals();
		return $this->_taxRate;
	}
	
	public function setCoupon(Resource_Coupon_Item $coupon)
	{
		$this->_coupon = $coupon;
		$this->CalculateTotals();
		$this->persist();
	}
	
	public function getCoupon()
	{
		return $this->_coupon;
	}
	
	public function applyCoupon()
	{
		if ($this->_coupon->type == 'percent') {
			
			$this->_discount = round($this->_subTotal * ($this->_coupon->value / 100), 2);
			
		} else if ($this->_coupon->type == 'fixed') {
			
			$this->_discount = $this->_coupon->value;
			
		}

		$this->_subTotal = $this->_subTotal - $this->_discount;
	}
	
	public function setDiscount($discount)
	{
		$this->_discount = $discount;

		$this->CalculateTotals();
		$this->persist();
	}
	
	public function getDiscount()
	{
		$this->CalculateTotals();
		return $this->_discount;
	}
	
	public function deleteAll() 
	{
		unset($this->_items);
		unset($this->_tax);
		unset($this->_coupon);
		unset($this->_discount);
		$this->persist();
	}
	
	public function isEmpty()
	{
		return $this->count() == 0;
	}
	
	/**
     * Does the given offset exist?
     *
     * @param string|int $key key
     * @return boolean offset exists?
     */
    public function offsetExists($key)
    {
        return isset($this->_items[$key]);
    }

    /**
     * Returns the given offset.
     *
     * @param string|int $key key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->_items[$key];
    }

    /**
     * Sets the value for the given offset.
     *
     * @param string|int $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        return $this->_items[$key] = $value;
    }

    /**
     * Unset the given element.
     *
     * @param string|int $key
     */
    public function offsetUnset($key)
    {
        unset($this->_items[$key]);
    }

    /**
     * Returns the current row.
     *
     * @return array|boolean current row 
     */
    public function current()
    {
        return current($this->_items);
    }

    /**
     * Returns the current key.
     *
     * @return array|boolean current key
     */
    public function key()
    {
        return key($this->_items);
    }

    /**
     * Moves the internal pointer to the next item and
     * returns the new current item or false.
     *
     * @return array|boolean next item
     */
    public function next()
    {
        return next($this->_items);
    }

    /**
     * Reset to the first item and return.
     *
     * @return array|boolean first item or false
     */
    public function rewind()
    {
        return reset($this->_items);
    }

    /**
     * Is the pointer set to a valid item?
     *
     * @return boolean valid item?
     */
    public function valid()
    {
        return current($this->_items) !== false;
    }

    /**
     * Seek to the given index.
     *
     * @param int $index seek index
     */
    public function seek($index)
    {
        $this->rewind();
        $position = 0;

        while ($position < $index && $this->valid()) {
            $this->next();
            $position++;
        }

        if (!$this->valid()) {
            throw new My_Model_Exception('Invalid seek position');
        }
    }

    /**
     * Count the cart items
     *
     * @return int row count
     */
    public function count()
    {
        return count($this->_items);
    }
}
