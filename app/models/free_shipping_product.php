<?php 
class FreeShippingProduct extends AppModel {
	var $name = 'FreeShippingProduct';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Product', 'Shipping');
	
	var $validate = array(
		'quantity' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Není zadáno množství produktu'
			)
		)
	);
	
	function getQuantity($productId, $shippingId) {
		$item = $this->getByProductShipping($productId, $shippingId);
		
		if (empty($item)) {
			return false;
		}
		return $item['FreeShippingProduct']['quantity'];
	}
	
	function getByProductShipping($productId, $shippingId) {
		$item = $this->find('first', array(
			'conditions' => array('product_id' => $productId, 'shipping_id' => $shippingId),
			'contain' => array(),
		));
		
		return $item;
	}
	
	function getByProductId($productId) {
		$item = $this->find('first', array(
			'conditions' => array('product_id' => $productId),
			'contain' => array(),
		));
		
		return $item;
	}
}
?>