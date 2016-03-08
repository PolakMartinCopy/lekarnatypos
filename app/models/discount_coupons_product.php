<?php
class DiscountCouponsProduct extends AppModel {
	var $name = 'DiscountCouponsProduct';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'DiscountCoupon',
		'Product'
	);
}