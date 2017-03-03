<?php

/**
* 
*/
class My_Controller_Action extends Zend_Controller_Action
{
	public $db;
	public $breadcrumbs;
	public $messenger;
	public $ajaxContext;
	protected $_identity = null;
	protected $_session = array();
	protected $_models = array();
	
	public function init()
	{
		parent::init();
		
		$auth = Zend_Auth::getInstance();
		
		if ($auth->hasIdentity()) {
            $this->view->identity = $this->_identity = $auth->getIdentity();
        }
		

		$home = $this->getUrl(null, 'index');
		
		$this->breadcrumbs = new My_Breadcrumbs();
		// $this->breadcrumbs->addStep('Home', $home);
		$this->messenger = $this->_helper->flashMessenger;
		$this->ajaxContext = $this->_helper->getHelper('AjaxContext');
	}
	
	public function getUrl($action = null, $controller = null)
	{
		$url = rtrim($this->getRequest()->getBaseUrl(), '/') . '/';
		$url .= $this->_helper->url->simple($action, $controller);
		
		return '/' . ltrim($url, '/');
	}
	
	public function getCustomUrl($options, $route = null)
    {
        return $this->_helper->url->url($options, $route);
    }
	
	public function redirect($action = null, $controller = null)
	{
		return $this->_redirect($this->getUrl($action, $controller));
	}
	
	public function preDispatch()
	{
		// location to add for preDispatch loop
		// Add auth to view here? already done in Bootstrap.php
	}
	
	public function postDispatch()
	{
		$this->view->breadcrumbs = $this->breadcrumbs;
		$this->view->title = $this->breadcrumbs->getTitle();
		$this->view->messages = $this->messenger->getMessages();
		$this->view->isXmlHttpRequest = 
			$this->getRequest()->isXmlHttpRequest();
	}
	
	public function sendJson($data)
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$this->getResponse()->setHeader('content-type', 'application/json');
		echo Zend_Json::encode($data);
	}
	
	public function getSession($name)
	{
		if (!isset($this->_session[$name])) {
			$this->_session[$name] = new Zend_Session_Namespace($name);
		}
		
		return $this->_session[$name];
	}
	
	public function getModel($name = null)
	{
		if (null === $name) {
			return null;
		}
		
		$name = ucfirst($name);
		
		if (!in_array($name, $this->_models)) {
			$modelName = 'Model_' . $name;
			$this->_models[$name] = new $modelName();
		}
		
		return $this->_models[$name];
	}
}