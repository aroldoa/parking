<?php
/**
* 
*/
class My_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	private $_defaultRole = 'guest';
	
	private $_authController = array(
		// 'module' 	 => 'default',
		'controller' => 'admin',
		'action'	 => 'login',
	);
		
	public function __construct()
	{
		$this->auth = Zend_Auth::getInstance();
		$this->acl = new Zend_Acl();
		
		// add the different user roles
        $this->acl->addRole(new Zend_Acl_Role($this->_defaultRole));
		$this->acl->addRole(new Zend_Acl_Role('customer'), $this->_defaultRole);
		$this->acl->addRole(new Zend_Acl_Role('employee'), 'customer');
		$this->acl->addRole(new Zend_Acl_Role('administrator'), 'employee');

		// add the resources we want to have control over
		// use Capitalized Module_Controller
		$this->acl->add(new Zend_Acl_Resource('admin'));
		// $this->acl->add(new Zend_Acl_Resource('account'));
		$this->acl->add(new Zend_Acl_Resource('reservation'));
		$this->acl->add(new Zend_Acl_Resource('ship'));
		$this->acl->add(new Zend_Acl_Resource('lot'));
		$this->acl->add(new Zend_Acl_Resource('inventory'));
		
		
		// Set access restriction rules
		$this->acl->allow();
		// $this->acl->deny(null, 'account');
		$this->acl->deny(null, 'admin');
		$this->acl->deny(null, 'reservation');
		$this->acl->deny(null, 'lot');
		$this->acl->deny(null, 'ship');
		$this->acl->deny(null, 'inventory');
		
		// $this->acl->allow(
		// 			$this->_defaultRole, 
		// 			array('account'), 
		// 			array('login', 'logout', 'register', 'registrationcomplete', 'resetpassword', 'resetcomplete')
		// 		);
		
		// allow non-authenticated users to authenticate
		$this->acl->allow(
			$this->_defaultRole, 
			array('admin'), 
			array('login', 'logout')
		);
		
		// Define resources/privileges for employees
		// All of inventory Controller
		$this->acl->allow(
			'employee', 
			array('inventory')
		);
		
		// Employees can access but not edit reservations
		$this->acl->allow(
			'employee', 
			array('reservation'),
			array('index', 'list', 'create', 'view', 'set-status')
		);
		
		// Emplyees can access dashboard and view users but not edit
		$this->acl->allow(
			'employee', 
			array('admin'),
			array('index','users', 'user-view')
		);
		
		// Admin gets it all
		$this->acl->allow(
			'administrator',
			array('admin', 'inventory', 'lot', 'reservation', 'ship')
		);

	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {	
		// get users identity from auth or set default
        if ($this->auth->hasIdentity())
            $role = $this->auth->getIdentity()->role;
        else
            $role = $this->_defaultRole;

        if (!$this->acl->hasRole($role))
            $role = $this->_defaultRole;

        // the ACL resource is the requested controller name
        $resource = strtolower($request->controller);

        // the ACL privilege is the requested action name
        $privilege = strtolower($request->action);

        // if we haven't explicitly added the resource, check the default permissions
        if (!$this->acl->has($resource))
            $resource = null;

		// for debugging
		// echo 'is allowed: ' . $this->acl->isAllowed($role, $resource, $privilege) ? 'Yes' : 'No';
		
        // if access denied - reroute the request to the default action handler
        if (!$this->acl->isAllowed($role, $resource, $privilege)) {
		 	// $request->setModuleName($this->_authController['module']);
            $request->setControllerName($this->_authController['controller']);
            $request->setActionName($this->_authController['action']);
        }
    }

}
