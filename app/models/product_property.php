<?php
class ProductProperty extends AppModel {
	var $name = 'ProductProperty';
	
	var $actsAs = array(
		'Containable',
		'Ordered' => array(
			'field' => 'order',
			'foreign_key' => false
		)
	);
	
	var $hasMany = array (
		'ProductPropertiesProduct' => array(
			'dependent' => true
		)
	);

	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název vlastnosti produktu'
			)
		)
	);
}
