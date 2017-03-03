<?php
/**
* 
*/
class AdminController extends My_Controller_Action
{
	protected $_authService;
	protected $_identity;
	protected $_user;
	protected $recordPerPage = 1800;
	
	public function init()
	{
		parent::init();
		
		$this->_authService = new Service_Authentication();
		$this->_identity = $this->_authService->getIdentity();
		
		if ($this->_identity) {
			$this->_user = $this->getModel('user')->getUserById($this->_identity->id);
		}
		
		$this->breadcrumbs->addStep('Dashboard', $this->getUrl(null, 'admin'));
	}
	
	public function indexAction()
	{
		// Dashboard: Show links to main functions
		// lots, lots>spots, reservations, users
	}
	
	public function logoutAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
		
		return $this->redirect(null, 'admin');
	}
	
	public function loginAction()
	{
		if ($this->_authService->getIdentity()) {
			$this->_redirect($this->getUrl(null, 'admin'));
		}
		
		$request = $this->getRequest();
		$loginForm = new Form_User_Login();
		$loginForm->setAction($this->getUrl('login', 'admin'));
		
		if ($request->isPost()) {
			if ($loginForm->isValid($request->getPost())) {

				if ($this->_authService->authenticate($loginForm->getValues())) {
					
					// need to reset the auth identity
					$user = $this->getModel('user')->getUserById($this->_authService->getIdentity()->id);
					$this->_authService->write($user);
					
					$this->_redirect($this->getUrl($request->getActionName(), $request->getControllerName()));
					return;
				} else {
					$loginForm->setDescription('Login failed, please try again');
				}
			}
		}
		
		$this->breadcrumbs->addStep('Login', null);
		$this->view->form = $loginForm;
	}
	
	public function usersAction()
	{
		$request = $this->getRequest();
		$form = new Form_User_Filters();
		$form->setAction($this->getUrl('users', 'admin'));
		
		if ($request->isPost()) {
			if ($form->isValid($request->getPost())) {
				if ($form->getElement('clear')->isChecked()) {
					$this->redirect('users', 'admin');
				}
			}
		}
		
		$options = array(
			'first_name' => $this->_getParam('first_name', null),
			'last_name' => $this->_getParam('last_name', null),
			'role' => $this->_getParam('role', null),
			'page' => $this->_getParam('page', 1),
			'state' => $this->_getParam('state', null),
			'zip' => $this->_getParam('zip', null)
		);
		
		foreach ($options as $key => $value) {
			if (null == $value) {
				unset($options[$key]);
			}
		}
		
		if ($request->isPost()) {
			$options['controller'] = 'admin';
			$options['action'] = 'users';
			$this->_redirect($this->getCustomUrl($options, 'default'));
		}
		
		$form->populate($options);
		
		$this->view->assign(array(
			'users' => $this->getModel('user')->getUsers($options),
			'form' => $form
		));
		
		$this->breadcrumbs->addStep('User Manager', $this->getUrl('users', 'admin'));
	}
	
	public function userViewAction()
	{
		$user = $this->getModel('user')->getUserById($this->_getParam('id', null));
		
		if (null === $user) {
			$this->redirect('users', 'admin');
		}
		
		$this->view->user = $user;
		
		$this->breadcrumbs->addStep('User Manager', $this->getUrl('users', 'admin'));
		$this->breadcrumbs->addStep('User Details');
	}
	
	public function userEditAction()
	{
		$request = $this->getRequest();
		$form = new Form_User_Edit();
		$user = $this->getModel('user')->getUserById($this->_getParam('id', null));
		
		if (null !== $user) {
			$form->populate($user->toArray());
		}
		
		if ($request->isPost()) {
			
			if ($form->isValid($request->getPost())) {
				
				if ($form->getElement('cancel')->isChecked()) {
					$this->_redirect($this->getCustomUrl(array(
						'controller' => 'admin',
						'action' => 'user-view',
						'id' => $user->id
					)));
				}
				
				if ($form->getElement('delete')->isChecked()) {
					$this->getModel('user')->deleteUser($user);
					$this->redirect('users', 'admin');
				}
				
				$data = $form->getValues();
				
				if (strlen($data['password']) == 0) {
					unset($data['password']);
				}
				
				if ($id = $this->getModel('user')->saveUser($data)) {
					
					if ($id == $this->_identity->id) {
						$user = $this->getModel('user')->getUserById($id);
						$this->_authService->write($user);
					}
					
					$this->messenger->addMessage('User Details Saved Successfully');
					
					$this->_redirect($this->getCustomUrl(array(
						'controller' => 'admin',
						'action' => 'user-view',
						'id' => $user->id
					)));
				}

			}
		}
		
		$this->view->form = $form;
		$this->breadcrumbs->addStep('User Manager', $this->getUrl('users', 'admin'));
		
		if ($user) {
			$this->breadcrumbs->addStep('User Details',$this->getCustomUrl(array(
				'controller' => 'admin',
				'action' => 'user-view',
				'id' => $user->id
			)));
			$this->breadcrumbs->addStep('Edit User');
		} else {
			$this->breadcrumbs->addStep('Create New User');
		}
		
	}
	
	public function exportAction()
	{
		$this->breadcrumbs->addStep('Export Data');
	}
	
	public function exportDataAction()
	{
		$data = $this->_getParam('data', null);
		switch ($data) {
			case 'users':
				$type = "users";
				$records = $this->getModel('user')->getUsers();
			break;
			case 'reservations':
				$type = "reservations";
				$records = $this->getModel('reservation')->getReservations();
			break;
			case 'transactions':
				$type = "transactions";
				$records = $this->getModel('transaction')->getTransactions();
			break;
		}
		// assign stuff to view
		$this->view->assign(array(
			'totalRecords' => count($records),
			'dataType' => $type,
			'recordPerPage' => $this->recordPerPage
		));
		$this->breadcrumbs->addStep('Export Data');
	}
	
	public function getCsvAction()
	{
		set_time_limit(36000000);
        ini_set('memory_limit', '-1');
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest();
		$data = $this->_getParam('data', null);
		$page = $this->_getParam('page');
		
		switch ($data) {
			case 'users':				
				header("Content-Disposition: attachment; filename=users.csv");
				header("Content-Type: application/csv");
				$users = $this->getModel('user')->getUserData($page, $this->recordPerPage);					
				echo "Name,Email,Phone,Address,City,State,Zip,Country,Created On,Last Login,Role,# Transactions,# Reservations\n";
				foreach ($users as $user) {
					echo $user->fullname.",".$user->email.",".$user->phone.",".$user->address.",".$user->city.",".$user->state.",".$user->zip.",".$user->country.",".$user->created.",".$user->last_login.",".$user->role.",".count($user->transactions).",".count($user->reservations)."\n";
				}					
			break;
			case 'reservations':
				header("Content-Disposition: attachment; filename=reservations.csv");
				header("Content-Type: application/csv");
				$reservations = $this->getModel('reservation')->getReservationData($page, $this->recordPerPage);
				echo "Customer Name,Transaction Id,Amount,Lot,Type,Spot Quantity,From,To,Created On,Cruise Ship\n";
				foreach ($reservations as $reservation) {
					echo $reservation->user->fullname.",".$reservation->transaction->transaction_id.",".$reservation->transaction->amount.",".$reservation->lot->name.",".$reservation->type.",".$reservation->quantity.",".$reservation->fromDate.",".$reservation->toDate.",".$reservation->created.",".$reservation->cruise->ship->name."\n";
				}
			break;
			case 'transactions':
				header("Content-Disposition: attachment; filename=transactions.csv");
				header("Content-Type: application/csv");
				$transactions = $this->getModel('transaction')->getTransactionData($page, $this->recordPerPage);			
				echo "Transaction Id,Date,Customer Name,Amount,Coupon,# Reservations\n";			
				foreach ($transactions as $transaction) {
					echo $transaction->transaction_id.",".$transaction->ts_created.",".$transaction->user->fullname.",".'$' . $transaction->amount.",".$transaction->coupon_code.",".count($transaction->reservations)."\n";
				}
			break;
			default:
				# code...
			break;
		}
		exit;
	}
	
	public function settingsAction()
	{
		$request = $this->getRequest();
		$form = new Form_Site_Settings();
		
		$settings = $this->getModel('siteSettings')->getSettings();
		
		// var_dump($settings);
		$form->populate($settings);
		
		if ($request->isPost()) {
			if ($form->isValid($request->getPost())) {
				if ($form->getElement('cancel')->isChecked()) {
					$this->redirect('index', 'admin');
				}
				
				if ($this->getModel('siteSettings')->saveSettings($form->getValues())) {
					$this->messenger->addMessage('Site Settings Successfully Updated');
					$this->redirect('index', 'admin');
				}
			}
		}
		
		$this->view->assign(array(
			'form' => $form
		));
		$this->breadcrumbs->addStep('Site Settings');
	}
	
	public function couponsAction()
	{
		$request = $this->getRequest();
		
		$options = array(
			'page' => $this->_getParam('page', 1)
		);
		
		$coupons = $this->getModel('coupon')->getCoupons($options);
		
		$this->view->assign(array(
			'coupons' => $coupons
		));
		
		$this->breadcrumbs->addStep('Site Coupons');
	}
	
	public function couponEditAction()
	{
		$request = $this->getRequest();
		$form = new Form_Coupon();
		
		$coupon = $this->getModel('coupon')->getRowById($this->_getParam('id', null));
		
		if (null !== $coupon) {
			// $form->getDisplayGroup('actions')->addElement($form->addDeleteButton());
			
			$form->populate($coupon->toArray());
			$form->getElement('expiration')->setValue($coupon->expirationDate);
		} // else {
		// 			$form->removeElement('delete');
		// 		}
		
		if ($request->isPost()) {
			$form->populate($request->getPost());
			
			if ($form->getElement('cancel')->isChecked()) {
				$this->redirect('coupons', 'admin');
			}
			
			if ($form->getElement('delete') && $form->getElement('delete')->isChecked()) {
				$this->getModel('coupon')->deleteCoupon($coupon);
				
				$this->redirect('coupons', 'admin');
			}
			
			if ($form->isValid($request->getPost())) {
				
				$id = $this->getModel('coupon')->saveCoupon($form->getValues());
				
				$this->redirect('coupons', 'admin');
			}
		}
		
		$this->view->form = $form;
		$this->customLinks();
		$this->breadcrumbs->addStep('Site Coupons', $this->getUrl('coupons', 'admin'));
		$this->breadcrumbs->addStep('Coupon Editor');
	}
	
	public function customLinks()
	{
		$this->view->headScript()->appendFile('/js/jquery-ui-1.8.17.custom.min.js', 'text/javascript');
		$this->view->headScript()->appendFile('/js/forms.js', 'text/javascript');
		$this->view->headLink()->appendStylesheet('/css/jquery-ui-custom-theme/jquery-ui-1.8.23.custom.css');
	}
}
