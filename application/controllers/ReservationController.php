<?php
/**
*
*/
class ReservationController extends My_Controller_Action
{
	private $dateErrors = array();
	public function init()
	{
		parent::init();
		$this->breadcrumbs->addStep('Dashboard', $this->getUrl(null, 'admin'));
		$this->breadcrumbs->addStep('Reservation Manager', $this->getUrl(null, 'reservation'));
	}

	public function indexAction()
	{
		# code...
	}

	public function listAction()
	{
		$request = $this->getRequest();
		$form = new Form_Reservation_Filters();
		$form->setAction($this->getUrl('list', 'reservation'));

		// get all the requested options or defaults
		$options = array(
			// 'order' => array('from DESC', 'type ASC'),
			'show' => $this->_getParam('show', 15),
			'page' => $this->_getParam('page', 1),
			'lot' => $this->_getParam('lot', null),
			'type' => $this->_getParam('type', null),
			'user' => $this->_getParam('user', null),
			'status' => $this->_getParam('status', null),
			'transaction' => $this->_getParam('transaction', null),
			'date' => $this->_getParam('date', null),
			'from' => $this->_getParam('from', null),
			'to' => $this->_getParam('to', null),
		);

		// check if we are clearing the filters
		if ($request->isPost()) {
			if ($form->isValid($request->getPost())) {
				if ($form->getElement('clear')->isChecked()) {
					$this->persistantFilters($options, true);
					$this->redirect('list', 'reservation');
				}
			}
		}

		// call function to persist the filters
		$optionsGet = $options;
		$options = $this->persistantFilters($options);
		// get rid of null valued keys
		foreach ($optionsGet as $key => $value) {
			if (null == $value) {
				unset($options[$key]);
			}
		}

		// set the layout if print is requested, and unset pagination params
		if ($this->_getParam('print', null)) {
			unset($options['page']);
			unset($options['show']);

			$this->_helper->layout()->setLayout('_admin-print');
		}

		// define the date fields to be converted from dd/dd/dd to timestam
		$urlTimes = array(
			'date', 'from', 'to'
		);

		// encode dates into timestamp for url
		foreach ($urlTimes as $key) {
			if (array_key_exists($key, $options)) {
				if (!is_numeric($options[$key])) {
					$options[$key] = (int) strtotime($options[$key]);
				}
			}
		}

		// redirect to current action with options in the parameters
		if ($request->isPost()) {
			$options['controller'] = 'reservation';
			$options['action'] = 'list';
			$this->_redirect($this->getCustomUrl($options, 'default'));
		}

		// get the reservations with the set options
		$reservations = $this->getModel('reservation')->getReservations($options);

		// if print version requested then alphabetize the results
		if ($this->_getParam('print', null)) {
			$users = array();
			foreach ($reservations as $key => $reservation) {
				$origReservations[$key] = $reservation;
				$users[$key] = $reservation->user->last_name;
			}

			asort($users);

			foreach ($users as $key => $value) {
				$alphaReservations[] = $origReservations[$key];
			}

			$reservations = $alphaReservations;
		}

		// show inventory remaining if proper options set
		if (isset($options['from']) && isset($options['to']) && isset($options['lot'])) {
			$spots = $this->getModel('spot')->getSpots($options);
			$remaining = array();
			foreach ($spots as $spot) {
				$options['lot'] = $spot->lot->id;
				$remaining[$spot->type] = $spot->inventoryRemaining($options);
			}
			$this->view->remaining = $remaining;
		}

		// convert timestamps back to dates for form display
		foreach ($urlTimes as $key) {
			if (array_key_exists($key, $options)) {
				$options[$key] = date('m/d/Y', $options[$key]);
			}
		}

		// populate the form
		$form->populate($options);

		// assign stuff to view
		$this->view->assign(array(
			'reservations' => $reservations,
			'form' => $form
		));

		$this->breadcrumbs->addStep('View Reservations', $this->getUrl('view', 'reservation'));
		$this->customLinks();
	}

	public function persistantFilters($options = array(), $reset = false)
	{
		if ($reset) {
			$this->getSession('ReservationFilters')->options = null;
			return $options;
		}

		$sessionFilters = $this->getSession('ReservationFilters')->options;

		if ($sessionFilters == null) {
			$sessionFilters = new stdClass();
		}

		foreach ($options as $key => $value) {

			if (null == $value) {
				if ($this->getRequest()->isPost()) {
					unset($sessionFilters->$key);
				}

				if (!isset($sessionFilters->$key)) {
					unset($options[$key]);
				} else {
					$options[$key] = $sessionFilters->$key;
				}
			} else {
				$sessionFilters->$key = $value;
			}

		}
		$this->getSession('ReservationFilters')->options = $sessionFilters;

		return $options;
	}

	public function viewAction()
	{
		$request = $this->getRequest();

		$reservation = $this->getModel('reservation')->getRowById($this->_getParam('id', null));

		if (null === $reservation) {
			$this->redirect('list', 'reservation');
		}

		$this->view->assign(array(
			'reservation' => $reservation,
		));

		$this->breadcrumbs->addStep('View Reservations', $this->getUrl('list', 'reservation'));
		$this->breadcrumbs->addStep('Reservation Details');
	}

	public function createAction()
	{
		$request = $this->getRequest();
		$form = new Form_Reservation();
		$form->setAction($this->getUrl('create', 'reservation'));
		$form->removeElement('delete');

		if (null !== $user = $this->_getParam('user', null)) {
			$form->addElement('hidden', 'user', array(
				'decorators' => array('Composite'),
				'value' => $user
			));
		}

		if ($request->isPost()) {
			$valid = $form->isValid($request->getPost());

			if ($form->getElement('cancel')->isChecked()) {
				$this->redirect('index', 'reservation');
			}

			if ($valid) {

				$from = strtotime($form->getValue('from'));
				$to = strtotime($form->getValue('to'));
				$type = $form->getValue('type');
				$qty = $form->getValue('quantity');

				if ($this->getModel('reservation')->checkAvailabilityByDate($from, $to, $type, $qty, null, true)) {
					$identity = null == $user ? $this->_identity : $this->getModel('User')->getUserById($user);

					if ($this->getModel('reservation')->save($form->getValues(), $identity)) {
						$this->messenger->addMessage('Reservation Created');
						$this->redirect('view', 'reservation');
					}
				} else {
					$form->getElement('quantity')->setErrors(array('Insufficient number of spots available'));
				}


			}
		}

		$this->view->form = $form;
		$this->customLinks();
		$this->breadcrumbs->addStep('Create Reservation', $this->getUrl('create', 'reservation'));
	}

	public function editAction()
	{
		$request = $this->getRequest();
		$reservationModel = new Model_Reservation();
		$form = new Form_Reservation();
		$form->setAction($this->getUrl('edit', 'reservation'));
		$form->getElement('submit')->setLabel('Update Reservation');

		$reservation = $reservationModel->getRowById($this->_getParam('id', null));

		if ($reservation === null) {
			$this->redirect('view', 'reservation');
		}

		// populate the form and format the date feilds
		$form->populate($reservation->toArray());
		$form->getElement('from')->setValue($reservation->fromDate);
		$form->getElement('to')->setValue($reservation->toDate);

		if ($request->isPost()) {
			if ($form->isValid($request->getPost())) {

				if ($form->getElement('cancel')->isChecked()) {
					$this->_redirect($this->getCustomUrl(array(
						'controller' => 'reservation',
						'action' => 'view',
						'id' => $reservation->id
					), 'default'));
				}

				if ($form->getElement('delete')->isChecked()) {
					$reservationModel->delete($reservation);
					$this->redirect('view', 'reservation');
				}

				$from = strtotime($form->getValue('from'));
				$to = strtotime($form->getValue('to'));
				$type = $form->getValue('type');
				$qty = $form->getValue('quantity');

				if ($reservationModel->checkAvailabilityByDate($from, $to, $type, $qty, $reservation->quantity)) {

					if ($reservationModel->save($form->getValues(), $reservation->user)) {
						$this->messenger->addMessage('Reservation Updated');
						$this->_redirect($this->getCustomUrl(array(
							'controller' => 'reservation',
							'action' => 'view',
							'id' => $reservation->id
						), 'default'));
					}
				} else {
					$form->getElement('quantity')->setErrors(array('Insufficient number of spots available'));
				}
				$form->getElement('to')->setErrors($this->dateErrors);
			}
		}

		$this->view->form = $form;
		$this->customLinks();

		$this->breadcrumbs->addStep('View Reservations', $this->getUrl('list', 'reservation'));
		$this->breadcrumbs->addStep('Reservation Details', $this->getCustomUrl(array(
			'controller' => 'reservation',
			'action' => 'view',
			'id' => $reservation->id
		), 'default'));
		$this->breadcrumbs->addStep('Edit Reservation');
	}

	public function setStatusAction()
	{
		$reservation = $this->getModel('reservation')->getRowById($this->_getParam('id', null));

		if (null === $reservation) {
			$this->redirect('list', 'reservation');
		}

		$reservation->status = 'complete';
		$reservation->save();

		$this->_redirect($this->getCustomUrl(array(
			'controller' => 'reservation',
			'action' => 'view',
			'id' => $reservation->id
		), 'default'));
	}

	public function customLinks()
	{
		$this->view->headScript()->appendFile('/js/jquery-ui-1.8.17.custom.min.js', 'text/javascript');
		$this->view->headScript()->appendFile('/js/forms.js', 'text/javascript');
		$this->view->headLink()->appendStylesheet('/css/jquery-ui-custom-theme/jquery-ui-1.8.23.custom.css');
	}

	public function checkDates($values)
	{
		$from = strtotime($values['from']);
		$to = strtotime($values['to']);

		if ($from >= $to) {
			$this->dateErrors[] = 'To date must be after from date';
			return false;
		}

		return true;
	}
}
