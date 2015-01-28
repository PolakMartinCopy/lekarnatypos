<?php
class SupplierCategory extends AppModel {
	var $name = 'SupplierCategory';
	
	var $actsAs = array('Containable');

	var $belongsTo = array(
		'Supplier',
		'Category'
	);
	
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'Zadejte název kategorie'
		)	
	);
}