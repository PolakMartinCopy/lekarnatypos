<?php
class CategoriesComparator extends AppModel {
	var $name = 'CategoriesComparator';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Category', 'Comparator');
	
	var $validate = array(
		'path' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte ekvivalent kategorie ve srovnávači'
			)
		)
	);
}