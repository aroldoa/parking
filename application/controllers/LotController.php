<?php
/**
* 
*/
class LotController extends My_Controller_Action
{
	
	public function init()
	{
		parent::init();
		$this->breadcrumbs->addStep('Dashboard', $this->getUrl(null, 'admin'));
		$this->breadcrumbs->addStep('Lot Manager', $this->getUrl(null, 'lot'));
	}
	
	public function indexAction()
	{
		// get Request on Model, add lots to view and set up breadcrumbs
		$request = $this->getRequest();
		$lotModel = $this->getModel('lot');
		
		$this->view->lots = $lotModel->getLots();
		$this->breadcrumbs->addStep('Lots', $this->getUrl('index', 'lot'));
	}
	
	public function viewAction()
	{
		// get the request object and models
		$request = $this->getRequest();
		$lotModel = $this->getModel('lot');
		
		// load our requested lot
		$lot = $lotModel->getRowById($this->_getParam('id', null));
		
		// redirect if load unsuccessfull
		if (!$lot) {
			$this->redirect('index', 'lot');
		}
		
		// add lot to view and set up breadcrumbs
		$this->view->lot = $lot;
		$this->breadcrumbs->addStep('Lots', $this->getUrl('index', 'lot'));
		$this->breadcrumbs->addStep($lot->name );
	}
	
	public function lotEditAction()
	{
		// get our request object
		$request = $this->getRequest();
		// instantiate models and forms, as well as set action of form
		$lotModel = $this->getModel('lot');
		$form = new Form_Lot();
		$form->setAction($this->getUrl('lot-edit', 'lot'));
		
		// load our lot to edit
		$lot = $lotModel->getRowById($this->_getParam('id', null));
		
		if ($lot) {
			// if we are editing the lot, then populate form and add to view
			$form->populate($lot->toArray());
			$this->view->lot = $lot;
		} else {
			// we are creating a new lot so remove the un-needed delete element
			$form->removeElement('delete');
		}
		
		// if our request is a post
		if ($request->isPost()) {
			// and our form valid
			if ($form->isValid($request->getPost())) {
				
				if ($form->getElement('cancel')->isChecked()) {
					$this->_redirect($this->getCustomUrl(array(
						'controller' => 'lot', 
						'action' => 'view',
						'id' => $lot->id
					), 'default'));
				}
				
				// if the lot already existed, adn delete element was clicked
				if ($lot && $form->getElement('delete')->isChecked()) {
					if ($lotModel->delete($lot)) {
						$this->messenger->addMessage('Lot Deleted');
					}
					$this->redirect('index', 'lot');
				}
				
				// if we were saving existing/new data
				if ($id = $lotModel->save($form->getValues())) {
					$this->messenger->addMessage('Lot Saved');
					$this->_redirect($this->getCustomUrl(array(
						'controller' => 'lot', 
						'action' => 'view', 
						'id' => $id
					), 'default'));
				}
			}
		} 
		
		$this->view->form = $form;
		// set up breadcrumbs based on the current conditions
		$this->breadcrumbs->addStep('Lots', $this->getUrl('index', 'lot'));
		if ($lot) {
			$this->breadcrumbs->addStep($lot->name, $this->getCustomUrl(array(
				'controller' => 'lot', 
				'action' => 'view',
				'id' => $lot->id
			)));
			$this->breadcrumbs->addStep('Edit ' . $lot->name);
		} else {
			$this->breadcrumbs->addStep('Create Lot');
		}
		
	}
	
	public function spotAction()
	{
		$spot = $this->getModel('spot')->getRowById($this->_getParam('id', null));
		
		if (null === $spot) {
			$this->redirect(null, 'lot');
		}
		
		$this->view->assign(array(
			'spot' => $spot
		));
		
		$this->breadcrumbs->addStep($spot->lot->name, $this->getCustomUrl(array(
			'controller' => 'lot', 
			'action' => 'view',
			'id' => $spot->lot->id
		)));
		$this->breadcrumbs->addStep("'$spot->type' Spot Info");
	}
	
	public function spotEditAction()
	{
		// get the request object
		$request = $this->getRequest();
		// initiate our models
		$spotModel = $this->getModel('spot');
		$lotModel = $this->getModel('lot');
		// initiate our form and set actions
		$form = new Form_Spot();
		$form->setAction($this->getUrl('spot-edit', 'lot'));
		
		// get the parent(lot) and the spot
		$lot = $lotModel->getRowById($this->_getParam('lot', null));
		$spot = $spotModel->getRowById($this->_getParam('id', null));
		
		// if we are editing the spot
		if ($spot) {
			$form->populate($spot->toArray());
			$this->view->spot = $spot;
			$lot = $spot->parent;
		} else {
			// if we are creating a spot, the lot should be set
			if (!$lot) {
				throw new Exception('here');
				$this->redirect('index', 'lot');
			}
			// remove delete element since this is a new creation
			$form->removeElement('delete');
			// set lot element value
			$form->getElement('lot')->setValue($lot->id);
		}
		
		// if request was a post
		if ($request->isPost()) {
			// validate the data
			if ($form->isValid($request->getPost())) {
				
				// if spot already existed and they clicked on the delete button
				if ($spot && $form->getElement('delete')->isChecked()) {
					// delete then re-direct
					if ($spotModel->delete($spot)) {
						$this->messenger->addMessage('Spot Deleted');
					}
					$this->_redirect($this->getCustomUrl(array(
						'controller' => 'lot', 
						'action' => 'view', 
						'id' => $spot->lot
					), 'default'));
				}
				
				// if saving new or update
				if (!$form->getElement('cancel')->isChecked() && $id = $spotModel->save($form->getValues())) {
					// refresh or load "$spot" based on save results and redirect to parent "lot"
					$spot = $spotModel->getRowById($id);
					$this->messenger->addMessage('Spot Saved');
					$this->_redirect($this->getCustomUrl(array(
						'controller' => 'lot', 
						'action' => 'view', 
						'id' => $spot->lot->id
					), 'default'));
					
				}
			} // end if valid
			
			if ($form->getElement('cancel')->isChecked()) {
				// throw new Exception('here');
				$this->_redirect($this->getCustomUrl(array(
					'controller' => 'lot', 
					'action' => 'view', 
					'id' => $lot->id
				), 'default'));
			}
		} 
		
		// put form into the view
		$this->view->form = $form;
		
		// Set up breadcrumbs
		$this->breadcrumbs->addStep('Lots', $this->getUrl('index', 'lot'));
		$this->breadcrumbs->addStep($lot->name, $this->getCustomUrl(array(
			'controller' => 'lot',
			'action' => 'view',
			'id' => $lot->id
		)));
		// if we are editing or creating new
		if ($spot) {
			$this->breadcrumbs->addStep('Edit Spot');
		} else {
			$this->breadcrumbs->addStep('Create New Spot Type');
		}
	}
	
	public function spotPriceAction()
	{
		$tier = $this->getModel('spot')->getPriceTier($this->_getParam('id', null));
		$form = new Form_Spot_Price();
		
		if (null !== $tier) {
			// otherwise populate and get parent spot from the tier
			$form->populate($tier->toArray());
			$spot = $this->getModel('spot')->getRowById($tier->spot);
			
			// see if we need to delete
			$process = $this->_getParam('process', null);
			if ($process && $process == 'delete') {
				$this->getModel('spot')->deleteTier($tier);
				$this->_redirect($this->getCustomUrl(array(
					'controller' => 'lot',
					'action' => 'spot', 
					'id' => $spot->id
				), 'default'));
			}
			
		} else {
			
			$spot = $this->getModel('spot')->getRowById($this->_getParam('spot', null));
			if (null === $spot) {
				$this->redirect(null, 'lot');
			}
			$form->getElement('spot')->setValue($spot->id);
		}
		
		// if form posted, validate and save
		if ($this->getRequest()->isPost()) {
			$form->populate($this->getRequest()->getPost());
			
			if ($form->getElement('cancel')->isChecked()) {
				$this->_redirect($this->getCustomUrl(array(
					'controller' => 'lot',
					'action' => 'spot', 
					'id' => $spot->id
				), 'default'));
			}
			
			if ($form->isValid($this->getRequest()->getPost())) {
				$this->getModel('spot')->savePriceTier($form->getValues());
				$this->_redirect($this->getCustomUrl(array(
					'controller' => 'lot',
					'action' => 'spot', 
					'id' => $spot->id
				), 'default'));
			}
		}
		
		$this->view->assign(array(
			'form' => $form,
			'spot' => $spot
		));
		$this->breadcrumbs->addStep('Lots', $this->getUrl('index', 'lot'));
		$this->breadcrumbs->addStep($spot->lot->name, $this->getCustomUrl(array(
			'controller' => 'lot',
			'action' => 'view',
			'id' => $spot->lot->id
		)));
		$this->breadcrumbs->addStep("'$spot->type' Price Tier");
	}
}
