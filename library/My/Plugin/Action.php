<?php
/**
* 
*/
class My_Plugin_Action extends Zend_Controller_Plugin_Abstract
{
	protected $_stack;
	
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
		$stack = $this->getStack();
		
		// Recent Blogs
		$categoryRequest = new Zend_Controller_Request_Simple();
		$categoryRequest->setControllerName('blogs')
						->setActionName('recent')
						->setModuleName('default')
						->setParam(
							'responseSegment',
							'recentBlogs'
						);
		
		// PUSH REQUEST INTO THE STACK
		$stack->pushStack($categoryRequest);
		
		
		// Products Menu
		$categoryRequest = new Zend_Controller_Request_Simple();
		$categoryRequest->setControllerName('products')
						->setActionName('listing')
						->setModuleName('default')
						->setParam(
							'responseSegment',
							'productsListing'
						);
		
		// PUSH REQUEST INTO THE STACK
		// $stack->pushStack($categoryRequest);
		
		// Top Level Categories
		$categoryRequest = new Zend_Controller_Request_Simple();
		$categoryRequest->setControllerName('category')
						->setActionName('index')
						->setModuleName('default')
						->setParam('responseSegment',
								   'productCategories'
						);
		// Push request onto stack
		$stack->pushStack($categoryRequest);
	}
	
	public function getStack() 
	{
		if (null === $this->_stack) {
			$front = Zend_Controller_Front::getInstance();
			if (!$front->hasPlugin('Zend_Controller_Plugin_ActionStack')) {
				$stack = new Zend_Controller_Plugin_ActionStack();
				$front->registerPlugin($stack);
			} else {
				$stack = $front->getPlugin('ActionStack');
			}
			$this->_stack = $stack;
		}
		
		return $this->_stack;
	}
}
