<?php

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        		if (APPLICATION_ENV !== 'development') {
					$this->_forward('error404');
					return;
				}
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
            default: // FALL THROUGH
                // application error
                $this->getResponse()->clearBody();
                
				$this->getLog()->crit(
					$errors->exception->getMessage() . "\n" . 
					$errors->exception->getTraceAsString());
					
				$this->view->exception = $errors->exception;
		        $this->view->request   = $errors->request;
        }
    }
	
	public function error404Action() 
	{
		$request 	= $this->getRequest();
		$error 		= $request->getParam('error_handler');
		$uri 		= $request->getRequestUri();
		
		$this->getLog()->info('404 error occurred: ' . $uri);
		$this->getResponse()->setHttpResponseCode(404);
		
		$this->view->requestedAddress = $uri;
	}
	
	public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }
}