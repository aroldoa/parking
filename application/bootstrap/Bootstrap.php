<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public $frontContoller;
	public $_log;
	public $_view;
	
	protected function _initNamespaces()
	{
		$this->_resourceLoader = new Zend_Application_Module_Autoloader(
			array(
				'namespace' 	=> '',
				'basePath' 		=> APPLICATION_PATH
			)
		);
		
		$this->_resourceLoader->addResourceTypes(array(
			'modelResource'	=> array(
				'path' 		=> 'models/resources',
				'namespace' => 'Resource',
			),
		));
	}

	protected function _initLog()
	{
		$this->bootstrap('frontController');
		$this->frontController = $this->getResource('frontController');
		$log = new Zend_Log();
		
		$writer = $this->getEnvironment() == 'production' ? 
			new Zend_Log_Writer_Stream(APPLICATION_PATH . '/../data/logs/application.log') :
			new Zend_Log_Writer_Firebug();
		
		$log->addWriter($writer);
		
		if ($this->getEnvironment() == 'production') {
			$filter = new Zend_Log_Filter_Priority(Zend_Log::CRIT);
			$log->addFilter($filter);
		}
		
		
		Zend_Registry::set('log', $log);
		return $this->_log = $log;
	}
	
	protected function _initConfig()
	{
		Zend_Registry::set('config', $this->getOptions());
	}
	
	protected function _initDbConnection()
	{
		$this->bootstrap('db');
		$db = $this->getResource('db');
		// $db->getConnection();
		Zend_Registry::set('db', $db);
	}
	
	protected function _initLocale()
	{
		$locale = new Zend_Locale('en_US');
		Zend_Registry::set('Zend_Locale', $locale);
	}
	
    protected function _initViewSettings()
    {
        $this->bootstrap('view');
		$this->_view = $this->getResource('view');
		
		$this->_view->setEncoding('UTF-8');
		$this->_view->doctype('HTML5');
		
		$this->_view
			 ->headMeta()
			 ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
		// $this->_view
		// 			 ->headMeta()
		// 			 ->appendHttpEquiv('Content-Language', 'en_US');
		$this->_view
			 ->headMeta()
			 ->appendName('description', 'Lighthouse Cruise Ship Parking Reservations');
		$this->_view
		     ->headMeta()
		     ->appendName('google-site-verification', '');
		$this->_view->headTitle('Lighthouse Parking Reservation Manager');
		$this->_view->headTitle()->setSeparator(' - ');
		
		// add our css and js files
		$cssFiles = array(
			'base', 
			'skeleton', 
			'layout', 
			'project'
		);
		
		$jsFiles = array(
			'jquery.min', 
			'hoverIntent',
			'general'
		);
		
		$this->addCssFiles($cssFiles);
		$this->addJsFiles($jsFiles);
		
		return $this->_view;
    }
    
    protected function _initRoutes() 
	{
		// $this->_logger->info('Bootstrap ' . __METHOD__);
		$this->bootstrap('frontController');
		
		$router = $this->frontController->getRouter();
		
		// Product View Route
		$route = new Zend_Controller_Router_Route(
			'/profile/:user',
			array(
				// 'module' 		=> 'default',
				'controller'	=> 'user',
				'action'		=> 'index',
				'user' 			=> null
			),
			array(
				'user' 			=> '[a-zA-Z0-9]+',
			)
		);
		
        // $router->addRoute('userProfile', $route);
	}
	
	protected function _initDbProfiler()
	{

		$this->bootstrap('db');

		if ('production' !== $this->getEnvironment() && $this->hasResource('db')) {
			$profiler = new Zend_Db_Profiler_Firebug('All Db Queries');

			$profiler->setEnabled(true);

			$this->getPluginResource('db')->getDbAdapter()->setProfiler($profiler);
		}
		
	}
	
	protected function _initDbCaches()
	{

		$this->bootstrap('db');

		if ('development' != $this->getEnvironment()) {
			$frontendOptions = array(
				'automatic_serialization' => true
			);

			$backendOptions = array(
				'cache_dir' => APPLICATION_PATH . '/../data/cache'
			);

			$cache = Zend_Cache::factory(
				'Core',
				'File',
				$frontendOptions,
				$backendOptions
			);

			Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
		}
	}
	
	public function addCssFiles($files = array())
	{
		if (count($files) > 0) {
			foreach ($files as $file) {
				$file = $file . '.css';
				$this->_view->headLink()->appendStylesheet('/css/' . $file);
			}
		}
	}
	
	public function addJsFiles($files = array())
	{
		if (count($files) > 0) {
			foreach ($files as $file) {
				$file = $file . '.js';
				$this->_view->headScript()->appendFile(
				    '/js/' . $file,
				    'text/javascript'
				);
			}
		}
	}
}