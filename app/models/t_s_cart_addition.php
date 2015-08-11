<?php
class TSCartAddition extends AppModel {
	var $name = 'TSCartAddition';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'Product',
		'Cart'
	);
	
	var $productId = null;
	
	function myCreate() {
		if ($this->productId) {
			$cartId = $this->Cart->get_id();
			$save = array(
				'TSCartAddition' => array(
					'cart_id' => $cartId,
					'product_id' => $this->productId
				)
			);
			return $this->save($save);
		}
		return false;
	}
}