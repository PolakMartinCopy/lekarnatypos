<?php
class DiscountCouponsProduct extends AppModel {
	var $name = 'DiscountCouponsProduct';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'DiscountCoupon',
		'Product'
	);
	
	function getProductIds($couponId) {
		$productIds = $this->find('all', array(
			'conditions' => array('DiscountCouponsProduct.discount_coupon_id' => $couponId),
			'contain' => array(),
			'fields' => array('DiscountCouponsProduct.product_id')
		));
		$productIds = Set::extract('/DiscountCouponsProduct/product_id', $productIds);
		return $productIds;
	}
}