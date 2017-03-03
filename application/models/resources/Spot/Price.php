<?php
/**
* 
*/
class Resource_Spot_Price extends My_Model_Resource_Db_Table_Abstract
{
	protected $_name = 'spot_price';
	protected $_referenceMap = array(
		'Spot' => array(
			'columns' => array('spot'),
			'refTableClass' => 'Resource_Spot',
			'refColumns' => array('id')
		),
	);
	protected $_rowClass = "Resource_Spot_Price_Item";
}
