<?php
class Comparator extends AppModel {
	var $name = 'Comparator';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array(
		'ComparatorProductClickPrice' => array(
			'dependent' => true
		)
	);
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název srovnávače'
			)
		)
	);
}