<?php
/**
 *
 *
 *
 */
 abstract class My_Model_Abstract implements My_Model_Interface 
 {
 	/**
    * @var array Class methods
    */
    protected $_classMethods;

    protected $_resources = array();

    protected $_forms = array();

	protected $_cache;

	protected $_cacheOptions = array();

    public function __construct($options = null)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (is_array($options)) {
            $this->setOptions($options);
        }

        $this->init();
    }

    /**
     * Constructor extensions
     */
    public function init()
    {}

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

	public function getResource($name) 
	{
        if (!isset($this->_resources[$name])) {
            $class = join('_', array(
                    'Resource',
                    $this->_getInflected($name)
            ));
            $this->_resources[$name] = new $class();
        }
	    return $this->_resources[$name];
	}

    private function _getNamespace()
    {
        $ns = explode('_', get_class($this));
        return $ns[0];
    }

    private function _getInflected($name)
    {
        $inflector = new Zend_Filter_Inflector(':class');
        $inflector->setRules(array(
            ':class'  => array('Word_CamelCaseToUnderscore')
        ));
        return ucfirst($inflector->filter(array('class' => $name)));
    }

	public function getForm($name)
    {
        if (!isset($this->_forms[$name])) {
            $class = join('_', array(
                    // $this->_getNamespace(),
                    'Form',
                    $this->_getInflected($name)
            ));
            $this->_forms[$name] = new $class(
				array('model' => $this)
			);
        }
	    return $this->_forms[$name];
    }

	public function setCache(My_Model_Cache_Abstract $cache) 
	{
		$this->_cache = $cache;
	}

	public function setCacheOptions(array $options) 
	{
		$this->_cacheOptions = $options;
	}

	public function getCacheOptions() 
	{
		if (empty($this->_cacheOptions)) {
			$frontendOptions = array(
				'lifetime' => 120,
				'automatic_serialization' => true
			);

			$backendOptions = array(
				'cache_dir' => APPLICATION_PATH . '/../data/cache/db'
			);

			$this->_cacheOptions = array(
				'frontend' 			=> 'Class',
				'backend' 			=>'File',
				'frontendOptions'	=> $frontendOptions,
				'backendOptions'	=> $backendOptions
			);
		}
		return $this->_cacheOptions;
	}

	public function getCached($tagged = null) 
	{
		if (null === $this->_cache) {
			$this->_cache = new My_Model_Cache(
				$this,
				$this->getCacheOptions()
			);
		}
		$this->_cache->setTagged($tagged);
		return $this->_cache;
	}
 }