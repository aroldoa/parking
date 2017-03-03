<?php
/**
* THIS CONTROLLER NOT USED CURRENTLY IN THIS APPLICATION! 10-15-2012
*/
class AccountController extends My_Controller_Action
{
	protected $_authService;
	protected $_identity;
	protected $_userModel;
	protected $_user;
	
	public function init()
	{
		parent::init();
		$this->_authService = new Service_Authentication();
		$this->_identity = $this->_authService->getIdentity();
		$this->_userModel = new Model_User();
		
		if ($this->_identity) {
			$this->_user = $this->_userModel->getUserById($this->_identity->id);
		}
		
		
		if (isset($this->_user->role) && $this->_user->role === 'administrator')  {
				$this->breadcrumbs->addStep('Admin', $this->getUrl(null, 'admin'));
		}
		
		$this->breadcrumbs->addStep('Account', $this->getUrl(null, 'account'));
		// $this->view->headLink()->appendStylesheet('/css/auxStyles.css');
	}
	
	public function indexAction()
	{
		$this->view->user = $this->_user;
	}
	
	public function updateAction()
	{
		$request = $this->getRequest();
		$form = new Form_User_Edit();
		// $user = $this->_userModel->getUserById($this->_user->id);
		
		$form->removeElement('role');
		$form->populate($this->_user->toArray());
		
		if ($request->isPost()) {
			
			if ($form->isValid($request->getPost())) {
				
				$data = $form->getValues();
				
				if (strlen($data['password']) == 0) {
					unset($data['password']);
				}
				
				if ($id = $this->_userModel->saveUser($data)) {
					
					$user = $this->_userModel->getUserById($id);
					$this->_authService->write($user);
					$this->_redirect($this->getUrl(null, 'account'));
				}

			}
		}
		
		$this->breadcrumbs->addStep('Update Account');
		$this->view->form = $form;
	}
	
	public function loginAction()
	{
		if ($this->_authService->getIdentity()) {
			$this->_redirect($this->getUrl(null, 'account'));
		}
		
		$request = $this->getRequest();
		$loginForm = new Form_User_Login();
		$loginForm->setAction($this->getUrl('login', 'account'));
		
		if ($request->isPost()) {
			if ($loginForm->isValid($request->getPost())) {

				if ($this->_authService->authenticate($loginForm->getValues())) {
					
					// need to reset the auth identity
					$user = $this->_userModel->getUserById($this->_authService->getIdentity()->id);
					$this->_authService->write($user);
					
					$this->_redirect($this->getUrl($request->getActionName(), $request->getControllerName()));
					return;
				} else {
					$loginForm->setDescription('Login failed, please try again');
				}
			}
		}
		
		$this->breadcrumbs->addStep('Login', null);
		$this->view->loginForm = $loginForm;
	}
	
	public function logoutAction() 
	{
		$this->_authService->clear();
		$this->_redirect($this->geturl(null, 'index'));
	}
	
	public function registerAction()
	{
		// redirect registration attempts, by appointment only
		$this->redirect('login', 'account');
		
		if ($this->_authService->getIdentity()) {
			$this->_redirect($this->getUrl(null, 'account'));
		}
		
		$request = $this->getRequest();
		$form = new Form_User_Register();
		
		if ($request->isPost()) {
			
			if ($form->isValid($request->getPost())) {
				$data = $form->getValues();
				$data['password'] = $this->_userModel->createPassword();
				
				// save the user
				if ($id = $this->_userModel->saveUser($data)) {

					$mailService = new Service_SendEmail();
					
					if ($mailService->process($data, 'user-registration.phtml')) {
						$user = $this->_userModel->getUserById($id);
						$session = new Zend_Session_Namespace('Registration');
						$session->user = $user;
						$this->_redirect($this->getUrl('registrationcomplete', 'account'));
					}
				}
			}
			
			if (!$form->getElement('username')->isValid($request->getPost('username'))) {
				$username = $form->getElement('username');
				$form->clearErrorMessages();
				$username->addErrorMessage('Username already taken');
			}
		}
		
		$this->view->form = $form;
	}
	
	public function registrationcompleteAction()
	{
		$session = new Zend_Session_Namespace('Registration');
		$user = $session->user;
		
		if ($user == null) {
			$this->_redirect($this->getUrl(null, 'index'));
		}
		
		$this->view->user = $user;
	}
	
	public function resetpasswordAction()
	{
		$request = $this->getRequest();
		$form = new Form_User_ResetPassword();
		
		if ($request->isPost()) {
			
			if ($form->isValid($request->getPost())) {
				
				$email = $form->getValue('email');
				
				if ($user = $this->_userModel->getUserByEmail($email)) {
					
					$password = $this->_userModel->createPassword();
					$user->password = md5($password);
					
					if ($user->save()) {
						
						$data = $user->toArray();
						$data['password'] = $password;
						$mailService = new Service_SendEmail();
						
						if ($mailService->process($data, 'reset-password.phtml')) {
							
							$session = new Zend_Session_Namespace('PasswordReset');
							$session->user = $user->id;
							$this->_redirect($this->getUrl('resetcomplete', 'account'));
						}
					}
					
				} else {
					$form->getElement('email')->addError('No record with that email found');
				}
			}
		}
		
		$this->view->form = $form;
	}
	
	public function resetcompleteAction()
	{
		$session = new Zend_Session_Namespace('PasswordReset');
		$userId = $session->user;
		
		$user = $this->_userModel->getUserById($userId);
		
		if ($user == null) {
			$this->_redirect($this->getUrl(null, 'index'));
		}
		
		$this->view->user = $user;
	}
}
