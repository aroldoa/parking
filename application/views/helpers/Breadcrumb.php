<?php
/**
* 
*/
class Zend_View_Helper_Breadcrumb extends Zend_View_Helper_Abstract
{
	
	public function breadcrumb($trail = array(), $truncate = 40, $seperator = ' &raquo; ')
	{
		$links = array();
		$numSteps = count($trail);
		for ($i = 0; $i < $numSteps; $i++) {
			$step = $trail[$i];
			
			// truncate the title if req.
			$step['title'] = strlen($step['title']) > $truncate ? 
				substr($step['title'], 0, $truncate -2) . '...' : $step['title'];
		
		// build the link if it's set and isn't last step
		if (strlen($step['link']) > 0 && $i < $numSteps - 1) {
			$links[] = sprintf('<a href="%s" title="%s" rel="nofollow">%s</a>',
							   htmlSpecialChars($step['link']),
							   htmlSpecialChars($step['title']),
							   htmlSpecialChars($step['title']));
		}
		else {
			// either the link isn't set or it's the last step
			$links[] = htmlSpecialChars($step['title']);
		}
			
		}
		// join the links using the seperator
		return join($seperator, $links);
	}
	
	// public function getTrial()
	// 	{
	// 		# code...
	// 	}
}
