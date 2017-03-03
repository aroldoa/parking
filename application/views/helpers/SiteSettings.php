<?php
/**
* 
*/
class Zend_View_Helper_SiteSettings extends Zend_View_Helper_Abstract
{
	
	public function siteSettings()
	{
		$settings = new Model_SiteSettings();
			
		return $settings;
	}
}

?>