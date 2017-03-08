<?php
/**
*
*/
class Zend_View_Helper_Cart extends Zend_View_Helper_Abstract
{
	public $cartModel;

	public function cart()
	{
		$this->cartModel = new Model_Cart();
		return $this;
	}

	public function addForm($item)
	{
		$form = new Form_Cart_Add();
		$form->setAction($this->view->url(array(
			'controller' => 'index',
			'action' => 'add'
		)));
		// populate
		$item = (array) $item;
		$form->populate($item);
		return $form;
	}

	public function cartTable()
	{
		$cartTable = $this->cartModel->getForm('cartTable');
		$cartTable->setAction($this->view->url(array(
			'controller' => 'index',
			'action' => 'update'
		),
		'default', true));

		$qtys = new Zend_Form_SubForm();

		foreach($this->cartModel as $item) {
			$qtys->addElement('text', (string) $item->id, array(
				'value' 		=> $item->quantity,
				'belongsTo'		=> 'quantity',
				'style'			=> 'width: 30px;',
				'decorators'	=> array(
					'ViewHelper'
				),
			));
		}

		$cartTable->addSubform($qtys, 'qtys');

		$coupon = new Zend_Form_SubForm();

		$coupon->addElement('text', 'coupon', array(
			'decorators' => array('ViewHelper')
		));

		$coupon->addElement('submit', 'applycoupon', array(
			'description' => 'You must click apply to add the coupon code',
			'label' => 'Apply Coupon',
			'decorators' => array(
				'ViewHelper',
					array('Description', array('placement' => 'append', 'tag' => 'div', 'class'=>'note')),
			)
		));

		$cartTable->addSubForm($coupon, 'coupon');

		return $cartTable;
	}

	public function checkoutTable()
	{
		$this->view->cartModel = $this->cartModel;
		return $this->view->render('index/_checkoutTable.phtml');
	}
}
