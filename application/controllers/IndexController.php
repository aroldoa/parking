<?php

class IndexController extends My_Controller_Action
{
	protected $_form;
	protected $_models = array();
	protected $_ship;
	protected $_message;

    public function init()
    {
			parent::init();
			$this->_helper->layout()->setLayout('lighthouse');
			$this->view->headLink()->appendStylesheet('/css/sitestyles.css');

			// Zend_Session::namespaceUnset('Model_Cart');
			// Zend_Session::namespaceUnset('SearchResults');
    }

    public function indexAction()
    {
		// force https (SSL) if on production and not active
		if (APPLICATION_ENV == 'production') {
			if (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on') {
				header('Location: https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
				exit;
			}
		}

		// $this->getModel('cart')->emptyCart();
		$request = $this->getRequest();
		// $form = new Form_Search();
		$form = $this->getModel('cart')->getForm('search');
		$form->setAction('/');
		$this->customLinks();

		$checkoutForm = $this->getModel('cart')->getForm('cartAuthorizenet_Checkout');

		if ($this->getModel('cart')->isEmpty()) {
			$checkoutForm->removeElement('submit');
			$checkoutForm->removeElement('confirm');
		}

		if ($request->isPost()) {
			// set flags
			$cart = false;
			$process = false;

			$ship = $this->getModel('ship')->getRowById($this->_getParam('ship', null));

			if (null !== $ship) {
				$this->_ship = $ship;
				$form->setCruiseDateOptions($ship);
			}

			$cruise = $this->getModel('cruise')->getRowById($this->_getParam('cruise', null));

			if (null !== $cruise && $ship) {
				$form->setTypeOptions();
			}

			// spot type selection
			$type = $this->_getParam('type', null);

			if ($type != '' && $cruise && $ship) {
				$form->getElement('type')->setValue($type);
				$options = array(
					'from' => (int)$cruise->date,
					'to' => (int)$cruise->return,
					'type' => $type,
				);

				if ($ship->lot && $ship->lot > 0) {
					$options['lot'] = $ship->lot;
				}

				$spotModel = $this->getModel('Spot');
				$spots = $spotModel->getSpots($options);

				$remaining = 0;

				foreach ($spots as $spot) {
					$inStock = $spot->inventoryRemaining($options);

					if (!$inStock > 0) {
						continue;
					}

					$remaining = $inStock;
				}

				$form->setQuantityOptions($remaining);
			}

			$quantity = $this->_getParam('quantity', null);

			if ($quantity > 0 && $type != '' && $cruise && $ship) {

				$this->view->searched = true;
				$results = $this->getModel('Reservation')->checkAvailabilityByCruise($cruise, $type, $quantity);

				if ($results) {
					$this->view->results = $results;
					$this->getSession('SearchResults')->results = $results;
				}
			}

			$form->populate($request->getPost());
			$checkoutForm->populate($request->getPost());

			if ($checkoutForm->getElement('submit')) {

				if ($checkoutForm->getElement('submit')->isChecked()) {

					if ($checkoutForm->isValid($request->getPost())) {

						// make sure conditions agreed to
						if (true == $checkoutForm->getValue('confirm')) {

							// grab the cleaned values from the form
							$formValues = $checkoutForm->getValues();
							// remove the confirmation field from transaction
							unset($formValues['confirm']);

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
								$formValues['exp_date'] = $formValues['exp_month'].'/'.date('y',strtotime($formValues['exp_year'].date('-m-d H:i:s')));
								unset($formValues['exp_month']);
								unset($formValues['exp_year']);

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
									if (!$this->getModel('Transaction')->saveTransaction(
										$user, $response, $this->getModel('cart')
									)) {
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
									if ($this->sendConfirmation($user, $transaction, '_reservation-confirmation.phtml')) {
										$this->sendConfirmation($user, $transaction, '_transaction-notice.phtml', true);
									}
									// $this->sendConfirmation($user, $transaction, '_reservation-confirmation.phtml');


									// redirect to thank you page as we are all done here
									$this->_redirect($this->getUrl('thankyou', 'index'));

								} else {

									// payment processing failed, lets display error message
									$reason = $transactionService->getResponse()->response_reason_text;
									$this->view->error = $reason;
								}
							} else {
								// one of the cart items (spots) became un-available in the time
								$reason = 'One of your reservation types (' . $taken[0]->lot->name . ' ' . $taken[0]->spot->type . ' Parking) has become unavailable since adding to your cart, Check maybe a lower quantity or another spot type.';
								$this->view->error = $reason;
							}
						}

					}
				}
			}

		}

		$this->view->assign(array(
			'searchForm' => $form,
			'checkoutForm' => $checkoutForm,
			'cartModel' => $this->getModel('cart'),
			'images' => $this->getIndexImages()
		));
		$this->customLinks();
		$this->breadcrumbs->addStep('Search For Cruise Parking');
    }

	public function viewAction()
	{
		$this->view->assign(array(
			'cartModel' => $this->getModel('cart')
		));
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

			$this->redirect(null, 'index');

		} else if ($request->getPost('continue')) {

			$this->redirect(null, 'index');

		} else if ($request->getPost('empty')) {

			$this->getModel('cart')->deleteAll();

		} else if ($request->getPost('update')) {

			foreach ($this->_getParam('quantity') as $id => $qty) {

				$item = $this->getModel('cart')->offsetGet($id);
				if (null !== $item) {

					// check the availability of spots
					if ($this->getModel('Reservation')->checkAvailabilityByCruise($item->cruise, $item->spot->type, $qty, $item->quantity)) {

						$this->getModel('cart')->addItem($item, $qty);
					}

				}
			}

		}
		else if ($request->getPost('applycoupon'))
		{
			$type_parking = array();
			foreach ($this->_getParam('quantity') as $id => $qty)
			{
				$item = $this->getModel('cart')->offsetGet($id);
				if (null !== $item)
				{
					switch ($item->spot->type) {
						case 'Un-Covered':
							if (! in_array('un-covered', $type_parking))
								$type_parking[] = 'un-covered';
							break;

						case 'Covered':
							if (! in_array('covered', $type_parking))
								$type_parking[] = 'covered';
							break;
					}
				}
			}

			$coupon = $this->getModel('coupon')->getCouponByCouponCode($this->_getParam('coupon', null));

			if (null !== $coupon) {
				$subtotal = $this->getModel('cart')->getSubTotal();

				// make sure coupon still valid
				if ($coupon->expiration > time() && ($coupon->type_parking === 'both' || in_array($coupon->type_parking, $type_parking))) {

					$this->getModel('cart')->setCoupon($coupon);

				}
				else{
					$this->_helper->flashMessenger->addMessage("Invalid coupon code");
				}
			}
			else{
				$this->_helper->flashMessenger->addMessage("Invalid coupon code");
			}
		}

		$this->redirect(null, 'index');
	}

	public function sendConfirmation(Resource_User_Item $user, Resource_Transaction_Item $transaction, $template = null, $toAdmin = false)
	{
		if (null === $template) {
			$template = '_reservation-confirmation.phtml';
		}

		$data = array(
			'user' => $user,
			'transaction' => $transaction,
			'siteSettings' => $this->getModel('siteSettings')
		);

		$emailService = new Service_SendEmail();

		if ($toAdmin) {
			$emailService->setToAdmin();
		}

		return $emailService->process($data, $template);
	}

	public function thankyouAction()
	{
		$transactionId = $this->getSession(__CLASS__)->transactionId;
		$transaction = $this->getModel('transaction')->getTransaction($transactionId);
		// var_dump($transaction);
		$this->view->transaction = $transaction;
	}

	public function calendarAction()
	{
		$this->_helper->layout->disableLayout();
		// Spot to develop the calendar and return via an ajax request.
		$request = $this->getRequest();
		$shipModel = $this->getModel('ship');

		$ship = $shipModel->getRowById($this->_getParam('ship', 1));

		$cruises = $ship->getCruises();

		$cruiseArray = array();
		foreach ($cruises as $cruise) {
			$date = date('m/d/y', $cruise->date);
			$cruiseArray[$date] = $cruise->toArray();
 		}

		// get cal params
		$year = $this->_getParam('year', date('Y'));
		$month = $this->_getParam('month', date('n'));

		$cYear = $year;
		$cMonth = $month;

		$prev_year = $cYear;
		$next_year = $cYear;
		$prev_month = $cMonth-1;
		$next_month = $cMonth+1;

		if ($prev_month == 0 ) {
		    $prev_month = 12;
		    $prev_year = $cYear - 1;
		}
		if ($next_month == 13 ) {
		    $next_month = 1;
		    $next_year = $cYear + 1;
		}

		$this->view->assign(array(
			'cYear' => $cYear,
			'cMonth' => $cMonth,
			'cruises' => $cruiseArray
		));

	}

	public function getIndexImages()
	{
		$root = $_SERVER['DOCUMENT_ROOT'];
		$imgFolder = '/images/homepage/';

		$dir = $root . $imgFolder;

		$imgs = array();
		// Open directory, and read its contents
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
					if (!is_dir($file)) {
						// get ship images if ship is set
						if ($this->_ship) {
							$name = preg_replace('/ /', '_', $this->_ship->name);
							if (preg_match("/$name/i", $file)) {
								$imgs[] = array(
									'filename' => $file,
									'url' => $imgFolder . $file
								);
							}
						} else {
							if (preg_match('/Default/i', $file)) {
								$imgs[] = array(
									'filename' => $file,
									'url' => $imgFolder . $file
								);
							}
						}

					}
		        }
		        closedir($dh);
		    }
		}

		if (count($imgs) == 0) {
			// get default (lot) images
			$imgs[] = array(
				'filename' => 'Default.png',
				'url' => $imgFolder . 'Default.png'
			);
		}

		return $imgs;
	}

	public function customLinks()
	{
		$this->view->headScript()->appendFile('/js/jquery-ui-1.8.17.custom.min.js', 'text/javascript');
		$this->view->headScript()->appendFile('/js/forms.js', 'text/javascript');
		// $this->view->headScript()->appendFile('/js/calendar.js', 'text/javascript');
		$this->view->headLink()->appendStylesheet('/css/jquery-ui-custom-theme/jquery-ui-1.8.23.custom.css');
	}

}

