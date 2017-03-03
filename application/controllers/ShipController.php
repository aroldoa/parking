<?php
/**
* 
*/
class ShipController extends My_Controller_Action
{
	
	public function init()
	{
		parent::init();
		$this->breadcrumbs->addStep('Dashboard', $this->getUrl(null, 'admin'));
		$this->breadcrumbs->addStep('Ship Manager', $this->getUrl(null, 'ship'));
	}
	
	public function indexAction()
	{
		$request = $this->getRequest();
		$shipModel = $this->getModel('ship');
		
		$this->view->ships = $shipModel->getShips();
		$this->breadcrumbs->addStep('Cruise Ships');
	}
	
	public function viewAction()
	{
		$request = $this->getRequest();
		$ship = $this->getModel('ship')->getRowById($this->_getParam('id', null));
		
		if ($ship === null) {
			$this->redirect('index', 'ship');
		}
		
		$form = new Form_Ship_Filter();
		$form->setAction($this->getUrl('view', 'ship'));
		$form->getElement('id')->setValue($ship->id);
		
		if ($request->isPost()) {
			if ($form->isValid($request->getPost())) {
				// clear the filters?
				if ($form->getElement('clear')->isChecked()) {
					$this->_redirect($this->getCustomUrl(
					array(
						'controller' => 'ship',
						'action' => 'view', 
						'id' => $ship->id
					), 'default'));
				}
			}
		}
		
		$options = array(
			'id' => $ship->id,
			'ship' => $ship->id,
			'page' => $this->_getParam('page', 1),
			'show' => $this->_getParam('show', null),
			'year' => $this->_getParam('year', null)
		);
		
		foreach ($options as $key => $value) {
			if (null == $value) {
				unset($options[$key]);
			}
		}
		
		if ($request->isPost()) {
			$options['controller'] = 'ship';
			$options['action'] = 'view';
			$this->_redirect($this->getCustomUrl($options, 'default'));
		}
		
		$cruises = $this->getModel('cruise')->getCruises($options);
		
		$form->populate($options);
		
		$this->view->assign(array(
			'ship' => $ship,
			'cruises' => $cruises,
			'form' => $form
		));
		
		$this->breadcrumbs->addStep('Cruise Ships', $this->getUrl('index', 'ship'));
		$this->breadcrumbs->addStep($ship->name);
	}
	
	public function shipEditAction()
	{
		$request = $this->getRequest();
		$shipModel = $this->getModel('ship');
		$form = new Form_Ship();
		$form->setAction($this->getUrl('ship-edit', 'ship'));
		
		$ship = $shipModel->getRowById($this->_getParam('id', null));
		
		if ($ship !== null) {
			$form->populate($ship->toArray());
		}
		
		if ($request->isPost()) {
			if ($form->isValid($request->getPost())) {
				
				if ($form->getElement('delete')->isChecked()) {
					if ($shipModel->delete($ship)) {
						$this->redirect('index', 'ship');
					}
				}
				
				if ($form->getElement('cancel')->isChecked()) {
					$this->_redirect($this->getCustomUrl(array('controller'=>'ship', 'action'=>'view','id'=>$ship->id),'default'));
				}
				
				if ($id = $shipModel->save($form->getValues())) {
					$this->_redirect($this->getCustomUrl(array('controller'=>'ship', 'action'=>'view','id'=>$ship->id),'default'));
				}
			}
		}
		
		$this->view->form = $form;
		$this->breadcrumbs->addStep('Cruise Ships', $this->getUrl('index', 'ship'));
		if ($ship) {
			$this->breadcrumbs->addStep('Edit ' . $ship->name);
		} else {
			$this->breadcrumbs->addStep('Create Ship');
		}
		
	}
	
	public function cruiseEditAction()
	{
		$request = $this->getRequest();
		$cruiseModel = $this->getModel('Cruise');
		$shipModel = $this->getModel('Ship');
		$form = new Form_Cruise();
		$this->customLinks();
		
		$cruise = $cruiseModel->getRowById($this->_getParam('id', null));
		$ship = $shipModel->getRowById($this->_getParam('ship', null));
		
		if ($cruise) { // if we are editing existing cruise
			$ship = $cruise->ship;
			$form->populate($cruise->toArray());
			$form->getElement('date')->setValue($cruise->sailDate);
		} else if ($ship) { // if we are creating a new cruise date for a ship
			$form->getElement('ship')->setValue($ship->id);
			$form->removeElement('delete');
		} else { // if something not right
			$this->redirect('ships', 'ship');
		}
		
		if ($request->isPost()) {
			if ($form->isValid($request->getPost())) {
				
				if ($form->getElement('cancel')->isChecked()) {
					$this->_redirect($this->getCustomUrl(array(
						'controller' => 'ship', 
						'action' => 'view',
						'id' => $ship->id
					)));
				}
				
				if ($cruise && $form->getElement('delete')->isChecked()) {
					if ($cruiseModel->delete($cruise)) {
						$this->_redirect($this->getCustomUrl(array(
							'controller' => 'ship', 
							'action' => 'view',
							'id' => $ship->id
						)));
					}
				}
				
				if ($id = $cruiseModel->save($form->getValues())) {
					$cruise = $cruiseModel->getRowById($id);
					$this->_redirect($this->getCustomUrl(array(
						'controller' => 'ship', 
						'action' => 'view',
						'id' => $cruise->ship->id
					)));
				}
			}
		}
		
		$this->view->form = $form;
		$this->breadcrumbs->addStep('Cruise Ships', $this->getUrl('index', 'ship'));
		if ($cruise) {
			$this->breadcrumbs->addStep($cruise->ship->name, $this->getCustomUrl(array(
				'controller' => 'ship', 
				'action' => 'view', 
				'id' => $cruise->ship->id
			)));
			$this->breadcrumbs->addStep('Edit Cruise Details');
		} else {
			$this->breadcrumbs->addStep($ship->name, $this->getCustomUrl(array(
				'controller' => 'ship', 
				'action' => 'view', 
				'id' => $ship->id
			)));
			$this->breadcrumbs->addStep('Create Cruise Date');
		}
	}
	
	public function customLinks()
	{
		$this->view->headScript()->appendFile('/js/jquery-ui-1.8.17.custom.min.js', 'text/javascript');
		$this->view->headScript()->appendFile('/js/forms.js', 'text/javascript');
		$this->view->headLink()->appendStylesheet('/css/jquery-ui-custom-theme/jquery-ui-1.8.23.custom.css');
	}
}
