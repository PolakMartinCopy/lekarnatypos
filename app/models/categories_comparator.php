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
	
	function get_id($category_id, $comparator_id) {
		$cp = $this->find('first', array(
			'conditions' => array('category_id' => $category_id, 'comparator_id' => $comparator_id),
			'contain' => array(),
			'fields' => array('id')
		));
	
		if (empty($cp)) {
			return false;
		}
	
		return $cp['CategoriesComparator']['id'];
	}
}