<?php
/**
*
*/
class Form_Coupon extends My_Form_Abstract
{

	public function init()
	{
		parent::init();

		$this->addElement('hidden', 'id', array(
			'decorators' => array('viewHelper')
		));

		$this->addElement('text', 'title', array(
			'label' => 'Title:',
			'required' => true
		));

		$typesParking = array(
			'un-covered' => 'Uncovered',
			'covered' => 'Covered',
			'both' => 'Both'
		);
		$this->addElement('select', 'type_parking', array(
			'label' => 'Type of Parking:',
			'required' => true,
			'multiOptions' => $typesParking
		));

		$types = array(
			'percent' => 'Percent (%)',
			'fixed' => 'Fixed ($)'
		);
		$this->addElement('select', 'type', array(
			'label' => 'Type:',
			'required' => true,
			'multiOptions' => $types
		));

		$this->addElement('text', 'value', array(
			'label' => 'Value:',
			'required' => true
		));

		$this->addElement('text', 'code', array(
			'label' => 'Code:',
			'required' => true,
			'validators' => array('CouponCode')
		));

		$this->addElement('textarea', 'description', array(
			'label' => 'Description:',
			'required' => true,
			'attribs' => array(
				'cols' => 60,
				'rows' => 6
			),
		));

		$this->addElement('text', 'expiration', array(
			'label' => 'Coupon Expiration',
			'required' => true
		));

		$this->setCustomDecorators();


		$this->addElement('submit', 'submit', array(
			'label' => 'Save Coupon',
			'decorators' => array('viewHelper')
		));

		$this->addElement('submit', 'cancel', array(
			'label' => 'Cancel',
			'decorators' => array('viewHelper')
		));

		$this->addElement('submit', 'delete', array(
			'label' => 'Delete Coupon',
			'decorators' => array('viewHelper')
		));

		$this->addDisplayGroup(
			array('submit', 'delete', 'cancel'),
			'actions',
			array(
				'decorators' => $this->setCustomDisplayGroupDecorators('button')
			)
		);
	}

	public function addDeleteButton()
	{
		return $this->addElement('submit', 'delete', array(
			'label' => 'Delete Coupon',
			'decorators' => array('viewHelper')
		));
	}
}
