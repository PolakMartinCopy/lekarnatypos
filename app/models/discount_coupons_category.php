<?php
class DiscountCouponsCategory extends AppModel {
	var $name = 'DiscountCouponsCategory';

	var $actsAs = array('Containable');

	var $belongsTo = array(
			'DiscountCoupon',
			'Category'
	);
}