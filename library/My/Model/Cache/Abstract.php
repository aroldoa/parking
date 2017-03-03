<?php
/**
* 
*/
abstract class My_Model_Cache_Abstract
{
	
	protected $_classMethods;
	protected $_cache;
	protected $_frontend;
	protected $_backend;
	protected $_frontendOptions = array();
	protected $_backendOptions = array();
	protected $_model;
	protected $_tagged;
	
	public function __construct(My_Model_Abstract $model, $options, $tagged = null) 
	{
		$this->_model = $model;
		
		if ($options instanceof Zend_Config) {
			$options = $options->toArray();
		}
		
		if (is_array($options)) {
			$this->setOptions($options);
		}
		
		$this->setTagged($tagged);
	}
	
	public function setOptions(array $options) 
	{
		if (null === $this->_classMethods) {
			$this->_classMethods = get_class_methods($this);
		}
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (in_array($method, $this->_classMethods)) {
				$this->$method($value);
			}
		}
		return $this;
	}
	
	public function getCache() 
	{
		if (null === $this->_cache) {
			$this->_cache = Zend_Cache::factory(
				$this->_frontend,
				$this->_backend,
				$this->_frontendOptions,
				$this->_backendOptions
			);
		}
		return $this->_cache;
	}
	
	public function setFrontendOptions(array $frontend) 
	{
		$this->_frontendOptions = $frontend;
		$this->_frontendOptions['cached_entity'] = $this->_model;
	}
	
	public function setBackendOptions(array $backend) 
	{
		$this->_backendOptions = $backend;
	}
	
	public function setFrontend($frontend) 
	{
		if ('Class' != $frontend) {
			throw new My_Model_Exception('Frontend type must be Class');
		}
		$this->_frontend = $frontend;
	}
	
	public function setBackend($backend) 
	{
		$this->_backend = $backend;
	}
	
	public function setTagged($tagged = null) 
	{
		$this->_tagged = $tagged;
		
		if (null === $tagged) {
			$this->_tagged = 'default';
		}
	}
	
	public function __call($method, $params) 
	{
		if (!is_callable(array($this->_model, $method))) {
			throw new My_Model_Exception('Method ' . $method . ' does not
				exist in class ' . get_class($this->_model) );
		}
		
		$cache = $this->getCache();
		$cache->setTagsArray(array($this->_tagged));
		$callback = array($cache, $method);
		return call_user_func_array($callback, $params);
	}
}
