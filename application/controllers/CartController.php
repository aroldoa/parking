<?php
/**
* 
*/ /*
class CartController extends My_Controller_Action
{
	protected $_cartModel;
	
	public function init()
	{
		parent::init();
		$this->_helper->layout()->setLayout('lighthouse');
		$this->view->headLink()->appendStylesheet('/css/sitestyles.css');
		// $this->_checkoutSession = new Zend_Session_Namespace(__CLASS__);
	}
	
	public function indexAction()
	{
		return $this->redirect('view', 'cart');
	}
	
	public function addAction()
	{
		$request = $this->getRequest();
		
		$id = $this->_getParam('id', null);
		
		if (null !== $id) {
			$reservation = $this->getSession('SearchResults')->results[$id];
			
			// check the availability of spots
			if ($this->getModel('Reservation')->checkAvailabilityByCruise($reservation->cruise, $reservation->spot->type, $reservation->quantity)) {
				
				$this->getModel('cart')->addItem($reservation, $reservation->quantity);
			}
			
		}
		
		$this->redirect(null, 'index');
	}
	
	public function updateAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$request = $this->getRequest();
		
		// look for special button submissions
		if ($request->getPost('checkout')) {
			
			$this->redirect('checkout', 'cart');
			
		} else if ($request->getPost('continue')) {
			
			$this->redirect(null, 'index');
			
		} else if ($request->getPost('empty')) {
			
			foreach ($this->_getParam('quantity') as $id => $qty) {
				$item = $this->getModel('cart')->offsetGet($id);

				if (null !== $item) {
					$this->getModel('cart')->removeItem($item);
				}
			}
			
		} else if ($request->getPost('update')) {
			
			foreach ($this->_getParam('quantity') as $id => $qty) {
				$item = $this->getModel('cart')->offsetGet($id);
				
				if (null !== $item) {
					
					// check the availability of spots
					if ($this->getModel('Reservation')->checkAvailabilityByCruise($item->cruise, $item->spot->type, $qty, $item->qty)) {
						
						$this->getModel('cart')->addItem($item, $qty);
					}

				}
			}
			
		}
		
		$this->redirect('view', 'cart');
	}
	
	public function viewAction()
	{
		$this->view->assign(array(
			'cartModel' => $this->getModel('cart')
		));
	}
	
	public function checkoutAction()
	{
		// redirect if cart is empty
		if ($this->getModel('cart')->isEmpty()) {
			$this->redirect('view', 'cart');
		}
		
		// force https (SSL) if on production and not active
		if (APPLICATION_ENV == 'production') {
			if (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on') {
				header('Location: https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
				exit;
			}
		}
		
		// get the request and the form
		$request = $this->getRequest();
		$form = $this->getModel('cart')->getForm('cartAuthorizenet_Checkout');
		
		// regular old submission/validation story
		if ($request->isPost()) {
			if ($form->isValid($request->getPost())) {
				// grab the cleaned values from the form
				$formValues = $form->getValues();
				
				// load or create user from form info
				$user = $this->getModel('user')->getUserByEmail($formValues['email']);
				// create user if not already in database
				if (null === $user) {
					// if user is new, save and load
					$id = $this->getModel('user')->saveUser($formValues);
					$user = $this->getModel('user')->getUserById($id);
				}
				
				// check that all cart items are still still available
				$taken = array();
				foreach ($this->getModel('cart') as $item) {
					// check the availability of spots
					if (!$this->getModel('Reservation')->checkAvailabilityByCruise($item->cruise, $item->spot->type, $item->quantity)) {
						$taken[] = $item;
					}
				}
				
				// if all items in cart are still available, continue process
				if (count($taken) == 0) {
					// set up our transaction service class
					$transactionService = new Service_AuthorizeNet($this->getModel('siteSettings')->getSettings());
					$transactionService->setCartValues($this->getModel('cart'));
					$transactionService->setBillingValues($formValues);

					// try to process the payment
					if ($transactionService->processPayment()) { // success
						// get the response, initialize database and save
						$response = $transactionService->getResponse();
						$transId = $response->transaction_id;
						
						// save the transaction to db table
						if (!$this->getModel('Transaction')->saveTransaction($user, $response)) {
							throw new Exception('Error Saving Transaction after payment processed');
						}
						
						// load the transaction db table row
						$transaction = $this->getModel('transaction')->getTransaction($transId);

						// save each cart item (spot) to the reservations table
						foreach ($this->getModel('cart') as $item) {
							if (!$this->getModel('reservation')->saveReservation($item, $user, $transaction)) {
								throw new Exception('Error Saving Reservation after payment processed for txn id: '. $transacton->transaction_id);
							}
						}

						// save transaction_id to session for recall in "thankyou" action then empty cart
						$this->getSession(__CLASS__)->transactionId = $transId;
						$this->getModel('cart')->deleteAll();
						
						// lets send them an email, will defer to a method
						$this->sendConfirmation($user, $transaction);
						
						// redirect to thank you page as we are all done here
						$this->_redirect($this->getUrl('thankyou', 'cart'));
						
					} else {
						// payment processing failed, lets display error message
						$reason = $transactionService->getResponse()->response_reason_text;
						$this->view->error = $reason;
					}
				} else {
					// one of the cart items (spots) became un-available in the time
					$this->view->error = 'One of your reservation types (' . $taken[0]->lot->name . ' ' . $taken[0]->spot->type . ' Parking) has become unavailable since adding to your cart, Check maybe a lower quantity or another spot type.';
				}
				
			}
		}
		
		$this->view->assign(array(
			'cartModel' => $this->getModel('cart'),
			'form' => $form
		));
	}
	
	public function thankyouAction()
	{
		$transactionId = $this->getSession(__CLASS__)->transactionId;
		$transaction = $this->getModel('transaction')->getTransaction($transactionId);
		// var_dump($transaction);
		$this->view->transaction = $transaction;
	}
	
	public function sendConfirmation(Resource_User_Item $user, Resource_Transaction_Item $transaction)
	{
		$template = '_reservation-confirmation.phtml';
		$data = array(
			'user' => $user,
			'transaction' => $transaction,
		);
		
		$emailService = new Service_SendEmail();
		$emailService->process($data, $template);
	}
}
