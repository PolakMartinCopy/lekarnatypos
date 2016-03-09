<?php
class DiscountCouponsCategory extends AppModel {
	var $name = 'DiscountCouponsCategory';

	var $actsAs = array('Containable');

	var $belongsTo = array(
		'DiscountCoupon',
		'Category'
	);
	
	function getProductIds($couponId) {
		$productIds = $this->find('all', array(
			'conditions' => array('DiscountCouponsCategory.discount_coupon_id' => $couponId),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'categories_products',
					'alias' => 'CategoriesProduct',
					'type' => 'INNER',
					'conditions' => array('DiscountCouponsCategory.category_id = CategoriesProduct.category_id')
				)
			),
			'fields' => array('CategoriesProduct.product_id')
		));
		$productIds = Set::extract('/CategoriesProduct/product_id', $productIds);
		return $productIds;
	}
}