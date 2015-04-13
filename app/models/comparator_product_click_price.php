<?php
class ComparatorProductClickPrice extends AppModel {
	var $name = 'ComparatorProductClickPrice';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Product', 'Comparator');
	
	var $validate = array(
		'click_price' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte cenu za proklik'
			)
		)
	);

	function get_id($product_id, $comparator_id) {
		$cpcp = $this->find('first', array(
			'conditions' => array('product_id' => $product_id, 'comparator_id' => $comparator_id),
			'contain' => array(),
			'fields' => array('id')
		));
		
		if (empty($cpcp)) {
			return false;
		}
		
		return $cpcp['ComparatorProductClickPrice']['id'];
	}
}