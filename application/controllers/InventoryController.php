<?php
/**
* 
*/
class InventoryController extends My_Controller_Action
{
	
	public function init()
	{
		parent::init();
		$this->breadcrumbs->addStep('Dashboard', $this->getUrl(null, 'admin'));
		$this->breadcrumbs->addStep('Inventory', $this->getUrl(null, 'inventory'));
	}
	
	/**
	 * Show The Available lots
	 */
	public function indexAction()
	{
		$lots = $this->getModel('lot')->getLots();
		
		// if we have only one let, then we will just show them that data
		if (!(count($lots) > 1)) {
			$options = array(
				'controller' => 'inventory',
				'action' => 'view',
				'lot' => $lots[0]->id
			);
			$this->_redirect($this->getCustomUrl($options, 'default'));
		}
		
		$this->view->assign(array(
			'lots' => $lots
		));
	}
	
	public function viewAction()
	{
		$request = $this->getRequest();
		
		$year = $this->_getParam('year', date('Y'));
		$month = $this->_getParam('month', date('n'));
		$lotId = $this->_getParam('lot', null);
		
		$lot = $this->getModel('lot')->getRowById($lotId);
		
		if (null === $lot) {
			$this->redirect(null, 'inventory');
		}
		
		$this->view->assign(array(
			'lot' => $lot,
			'month' => $month,
			'year' => $year
		));
		
		$this->breadcrumbs->addStep('"' . $lot->name . '"' . 'Inventory');
	}
}
