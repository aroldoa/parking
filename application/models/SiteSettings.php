<?php
/**
* 
*/
class Model_SiteSettings extends My_Model_Abstract
{
	public $settings = array();
	
	public function __construct($options = array())
	{
		return $this;
	}
	
	public function getSettings()
	{
		if (!$this->settings) {
			$this->setSettings();
		}
		return $this->settings;
	}
	
	public function setSettings()
	{
		$rows = $this->getResource('siteSettings')->loadSettings();
		// var_dump($rows);
		foreach ($rows as $row) {
			$key = $row->key;
			$value = $row->value;
			$this->settings[$key] = stripslashes($value);
		}
	}
	
	public function saveSettings($data = array())
	{
		if (empty($data)) {
			return null;
		}
		
		foreach ($data as $key => $value) {
			// $setting = $this->getSetting($key);
			$this->getResource('siteSettings')->saveSetting($key, $value);
		}
		return true;
	}
	
	public function toArray()
	{
		return (array) $this->settings;
	}
	
	public function __get($key)
	{
		if (!$this->settings) {
			$this->setSettings();
		}
		
		if (isset($this->settings[$key])) {
			return stripslashes($this->settings[$key]);
		}
		
		return null;
	}
	
	public function getSpotTypes($value='')
	{
		// $types = $this->getSetting('spotTypes');
		$types = $this->spotTypes;		
		$types = explode("\n", $types);

		$pattern = array(
			'/\//',
			'/ /'
		);
		$replace = array(
			'-',
			'_'
		);
		
		$results = array(null => 'Select One');
		foreach ($types as $type) {
			$key = preg_replace($pattern, $replace, trim($type));
			$results[$key] = $type;
		}
		
		return $results;
	}
}
