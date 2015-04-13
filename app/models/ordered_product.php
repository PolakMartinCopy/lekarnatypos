<?php
class OrderedProduct extends AppModel {
	var $name = 'OrderedProduct';

	var $actsAs = array('Containable');

	var $belongsTo = array('Order', 'Product');

	var $hasMany = array(
		'OrderedProductsAttribute' => array(
			'dependent' => true
		)
	);
	
	function generate_product_name($product_id) {
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $product_id),
			'contain' => array('Manufacturer'),
			'fields' => array('Product.id', 'Product.name', 'Manufacturer.name')	
		));
		
		$product_name = null;
		if (!empty($product)) {
			$product_name = $product['Product']['name'] . ', ' . $product['Product']['id'] . ' (' . $product['Manufacturer']['name'] . ')';
		}
		return $product_name;
	}
}
?>